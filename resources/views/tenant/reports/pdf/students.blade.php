<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Students Reports</title>
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
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>STUDENT REPORT</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">TOTAL STUDENTS</div>
            <div class="summary-value">{{ $students->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">ACTIVE STUDENTS</div>
            <div class="summary-value">{{ $students->where('status', 'active')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">COURSES WITH STUDENTS</div>
            <div class="summary-value">{{ $students->groupBy('course_id')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">ACTIVE RATE</div>
            <div class="summary-value">{{ round($students->where('status', 'active')->count() / max(1, $students->count()) * 100) }}%</div>
        </div>
    </div>

    <!-- Visual Charts Section -->
    <div class="charts-row">
        <div class="chart-box">
            <div class="chart-title">Students by Course</div>
            @php
                // Create chart data
                $courseChartData = $students->groupBy(function($student) {
                    return $student->course ? $student->course->name : 'No Course';
                });
                
                // Get the top 5 courses by student count
                $topCourses = $courseChartData->sortByDesc(function($students) {
                    return $students->count();
                })->take(5);
                
                // Create HTML representation of pie chart
                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                $totalStudents = $topCourses->sum(function($students) { 
                    return $students->count(); 
                });
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Draw pie chart
                $cumulativeAngle = 0;
                $index = 0;
                $legends = [];
                
                foreach($topCourses as $course => $students) {
                    $count = $students->count();
                    $percentage = ($count / $totalStudents) * 100;
                    $angle = ($percentage / 100) * 360;
                    
                    // Calculate coordinates for pie slice
                    $centerX = 120;
                    $centerY = 120;
                    $radius = 100;
                    
                    $startAngle = $cumulativeAngle * (M_PI / 180);
                    $endAngle = ($cumulativeAngle + $angle) * (M_PI / 180);
                    
                    $startX = $centerX + $radius * cos($startAngle);
                    $startY = $centerY + $radius * sin($startAngle);
                    $endX = $centerX + $radius * cos($endAngle);
                    $endY = $centerY + $radius * sin($endAngle);
                    
                    $largeArcFlag = $angle > 180 ? 1 : 0;
                    
                    $color = $colors[$index % count($colors)];
                    
                    // Create the path for the pie slice
                    $path = "M$centerX,$centerY L$startX,$startY A$radius,$radius 0 $largeArcFlag,1 $endX,$endY Z";
                    
                    $chartHtml .= "<path d='$path' fill='$color' stroke='white' stroke-width='1' />";
                    
                    // Add to legends
                    $legends[] = [
                        'color' => $color,
                        'label' => $course,
                        'value' => $count,
                        'percentage' => round($percentage, 1)
                    ];
                    
                    $cumulativeAngle += $angle;
                    $index++;
                }
                
                $chartHtml .= '</svg>';
                
                // Add legends
                $chartHtml .= '<div style="position:absolute;top:0;right:0;width:180px;">';
                foreach($legends as $legend) {
                    $chartHtml .= "<div style='margin-bottom:5px;'>";
                    $chartHtml .= "<span style='display:inline-block;width:10px;height:10px;background:{$legend['color']};margin-right:5px;'></span>";
                    $chartHtml .= "<span style='font-size:10px;'>{$legend['label']} ({$legend['value']}) - {$legend['percentage']}%</span>";
                    $chartHtml .= "</div>";
                }
                $chartHtml .= '</div>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
        <div class="chart-box">
            <div class="chart-title">Student Status Distribution</div>
            @php
                // Create chart data for status
                $activeCount = $students->where('status', 'active')->count();
                $inactiveCount = $students->where('status', 'inactive')->count() ?: $students->where('status', '!=', 'active')->count();
                
                // Create HTML representation of pie chart
                $statusColors = ['#1cc88a', '#e74a3b'];
                $totalStudents = $activeCount + $inactiveCount;
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Calculate percentages
                $activePercentage = ($activeCount / $totalStudents) * 100;
                $inactivePercentage = ($inactiveCount / $totalStudents) * 100;
                
                // Draw active slice
                $activeAngle = ($activePercentage / 100) * 360;
                $inactiveAngle = ($inactivePercentage / 100) * 360;
                
                // Simplified pie chart drawing (colored blocks with percentages)
                $chartHtml .= "<rect x='50' y='80' width='40' height='20' fill='{$statusColors[0]}' />";
                $chartHtml .= "<text x='100' y='95' font-size='14'>Active: $activeCount (" . round($activePercentage, 1) . "%)</text>";
                
                $chartHtml .= "<rect x='50' y='110' width='40' height='20' fill='{$statusColors[1]}' />";
                $chartHtml .= "<text x='100' y='125' font-size='14'>Inactive: $inactiveCount (" . round($inactivePercentage, 1) . "%)</text>";
                
                // Draw simple pie representation
                $chartHtml .= "<circle cx='250' cy='100' r='70' fill='none' stroke='#ccc' stroke-width='1' />";
                
                // Active slice (starts at 0)
                $chartHtml .= "<path d='M250,100 L250,30 A70,70 0 " . ($activeAngle > 180 ? "1,1 " : "0,1 ") . (250 + 70 * sin($activeAngle * M_PI / 180)) . "," . (100 - 70 * cos($activeAngle * M_PI / 180)) . " Z' fill='{$statusColors[0]}' />";
                
                // Inactive slice
                $chartHtml .= "<path d='M250,100 L" . (250 + 70 * sin($activeAngle * M_PI / 180)) . "," . (100 - 70 * cos($activeAngle * M_PI / 180)) . " A70,70 0 " . ($inactiveAngle > 180 ? "1,1 " : "0,1 ") . "250,30 Z' fill='{$statusColors[1]}' />";
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>

    <!-- Student List Table -->
    <h2>Student List</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Status</th>
                <th>Enrollment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student->student_id }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->course ? $student->course->name : 'N/A' }}</td>
                <td class="{{ $student->status == 'active' ? 'active' : 'inactive' }}">
                    {{ ucfirst($student->status ?? 'N/A') }}
                </td>
                <td>{{ $student->created_at ? $student->created_at->format('M d, Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Enrollment by Month Section -->
    <div class="page-break"></div>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>STUDENT ENROLLMENT ANALYSIS</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <h2>Enrollment by Month</h2>
    @php
        $enrollmentByMonth = $students->groupBy('enrollment_month');
    @endphp
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Number of Enrollments</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollmentByMonth as $month => $monthStudents)
            <tr>
                <td>{{ $month }}</td>
                <td>{{ $monthStudents->count() }}</td>
                <td>{{ round(($monthStudents->count() / $students->count()) * 100, 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Students by Course Analysis -->
    <h2>Students by Course</h2>
    @php
        $studentsByCourse = $students->groupBy(function($student) {
            return $student->course ? $student->course->name : 'No Course';
        });
    @endphp
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>Number of Students</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentsByCourse as $courseName => $courseStudents)
            <tr>
                <td>{{ $courseName }}</td>
                <td>{{ $courseStudents->count() }}</td>
                <td>{{ round(($courseStudents->count() / $students->count()) * 100, 2) }}%</td>
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