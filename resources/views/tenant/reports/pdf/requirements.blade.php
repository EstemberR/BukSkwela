<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Requirements Reports</title>
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
        .percent {
            font-size: 14px;
            font-weight: bold;
        }
        .item-count {
            font-size: 12px;
            color: #666;
        }
        .completion-bar {
            height: 15px;
            background-color: #f2f2f2;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .completion-fill {
            height: 100%;
            background-color: #4e73df;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>REQUIREMENTS REPORT</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-title">TOTAL REQUIREMENTS</div>
            <div class="summary-value">{{ $requirements->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">MANDATORY</div>
            <div class="summary-value">{{ $requirements->where('is_mandatory', true)->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">OPTIONAL</div>
            <div class="summary-value">{{ $requirements->where('is_mandatory', false)->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-title">REQUIREMENTS WITH SUBMISSIONS</div>
            <div class="summary-value">{{ $requirements->filter(function($req) { return $req->students_count > 0; })->count() }}</div>
        </div>
    </div>

    <!-- Visual Charts Section -->
    <div class="charts-row">
        <div class="chart-box">
            <div class="chart-title">Requirement Type Distribution</div>
            @php
                // Create chart data for mandatory vs optional
                $mandatoryCount = $requirements->where('is_mandatory', true)->count();
                $optionalCount = $requirements->where('is_mandatory', false)->count();
                
                // Create HTML representation of pie chart
                $typeColors = ['#4e73df', '#1cc88a'];
                $totalRequirements = $mandatoryCount + $optionalCount;
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Calculate percentages
                $mandatoryPercentage = ($mandatoryCount / $totalRequirements) * 100;
                $optionalPercentage = ($optionalCount / $totalRequirements) * 100;
                
                // Colored boxes with text
                $chartHtml .= "<rect x='50' y='80' width='40' height='20' fill='{$typeColors[0]}' />";
                $chartHtml .= "<text x='100' y='95' font-size='14'>Mandatory: $mandatoryCount (" . round($mandatoryPercentage, 1) . "%)</text>";
                
                $chartHtml .= "<rect x='50' y='110' width='40' height='20' fill='{$typeColors[1]}' />";
                $chartHtml .= "<text x='100' y='125' font-size='14'>Optional: $optionalCount (" . round($optionalPercentage, 1) . "%)</text>";
                
                // Draw simple circle for visualization
                $chartHtml .= "<circle cx='270' cy='100' r='70' fill='none' stroke='#ccc' stroke-width='1' />";
                
                // Draw slices
                $startAngle = 0;
                
                // Mandatory slice
                if ($mandatoryCount > 0) {
                    $mandatoryAngle = 360 * ($mandatoryPercentage / 100);
                    $endAngle = $startAngle + $mandatoryAngle;
                    
                    // Convert angles to radians
                    $startRad = $startAngle * M_PI / 180;
                    $endRad = $endAngle * M_PI / 180;
                    
                    // Calculate points
                    $x1 = 270 + 70 * sin($startRad);
                    $y1 = 100 - 70 * cos($startRad);
                    $x2 = 270 + 70 * sin($endRad);
                    $y2 = 100 - 70 * cos($endRad);
                    
                    // Create path
                    $largeArcFlag = $mandatoryAngle > 180 ? 1 : 0;
                    $path = "M270,100 L$x1,$y1 A70,70 0 $largeArcFlag,1 $x2,$y2 Z";
                    
                    $chartHtml .= "<path d='$path' fill='{$typeColors[0]}' />";
                    
                    $startAngle = $endAngle;
                }
                
                // Optional slice
                if ($optionalCount > 0) {
                    $optionalAngle = 360 * ($optionalPercentage / 100);
                    $endAngle = $startAngle + $optionalAngle;
                    
                    // Convert angles to radians
                    $startRad = $startAngle * M_PI / 180;
                    $endRad = $endAngle * M_PI / 180;
                    
                    // Calculate points
                    $x1 = 270 + 70 * sin($startRad);
                    $y1 = 100 - 70 * cos($startRad);
                    $x2 = 270 + 70 * sin($endRad);
                    $y2 = 100 - 70 * cos($endRad);
                    
                    // Create path
                    $largeArcFlag = $optionalAngle > 180 ? 1 : 0;
                    $path = "M270,100 L$x1,$y1 A70,70 0 $largeArcFlag,1 $x2,$y2 Z";
                    
                    $chartHtml .= "<path d='$path' fill='{$typeColors[1]}' />";
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
        <div class="chart-box">
            <div class="chart-title">Requirements Submission Rate</div>
            @php
                // Create chart data for requirements with submissions
                $withSubmissionsCount = $requirements->filter(function($req) { return $req->students_count > 0; })->count();
                $noSubmissionsCount = $requirements->filter(function($req) { return $req->students_count == 0; })->count();
                
                // Create HTML representation of bar chart
                $submissionColors = ['#36b9cc', '#e74a3b'];
                
                $chartHtml = '<div style="width:400px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="400" height="250" viewBox="0 0 400 250">';
                
                // Calculate percentages
                $withSubmissionsPercentage = $totalRequirements > 0 ? ($withSubmissionsCount / $totalRequirements) * 100 : 0;
                $noSubmissionsPercentage = $totalRequirements > 0 ? ($noSubmissionsCount / $totalRequirements) * 100 : 0;
                
                // Bar chart title
                $chartHtml .= "<text x='200' y='40' font-size='14' text-anchor='middle' font-weight='bold'>Submission Status</text>";
                
                // Bar 1: With Submissions
                $barWidth = 60;
                $barHeight1 = 150 * ($withSubmissionsPercentage / 100);
                $chartHtml .= "<rect x='100' y='" . (200 - $barHeight1) . "' width='$barWidth' height='$barHeight1' fill='{$submissionColors[0]}' rx='5' ry='5' />";
                $chartHtml .= "<text x='" . (100 + $barWidth/2) . "' y='220' font-size='12' text-anchor='middle'>With Submissions</text>";
                $chartHtml .= "<text x='" . (100 + $barWidth/2) . "' y='" . (195 - $barHeight1) . "' font-size='14' text-anchor='middle' font-weight='bold'>" . round($withSubmissionsPercentage, 1) . "%</text>";
                $chartHtml .= "<text x='" . (100 + $barWidth/2) . "' y='" . (215 - $barHeight1) . "' font-size='12' text-anchor='middle'>($withSubmissionsCount)</text>";
                
                // Bar 2: No Submissions
                $barHeight2 = 150 * ($noSubmissionsPercentage / 100);
                $chartHtml .= "<rect x='240' y='" . (200 - $barHeight2) . "' width='$barWidth' height='$barHeight2' fill='{$submissionColors[1]}' rx='5' ry='5' />";
                $chartHtml .= "<text x='" . (240 + $barWidth/2) . "' y='220' font-size='12' text-anchor='middle'>No Submissions</text>";
                $chartHtml .= "<text x='" . (240 + $barWidth/2) . "' y='" . (195 - $barHeight2) . "' font-size='14' text-anchor='middle' font-weight='bold'>" . round($noSubmissionsPercentage, 1) . "%</text>";
                $chartHtml .= "<text x='" . (240 + $barWidth/2) . "' y='" . (215 - $barHeight2) . "' font-size='12' text-anchor='middle'>($noSubmissionsCount)</text>";
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>

    <!-- Top Requirements Chart -->
    <div class="chart-container">
        <div class="chart-box" style="width: 90%;">
            <div class="chart-title">Requirements with Most Submissions</div>
            @php
                // Get top 5 requirements with most submissions
                $topRequirements = $requirements->sortByDesc('students_count')->take(5);
                
                $chartHtml = '<div style="width:700px;height:250px;margin:0 auto;position:relative;">';
                $chartHtml .= '<svg width="700" height="250" viewBox="0 0 700 250">';
                
                // Define colors
                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                
                // Draw horizontal bar chart
                $barHeight = 30;
                $maxValue = $topRequirements->max('students_count') ?: 1;
                $barWidth = 500; // max width of bars
                $yStart = 40;
                $xStart = 150;
                
                $index = 0;
                foreach($topRequirements as $requirement) {
                    $count = $requirement->students_count ?: 0;
                    $color = $colors[$index % count($colors)];
                    
                    $width = ($count / $maxValue) * $barWidth;
                    $y = $yStart + ($index * ($barHeight + 15));
                    
                    // Draw bar
                    $chartHtml .= "<rect x='$xStart' y='$y' width='$width' height='$barHeight' fill='$color' rx='3' ry='3' />";
                    
                    // Truncate requirement name if too long
                    $displayName = strlen($requirement->name) > 18 ? substr($requirement->name, 0, 18) . '...' : $requirement->name;
                    
                    // Draw requirement name
                    $chartHtml .= "<text x='" . ($xStart - 5) . "' y='" . ($y + 20) . "' font-size='12' text-anchor='end'>" . $displayName . "</text>";
                    
                    // Draw count on bar
                    $chartHtml .= "<text x='" . ($xStart + $width - 10) . "' y='" . ($y + 20) . "' font-size='12' fill='white' text-anchor='end'>$count submissions</text>";
                    
                    $index++;
                }
                
                $chartHtml .= '</svg>';
                $chartHtml .= '</div>';
                
                echo $chartHtml;
            @endphp
        </div>
    </div>
    
    <!-- Requirements List Table -->
    <h2>Requirements List</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
                <th>Students Submitted</th>
                <th>Submission Rate</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalStudents = isset($totalStudentsCount) ? $totalStudentsCount : 1;
            @endphp
            @foreach($requirements as $index => $requirement)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $requirement->name }}</td>
                <td>{{ $requirement->description ?? 'No description' }}</td>
                <td>
                    <span class="{{ $requirement->is_mandatory ? 'active' : '' }}">
                        {{ $requirement->is_mandatory ? 'Mandatory' : 'Optional' }}
                    </span>
                </td>
                <td>{{ $requirement->students_count ?? 0 }}</td>
                <td>
                    @php
                        $submissionRate = $totalStudents > 0 ? ($requirement->students_count / $totalStudents) * 100 : 0;
                    @endphp
                    <div class="percent">{{ round($submissionRate, 1) }}%</div>
                    <div class="completion-bar">
                        <div class="completion-fill" style="width: {{ $submissionRate }}%"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Requirements Analysis Section -->
    <div class="page-break"></div>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <h1>REQUIREMENTS COMPLIANCE ANALYSIS</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
        <div class="date">Generated on: {{ date('F d, Y') }}</div>
    </div>
    
    <h2>Mandatory Requirements Compliance</h2>
    @php
        $mandatoryRequirements = $requirements->where('is_mandatory', true)->sortByDesc('students_count');
    @endphp
    <table>
        <thead>
            <tr>
                <th>Requirement Name</th>
                <th>Students Submitted</th>
                <th>Submission Rate</th>
                <th>Compliance Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mandatoryRequirements as $requirement)
            @php
                $submissionRate = $totalStudents > 0 ? ($requirement->students_count / $totalStudents) * 100 : 0;
                $complianceStatus = $submissionRate >= 90 ? 'High' : ($submissionRate >= 70 ? 'Medium' : 'Low');
                $statusClass = $submissionRate >= 90 ? 'active' : ($submissionRate < 70 ? 'inactive' : '');
            @endphp
            <tr>
                <td>{{ $requirement->name }}</td>
                <td>{{ $requirement->students_count ?? 0 }} / {{ $totalStudents }}</td>
                <td>
                    <div class="percent">{{ round($submissionRate, 1) }}%</div>
                    <div class="completion-bar">
                        <div class="completion-fill" style="width: {{ $submissionRate }}%; background-color: {{ $submissionRate >= 90 ? '#1cc88a' : ($submissionRate >= 70 ? '#f6c23e' : '#e74a3b') }}"></div>
                    </div>
                </td>
                <td class="{{ $statusClass }}">{{ $complianceStatus }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Requirements with No Submissions -->
    <h2>Requirements with No Submissions</h2>
    @php
        $requirementsWithNoSubmissions = $requirements->where('students_count', 0);
    @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requirementsWithNoSubmissions as $index => $requirement)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $requirement->name }}</td>
                <td>{{ $requirement->description ?? 'No description' }}</td>
                <td>
                    <span class="{{ $requirement->is_mandatory ? 'active' : '' }}">
                        {{ $requirement->is_mandatory ? 'Mandatory' : 'Optional' }}
                    </span>
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