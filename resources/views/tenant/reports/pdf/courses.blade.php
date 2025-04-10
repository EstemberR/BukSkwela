<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Courses Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #003366; /* BukSU primary color */
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }
        .logo-container {
            position: absolute;
            top: 15px;
            left: 20px;
        }
        .logo {
            max-width: 80px;
        }
        h1 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        h2 {
            font-size: 16px;
            color: #003366; /* BukSU primary color */
            border-bottom: 2px solid #003366;
            padding-bottom: 5px;
            margin-top: 25px;
        }
        .date {
            font-size: 12px;
            margin-top: 5px;
            color: #fff;
        }
        .school-name {
            font-size: 14px;
            margin-top: 5px;
            font-weight: normal;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #003366;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary-container {
            display: flex;
            flex-wrap: wrap;
            margin: 20px 0;
            justify-content: space-between;
        }
        .summary-box {
            width: 22%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .summary-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #003366;
            font-size: 13px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #003366;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        .inactive {
            color: red;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .chart-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .chart-image {
            width: 450px;
            height: auto;
            margin: 10px auto;
            display: block;
        }
        .chart-box {
            width: 48%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .chart-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #003366;
            font-size: 14px;
            text-align: center;
        }
        .charts-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .badge {
            padding: 5px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            display: inline-block;
        }
        .badge-primary {
            background-color: #4e73df;
        }
        .badge-success {
            background-color: #1cc88a;
        }
        .badge-warning {
            background-color: #f6c23e;
        }
        .badge-danger {
            background-color: #e74a3b;
        }
        .badge-info {
            background-color: #36b9cc;
        }
        .course-code {
            font-family: monospace;
            background-color: #f5f5f5;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>COURSES REPORT</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">TOTAL COURSES</div>
            <div class="summary-value">{{ $courses->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">ACTIVE COURSES</div>
            <div class="summary-value">{{ $courses->where('status', 'active')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">TOTAL STAFF</div>
            <div class="summary-value">{{ $courses->pluck('staff_id')->unique()->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">TOTAL STUDENTS</div>
            <div class="summary-value">
                @php
                    $totalStudents = 0;
                    foreach ($courses as $course) {
                        $totalStudents += $course->students_count;
                    }
                    echo $totalStudents;
                @endphp
            </div>
        </div>
    </div>

    <!-- Visual Charts Section -->
    <div class="charts-row">
        <div class="chart-box">
            <div class="chart-title">Course Status Distribution</div>
            @php
                // Calculate status distribution for pie chart
                $statusCounts = [
                    'active' => $courses->where('status', 'active')->count(),
                    'inactive' => $courses->where('status', 'inactive')->count(),
                    'pending' => $courses->where('status', 'pending')->count()
                ];
                
                // Remove any status with 0 count
                $statusCounts = array_filter($statusCounts);
                
                // Create HTML representation of pie chart
                $colors = ['#1cc88a', '#e74a3b', '#f6c23e', '#4e73df', '#36b9cc'];
                $total = array_sum($statusCounts);
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Create legend
                $y = 90;
                $i = 0;
                foreach ($statusCounts as $status => $count) {
                    $color = $colors[$i % count($colors)];
                    $percentage = ($count / $total) * 100;
                    
                    // Draw colored square for legend
                    $chartHtml .= "<rect x='50' y='$y' width='15' height='15' fill='$color' />";
                    
                    // Display status and count
                    $chartHtml .= "<text x='75' y='" . ($y + 12) . "' font-size='12'>" . ucfirst($status) . ": $count (" . round($percentage, 1) . "%)</text>";
                    
                    $y += 25;
                    $i++;
                }
                
                // Draw circle for pie chart
                $chartHtml .= "<circle cx='290' cy='120' r='70' fill='none' stroke='#ccc' stroke-width='1' />";
                
                // Draw slices of pie
                $startAngle = 0;
                $i = 0;
                
                foreach ($statusCounts as $status => $count) {
                    $percentage = ($count / $total) * 100;
                    $angle = 360 * ($percentage / 100);
                    $endAngle = $startAngle + $angle;
                    $color = $colors[$i % count($colors)];
                    
                    // Convert angles to radians for calculation
                    $startRad = $startAngle * M_PI / 180;
                    $endRad = $endAngle * M_PI / 180;
                    
                    // Calculate points on circle
                    $x1 = 290 + 70 * sin($startRad);
                    $y1 = 120 - 70 * cos($startRad);
                    $x2 = 290 + 70 * sin($endRad);
                    $y2 = 120 - 70 * cos($endRad);
                    
                    // Create path for slice
                    $largeArcFlag = $angle > 180 ? 1 : 0;
                    $path = "M290,120 L$x1,$y1 A70,70 0 $largeArcFlag,1 $x2,$y2 Z";
                    
                    $chartHtml .= "<path d='$path' fill='$color' />";
                    
                    $startAngle = $endAngle;
                    $i++;
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
        <div class="chart-box">
            <div class="chart-title">Top 5 Courses by Student Enrollment</div>
            @php
                // Get top 5 courses with most students
                $topCoursesByStudents = $courses->sortByDesc('students_count')->take(5);
                
                // Create HTML representation of horizontal bar chart
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Get maximum students count for scaling
                $maxStudents = $topCoursesByStudents->max('students_count');
                if ($maxStudents == 0) $maxStudents = 1; // Avoid division by zero
                
                // Draw horizontal bars
                $barHeight = 25;
                $barGap = 15;
                $chartWidth = 250;
                $startY = 50;
                
                foreach ($topCoursesByStudents as $index => $course) {
                    $color = $colors[$index % count($colors)];
                    $barWidth = ($course->students_count / $maxStudents) * $chartWidth;
                    $y = $startY + ($index * ($barHeight + $barGap));
                    
                    // Draw bar
                    $chartHtml .= "<rect x='120' y='$y' width='$barWidth' height='$barHeight' fill='$color' rx='3' ry='3' />";
                    
                    // Display course code
                    $displayCode = strlen($course->code) > 12 ? substr($course->code, 0, 12) . '...' : $course->code;
                    $chartHtml .= "<text x='115' y='" . ($y + 17) . "' font-size='12' text-anchor='end'>$displayCode</text>";
                    
                    // Display count on bar
                    $chartHtml .= "<text x='" . (125 + $barWidth - 5) . "' y='" . ($y + 17) . "' font-size='12' fill='white' text-anchor='end'>{$course->students_count}</text>";
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>

    <div class="charts-row">
        <div class="chart-box">
            <div class="chart-title">Staff with Most Courses</div>
            @php
                // Calculate courses per staff
                $staffCourses = [];
                foreach ($courses as $course) {
                    if (!$course->staff) continue;
                    
                    $staffName = $course->staff->name;
                    if (!isset($staffCourses[$staffName])) {
                        $staffCourses[$staffName] = 0;
                    }
                    $staffCourses[$staffName]++;
                }
                
                // Sort staff by course count (descending)
                arsort($staffCourses);
                
                // Take top 5 staff
                $topStaff = array_slice($staffCourses, 0, 5, true);
                
                // Create HTML representation of horizontal bar chart
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Get maximum course count for scaling
                $maxCourses = max($topStaff) ?: 1; // Avoid division by zero
                
                // Draw horizontal bars
                $barHeight = 25;
                $barGap = 15;
                $chartWidth = 250;
                $startY = 50;
                
                $i = 0;
                foreach ($topStaff as $staffName => $count) {
                    $color = $colors[($i + 2) % count($colors)]; // Different color scheme
                    $barWidth = ($count / $maxCourses) * $chartWidth;
                    $y = $startY + ($i * ($barHeight + $barGap));
                    
                    // Draw bar
                    $chartHtml .= "<rect x='120' y='$y' width='$barWidth' height='$barHeight' fill='$color' rx='3' ry='3' />";
                    
                    // Display staff name
                    $displayName = strlen($staffName) > 15 ? substr($staffName, 0, 15) . '...' : $staffName;
                    $chartHtml .= "<text x='115' y='" . ($y + 17) . "' font-size='12' text-anchor='end'>$displayName</text>";
                    
                    // Display count on bar
                    $chartHtml .= "<text x='" . (125 + $barWidth - 5) . "' y='" . ($y + 17) . "' font-size='12' fill='white' text-anchor='end'>$count</text>";
                    
                    $i++;
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
        <div class="chart-box">
            <div class="chart-title">Students per Course (Average: {{ round($courses->sum('students_count') / max($courses->count(), 1), 1) }})</div>
            @php
                // Calculate student distribution across courses
                $studentDistribution = [
                    '0' => $courses->where('students_count', 0)->count(),
                    '1-10' => $courses->where('students_count', '>', 0)->where('students_count', '<=', 10)->count(),
                    '11-20' => $courses->where('students_count', '>', 10)->where('students_count', '<=', 20)->count(),
                    '21-30' => $courses->where('students_count', '>', 20)->where('students_count', '<=', 30)->count(),
                    '31+' => $courses->where('students_count', '>', 30)->count()
                ];
                
                // Remove any ranges with 0 count
                $studentDistribution = array_filter($studentDistribution);
                
                // Create HTML representation of pie chart
                $total = array_sum($studentDistribution);
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Create legend
                $y = 50;
                $i = 0;
                foreach ($studentDistribution as $range => $count) {
                    $color = $colors[$i % count($colors)];
                    $percentage = ($count / $total) * 100;
                    
                    // Draw colored square for legend
                    $chartHtml .= "<rect x='50' y='$y' width='15' height='15' fill='$color' />";
                    
                    // Display range and count
                    $label = ($range === '0') ? 'No students' : $range . ' students';
                    $chartHtml .= "<text x='75' y='" . ($y + 12) . "' font-size='12'>$label: $count (" . round($percentage, 1) . "%)</text>";
                    
                    $y += 25;
                    $i++;
                }
                
                // Draw circle for pie chart
                $chartHtml .= "<circle cx='290' cy='120' r='70' fill='none' stroke='#ccc' stroke-width='1' />";
                
                // Draw slices of pie
                $startAngle = 0;
                $i = 0;
                
                foreach ($studentDistribution as $range => $count) {
                    $percentage = ($count / $total) * 100;
                    $angle = 360 * ($percentage / 100);
                    $endAngle = $startAngle + $angle;
                    $color = $colors[$i % count($colors)];
                    
                    // Convert angles to radians for calculation
                    $startRad = $startAngle * M_PI / 180;
                    $endRad = $endAngle * M_PI / 180;
                    
                    // Calculate points on circle
                    $x1 = 290 + 70 * sin($startRad);
                    $y1 = 120 - 70 * cos($startRad);
                    $x2 = 290 + 70 * sin($endRad);
                    $y2 = 120 - 70 * cos($endRad);
                    
                    // Create path for slice
                    $largeArcFlag = $angle > 180 ? 1 : 0;
                    $path = "M290,120 L$x1,$y1 A70,70 0 $largeArcFlag,1 $x2,$y2 Z";
                    
                    $chartHtml .= "<path d='$path' fill='$color' />";
                    
                    $startAngle = $endAngle;
                    $i++;
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>
    
    <!-- Course List Table -->
    <h2>Course List</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Staff</th>
                <th>Students</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $index => $course)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="course-code">{{ $course->code }}</span></td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->staff->name ?? 'Unassigned' }}</td>
                <td>{{ $course->students_count }}</td>
                <td>
                    @if($course->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($course->status == 'inactive')
                        <span class="badge badge-danger">Inactive</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($course->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Course Analysis -->
    <div class="page-break"></div>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>COURSE PERFORMANCE ANALYSIS</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <h2>Top 10 Courses by Student Enrollment</h2>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Code</th>
                <th>Name</th>
                <th>Staff</th>
                <th>Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses->sortByDesc('students_count')->take(10) as $index => $course)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="course-code">{{ $course->code }}</span></td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->staff->name ?? 'Unassigned' }}</td>
                <td>{{ $course->students_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Courses with No Students</h2>
    @php
        $emptyEnrollmentCourses = $courses->where('students_count', 0);
    @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Staff</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emptyEnrollmentCourses as $index => $course)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="course-code">{{ $course->code }}</span></td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->staff->name ?? 'Unassigned' }}</td>
                <td>
                    @if($course->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($course->status == 'inactive')
                        <span class="badge badge-danger">Inactive</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($course->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>© {{ date('Y') }} BukSkwela - Bukidnon State University School Management System. All rights reserved.</p>
        <p>This report is system-generated and requires no signature.</p>
    </div>
</body>
</html> 