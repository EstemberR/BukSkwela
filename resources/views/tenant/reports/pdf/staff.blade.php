<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Staff Reports</title>
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
            justify-content: space-between;
            margin: 20px 0;
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
        .department-tag {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            background-color: #4e73df;
        }
        .email {
            font-family: monospace;
            font-size: 11px;
            color: #555;
        }
        .staff-id {
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
        <h1>STAFF REPORT</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">TOTAL STAFF</div>
            <div class="summary-value">{{ $staff->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">ACTIVE STAFF</div>
            <div class="summary-value">{{ $staff->where('status', 'active')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">DEPARTMENTS</div>
            <div class="summary-value">
                @php
                    $departments = $staff->pluck('department')->filter()->unique()->count();
                    echo $departments;
                @endphp
            </div>
        </div>
        <div class="summary-box">
            <div class="summary-title">AVG STUDENTS</div>
            <div class="summary-value">
                @php
                    $avgStudents = round($staff->avg('students_count'), 1);
                    echo $avgStudents;
                @endphp
            </div>
        </div>
    </div>

    <!-- Visual Charts Section -->
    <div class="chart-container">
        <div class="chart-box">
            <div class="chart-title">Staff Status Distribution</div>
            @php
                // Calculate status distribution for pie chart
                $statusCounts = [
                    'active' => $staff->where('status', 'active')->count(),
                    'inactive' => $staff->where('status', 'inactive')->count(),
                    'on leave' => $staff->where('status', 'on leave')->count(),
                    'suspended' => $staff->where('status', 'suspended')->count(),
                    'pending' => $staff->where('status', 'pending')->count()
                ];
                
                // Remove any status with 0 count
                $statusCounts = array_filter($statusCounts);
                
                // Create HTML representation of pie chart
                $colors = ['#1cc88a', '#e74a3b', '#f6c23e', '#4e73df', '#36b9cc'];
                $total = array_sum($statusCounts);
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Create legend
                $y = 50;
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
            <div class="chart-title">Staff by Department</div>
            @php
                // Group staff by department
                $staffByDepartment = [];
                
                foreach ($staff as $member) {
                    $department = $member->department ?? 'No Department';
                    
                    if (!isset($staffByDepartment[$department])) {
                        $staffByDepartment[$department] = 0;
                    }
                    
                    $staffByDepartment[$department]++;
                }
                
                // Sort by count (descending)
                arsort($staffByDepartment);
                
                // Take top departments (limit to 6 for visibility)
                $staffByDepartment = array_slice($staffByDepartment, 0, 6, true);
                
                // Create HTML representation of horizontal bar chart
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Get maximum value for scaling
                $maxValue = max($staffByDepartment) ?: 1; // Avoid division by zero
                
                // Draw horizontal bars
                $barHeight = 25;
                $barGap = 15;
                $chartWidth = 250;
                $startY = 50;
                
                $i = 0;
                foreach ($staffByDepartment as $department => $count) {
                    $color = $colors[$i % count($colors)];
                    
                    // Calculate bar width based on count
                    $barWidth = ($count / $maxValue) * $chartWidth;
                    $y = $startY + ($i * ($barHeight + $barGap));
                    
                    // Draw bar
                    $chartHtml .= "<rect x='120' y='$y' width='$barWidth' height='$barHeight' fill='$color' rx='3' ry='3' />";
                    
                    // Display department name (truncated if needed)
                    $displayName = strlen($department) > 15 ? substr($department, 0, 15) . '...' : $department;
                    $chartHtml .= "<text x='115' y='" . ($y + 17) . "' font-size='12' text-anchor='end'>$displayName</text>";
                    
                    // Display count at end of bar
                    $chartHtml .= "<text x='" . (125 + $barWidth - 5) . "' y='" . ($y + 17) . "' font-size='12' fill='white' text-anchor='end'>$count</text>";
                    
                    $i++;
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>
    
    <div class="chart-container">
        <div class="chart-box">
            <div class="chart-title">Student-to-Staff Ratio</div>
            @php
                // Group staff by student count ranges
                $studentRanges = [
                    '0' => $staff->where('students_count', 0)->count(),
                    '1-5' => $staff->where('students_count', '>=', 1)->where('students_count', '<=', 5)->count(),
                    '6-10' => $staff->where('students_count', '>=', 6)->where('students_count', '<=', 10)->count(),
                    '11-20' => $staff->where('students_count', '>=', 11)->where('students_count', '<=', 20)->count(),
                    '21+' => $staff->where('students_count', '>', 20)->count()
                ];
                
                // Remove any range with 0 count
                $studentRanges = array_filter($studentRanges);
                
                // Create HTML representation of donut chart
                $total = array_sum($studentRanges);
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Create legend
                $y = 50;
                $i = 0;
                foreach ($studentRanges as $range => $count) {
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
                
                // Draw donut chart circles
                $chartHtml .= "<circle cx='290' cy='120' r='70' fill='none' stroke='#ccc' stroke-width='1' />";
                $chartHtml .= "<circle cx='290' cy='120' r='40' fill='white' stroke='none' />";
                
                // Draw slices of donut
                $startAngle = 0;
                $i = 0;
                
                foreach ($studentRanges as $range => $count) {
                    $percentage = ($count / $total) * 100;
                    $angle = 360 * ($percentage / 100);
                    $endAngle = $startAngle + $angle;
                    $color = $colors[$i % count($colors)];
                    
                    // Convert angles to radians for calculation
                    $startRad = $startAngle * M_PI / 180;
                    $endRad = $endAngle * M_PI / 180;
                    
                    // Calculate points on outer circle
                    $x1 = 290 + 70 * sin($startRad);
                    $y1 = 120 - 70 * cos($startRad);
                    $x2 = 290 + 70 * sin($endRad);
                    $y2 = 120 - 70 * cos($endRad);
                    
                    // Calculate points on inner circle
                    $x1i = 290 + 40 * sin($startRad);
                    $y1i = 120 - 40 * cos($startRad);
                    $x2i = 290 + 40 * sin($endRad);
                    $y2i = 120 - 40 * cos($endRad);
                    
                    // Create path for slice
                    $largeArcFlag = $angle > 180 ? 1 : 0;
                    $path = "M$x1,$y1 A70,70 0 $largeArcFlag,1 $x2,$y2 L$x2i,$y2i A40,40 0 $largeArcFlag,0 $x1i,$y1i Z";
                    
                    $chartHtml .= "<path d='$path' fill='$color' />";
                    
                    $startAngle = $endAngle;
                    $i++;
                }
                
                // Display average in center
                $avgStudents = round($staff->avg('students_count'), 1);
                $chartHtml .= "<text x='290' y='115' font-size='12' text-anchor='middle'>Avg.</text>";
                $chartHtml .= "<text x='290' y='135' font-size='16' font-weight='bold' text-anchor='middle'>$avgStudents</text>";
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
        <div class="chart-box">
            <div class="chart-title">Courses Per Staff</div>
            @php
                // Group staff by course count ranges
                $courseRanges = [
                    '0' => $staff->where('courses_count', 0)->count(),
                    '1-2' => $staff->where('courses_count', '>=', 1)->where('courses_count', '<=', 2)->count(),
                    '3-5' => $staff->where('courses_count', '>=', 3)->where('courses_count', '<=', 5)->count(),
                    '6-9' => $staff->where('courses_count', '>=', 6)->where('courses_count', '<=', 9)->count(),
                    '10+' => $staff->where('courses_count', '>=', 10)->count()
                ];
                
                // Remove any range with 0 count
                $courseRanges = array_filter($courseRanges);
                
                // Create HTML representation of vertical bar chart
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Define chart dimensions
                $chartHeight = 180;
                $chartWidth = 350;
                $baseY = 220;
                $leftMargin = 40;
                
                // Draw base horizontal line
                $chartHtml .= "<line x1='$leftMargin' y1='$baseY' x2='" . ($leftMargin + $chartWidth) . "' y2='$baseY' stroke='#ccc' stroke-width='1' />";
                
                // Calculate bar width and spacing
                $totalBars = count($courseRanges);
                $barWidth = ($chartWidth / $totalBars) * 0.6;
                $barSpacing = ($chartWidth / $totalBars) * 0.4;
                
                // Get maximum value for scaling
                $maxValue = max($courseRanges) ?: 1; // Avoid division by zero
                
                // Draw bars and labels
                $i = 0;
                foreach ($courseRanges as $range => $count) {
                    $color = $colors[$i % count($colors)];
                    
                    // Calculate bar height and position
                    $barHeight = ($count / $maxValue) * $chartHeight;
                    $x = $leftMargin + ($i * ($barWidth + $barSpacing)) + ($barSpacing / 2);
                    $y = $baseY - $barHeight;
                    
                    // Draw bar
                    $chartHtml .= "<rect x='$x' y='$y' width='$barWidth' height='$barHeight' fill='$color' rx='3' ry='3' />";
                    
                    // Draw range label
                    $chartHtml .= "<text x='" . ($x + $barWidth/2) . "' y='" . ($baseY + 15) . "' font-size='10' text-anchor='middle'>$range</text>";
                    
                    // Draw count on top of bar
                    $chartHtml .= "<text x='" . ($x + $barWidth/2) . "' y='" . ($y - 5) . "' font-size='10' text-anchor='middle'>$count</text>";
                    
                    $i++;
                }
                
                // Draw vertical axis
                $chartHtml .= "<line x1='$leftMargin' y1='40' x2='$leftMargin' y2='$baseY' stroke='#ccc' stroke-width='1' />";
                
                // Draw scale on vertical axis
                $scaleStep = $maxValue / 4;
                for ($j = 0; $j <= 4; $j++) {
                    $scaleValue = round($j * $scaleStep);
                    $scaleY = $baseY - ($j * ($chartHeight / 4));
                    
                    // Draw scale line
                    $chartHtml .= "<line x1='" . ($leftMargin - 5) . "' y1='$scaleY' x2='$leftMargin' y2='$scaleY' stroke='#ccc' stroke-width='1' />";
                    
                    // Draw scale value
                    $chartHtml .= "<text x='" . ($leftMargin - 10) . "' y='" . ($scaleY + 4) . "' font-size='10' text-anchor='end'>$scaleValue</text>";
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>
    
    <!-- Staff List Table -->
    <h2>Staff List</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Courses</th>
                <th>Students</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staff as $index => $member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="staff-id">{{ $member->staff_id }}</span></td>
                <td>{{ $member->name }}</td>
                <td><span class="email">{{ $member->email }}</span></td>
                <td>
                    @if($member->department)
                        <span class="department-tag" style="background-color: {{ '#' . substr(md5($member->department), 0, 6) }}">
                            {{ $member->department }}
                        </span>
                    @else
                        <span class="department-tag" style="background-color: #999999">No Department</span>
                    @endif
                </td>
                <td>{{ $member->courses_count }}</td>
                <td>{{ $member->students_count }}</td>
                <td>
                    @if($member->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($member->status == 'inactive')
                        <span class="badge badge-danger">Inactive</span>
                    @elseif($member->status == 'on leave')
                        <span class="badge badge-warning">On Leave</span>
                    @elseif($member->status == 'suspended')
                        <span class="badge badge-danger">Suspended</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($member->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Top Staff Analysis -->
    <div class="page-break"></div>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>TOP STAFF ANALYSIS</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <h2>Top Staff by Student Count</h2>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Students</th>
                <th>Courses</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staff->sortByDesc('students_count')->take(10) as $index => $member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="staff-id">{{ $member->staff_id }}</span></td>
                <td>{{ $member->name }}</td>
                <td>
                    @if($member->department)
                        <span class="department-tag" style="background-color: {{ '#' . substr(md5($member->department), 0, 6) }}">
                            {{ $member->department }}
                        </span>
                    @else
                        <span class="department-tag" style="background-color: #999999">No Department</span>
                    @endif
                </td>
                <td>{{ $member->students_count }}</td>
                <td>{{ $member->courses_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h2>Top Staff by Course Count</h2>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Courses</th>
                <th>Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staff->sortByDesc('courses_count')->take(10) as $index => $member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="staff-id">{{ $member->staff_id }}</span></td>
                <td>{{ $member->name }}</td>
                <td>
                    @if($member->department)
                        <span class="department-tag" style="background-color: {{ '#' . substr(md5($member->department), 0, 6) }}">
                            {{ $member->department }}
                        </span>
                    @else
                        <span class="department-tag" style="background-color: #999999">No Department</span>
                    @endif
                </td>
                <td>{{ $member->courses_count }}</td>
                <td>{{ $member->students_count }}</td>
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