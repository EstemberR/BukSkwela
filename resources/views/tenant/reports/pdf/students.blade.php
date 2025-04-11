<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>BukSU - Student Enrollment Report</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0 auto;
            padding: 0;
            background-color: #fff;
            width: 90%;
            max-width: 1000px;
        }
        
        /* BukSU Theme Colors */
        :root {
            --buksu-navy: #003366;
            --buksu-gold: #DAA520;
            --buksu-light-gold: #F0E68C;
            --buksu-accent: #1cc88a;
        }
        
        /* Header Styles */
        .header {
            background-color: var(--buksu-navy);
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            position: relative;
            border-bottom: 3px solid var(--buksu-gold);
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        
        .logo-container {
            position: absolute;
            top: 12px;
            left: 15px;
        }
        
        .logo {
            max-width: 60px;
        }
        
        .header-content {
            margin: 0 auto;
            width: 70%;
        }
        
        h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .school-name {
            font-size: 12px;
            margin-top: 3px;
            opacity: 0.9;
        }
        
        .date {
            font-size: 10px;
            margin-top: 3px;
            opacity: 0.8;
        }
        
        /* Section Headings */
        h2 {
            font-size: 14px;
            color: var(--buksu-navy);
            border-bottom: 2px solid var(--buksu-gold);
            padding-bottom: 3px;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }
        
        h3 {
            font-size: 12px;
            color: var(--buksu-navy);
            margin: 10px 0 8px 0;
            text-align: center;
        }
        
        p {
            font-size: 9px;
            margin: 5px 0 10px 0;
            text-align: center;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Section Container */
        .section-container {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 15px auto;
            font-size: 9px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            background-color: white;
        }
        
        th {
            background-color: var(--buksu-navy);
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            border: 1px solid #ddd;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 5px 6px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        td.label-cell {
            text-align: left;
            font-weight: bold;
            color: var(--buksu-navy);
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f0f0f0;
        }
        
        .value-cell {
            text-align: right;
            font-weight: bold;
        }
        
        /* KPI Cards Row */
        .kpi-container {
            display: flex;
            justify-content: center;
            margin: 0 auto 15px auto;
            gap: 10px;
            max-width: 95%;
        }
        
        .kpi-card {
            width: 22%;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .kpi-primary {
            background: linear-gradient(to right, var(--buksu-navy), #1e50a2);
            color: white;
        }
        
        .kpi-success {
            background: linear-gradient(to right, var(--buksu-gold), #eeba37);
            color: var(--buksu-navy);
        }
        
        .kpi-info {
            background: linear-gradient(to right, var(--buksu-navy), #004a94);
            color: white;
        }
        
        .kpi-warning {
            background: linear-gradient(to right, var(--buksu-gold), #c99516);
            color: var(--buksu-navy);
        }
        
        .kpi-title {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }
        
        /* 3-Column Grid Layout */
        .main-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin: 0 auto 20px auto;
            max-width: 98%;
        }
        
        .grid-item {
            width: 31%;
            background-color: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .grid-item-header {
            background-color: #f8f9fc;
            border-bottom: 2px solid var(--buksu-gold);
            padding: 8px 10px;
            text-align: center;
        }
        
        .grid-item-title {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            color: var(--buksu-navy);
        }
        
        .grid-item-body {
            padding: 10px;
        }
        
        /* Color indicators and badges */
        .color-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 4px;
            border-radius: 2px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 20px;
            text-transform: uppercase;
            color: white;
        }
        
        .badge-active {
            background-color: #1cc88a;
        }
        
        .badge-inactive {
            background-color: #e74a3b;
        }
        
        .badge-suspended {
            background-color: var(--buksu-gold);
        }
        
        .badge-graduated {
            background-color: var(--buksu-navy);
        }
        
        /* Table Notes */
        .table-note {
            font-size: 8px;
            font-style: italic;
            color: #666;
            margin: -10px 0 10px 0;
            text-align: center;
        }
        
        /* Page Utilities */
        .page-break {
            page-break-after: always;
            clear: both;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
            font-style: italic;
            background-color: #f8f9fc;
            padding: 10px;
            border-radius: 0 0 5px 5px;
        }
        
        /* Data Tables */
        .data-table {
            font-size: 8px;
            margin-bottom: 10px;
        }
        
        .data-table th {
            font-size: 7px;
            padding: 5px;
        }
        
        .data-table td {
            padding: 4px 5px;
        }
        
        /* Two-column layout */
        .two-column {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            max-width: 95%;
            margin-left: auto;
            margin-right: auto;
        }
        
        .column {
            width: 48%;
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Cover Page Header -->
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <div class="header-content">
            <h1>Student Enrollment Report</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
            <div class="date">Report generated on: {{ date('F d, Y') }}</div>
        </div>
    </div>
    
    <!-- Executive Summary & Key Metrics -->
    <div class="section-container">
        <h2>Report Overview</h2>
        <p>
            This report provides a comprehensive analysis of student enrollment data at Bukidnon State University.
            It includes detailed statistics on student distribution across courses, enrollment trends, and status breakdowns.
            The data presented covers all currently registered students within the system as of {{ date('F d, Y') }}.
        </p>
        
        <!-- Pre-calculate some values -->
        @php
            $totalStudents = $students->count();
            $activeStudents = $students->where('status', 'active')->count();
            $coursesCount = $students->groupBy('course_id')->count();
            $activeRate = round($activeStudents / max(1, $totalStudents) * 100);
        @endphp
        
        <h2>Key Metrics</h2>
        <div class="kpi-container">
            <div class="kpi-card kpi-primary">
                <div class="kpi-title">Total Students</div>
                <div class="kpi-value">{{ $totalStudents }}</div>
            </div>
            <div class="kpi-card kpi-success">
                <div class="kpi-title">Active Students</div>
                <div class="kpi-value">{{ $activeStudents }}</div>
        </div>
            <div class="kpi-card kpi-info">
                <div class="kpi-title">Courses</div>
                <div class="kpi-value">{{ $coursesCount }}</div>
        </div>
            <div class="kpi-card kpi-warning">
                <div class="kpi-title">Active Rate</div>
                <div class="kpi-value">{{ $activeRate }}%</div>
        </div>
        </div>
    </div>

    <!-- Course Distribution Analysis -->
            @php
        // Prepare course distribution data
        $courseData = $students->groupBy(function($student) {
                    return $student->course ? $student->course->name : 'No Course';
        })->sortByDesc(function($students) {
            return $students->count();
        });
        
        // BukSU theme colors for visualization
        $colorPalette = [
            '#003366', // Navy blue
            '#DAA520', // Gold
            '#004b8d', // Darker navy
            '#F0E68C', // Light gold
            '#1e50a2', // Medium navy
            '#eeba37', // Medium gold
            '#004080'  // Another navy shade
        ];
        
        // Status colors in BukSU theme
        $statusColors = [
            'active' => '#1cc88a',
            'inactive' => '#e74a3b',
            'suspended' => '#DAA520',
            'graduated' => '#003366',
            'withdrawn' => '#6f42c1'
        ];
        
        // Prepare status distribution data
        $statusCounts = [
            'active' => $students->where('status', 'active')->count(),
            'inactive' => $students->where('status', 'inactive')->count(),
            'suspended' => $students->where('status', 'suspended')->count(),
            'graduated' => $students->where('status', 'graduated')->count(),
            'withdrawn' => $students->where('status', 'withdrawn')->count()
        ];
        
        // Remove statuses with 0 count
        $statusCounts = array_filter($statusCounts);
        
        // Group and sort enrollment by month
        $enrollmentByMonth = $students->groupBy('enrollment_month');
        
        // Sort months chronologically
        $sortedEnrollment = $enrollmentByMonth->sortBy(function($students, $month) {
            return $month == 'Unknown' ? PHP_INT_MAX : strtotime(str_replace(' ', ' 1, ', $month));
        });
        
        // Take the last 6 months or all if less
        $recentMonths = $sortedEnrollment->count() > 6 ? $sortedEnrollment->take(-6) : $sortedEnrollment;
        
        // Get top 5 courses
        $topCourses = $courseData->take(5);
                $totalTopCourses = $topCourses->sum(function($students) {
                    return $students->count();
                });
                
        // Calculate course size statistics
        $coursesWithStudents = $courseData->count();
        $avgStudentsPerCourse = $coursesWithStudents > 0 ? round($totalStudents / $coursesWithStudents, 1) : 0;
        
        // Group courses by size ranges
        $sizeRanges = [
            '1-5 students' => 0,
            '6-10 students' => 0,
            '11-20 students' => 0,
            '21-30 students' => 0,
            '31+ students' => 0
        ];
        
        foreach ($courseData as $courseStudents) {
            $count = $courseStudents->count();
            if ($count <= 5) {
                $sizeRanges['1-5 students']++;
            } elseif ($count <= 10) {
                $sizeRanges['6-10 students']++;
            } elseif ($count <= 20) {
                $sizeRanges['11-20 students']++;
            } elseif ($count <= 30) {
                $sizeRanges['21-30 students']++;
            } else {
                $sizeRanges['31+ students']++;
            }
        }
        
        // Remove empty ranges
        $sizeRanges = array_filter($sizeRanges);
            @endphp
            
    <div class="section-container">
        <h2>Enrollment Distribution</h2>
        
        <!-- 3-column grid layout for main charts -->
        <div class="main-grid">
            <!-- Top Courses Chart -->
            <div class="grid-item">
                <div class="grid-item-header">
                    <h3 class="grid-item-title">Top 5 Courses</h3>
                </div>
                <div class="grid-item-body">
                    <table class="data-table">
                <thead>
                    <tr>
                                <th width="50%">Course</th>
                                <th width="25%">Students</th>
                                <th width="25%">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCourses as $course => $students)
                    <tr>
                                <td class="label-cell">
                                    <span class="color-indicator" style="background-color: {{ $colorPalette[$loop->index % count($colorPalette)] }}"></span>
                                    {{ strlen($course) > 20 ? substr($course, 0, 18) . '...' : $course }}
                        </td>
                        <td class="value-cell">{{ $students->count() }}</td>
                                <td class="value-cell">{{ round(($students->count() / $totalStudents) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            </div>
            
            <!-- Student Status Chart -->
            <div class="grid-item">
                <div class="grid-item-header">
                    <h3 class="grid-item-title">Student Status</h3>
                </div>
                <div class="grid-item-body">
                    <table class="data-table">
                <thead>
                    <tr>
                                <th width="40%">Status</th>
                                <th width="30%">Count</th>
                                <th width="30%">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statusCounts as $status => $count)
                    <tr>
                                <td class="label-cell">
                                    <span class="color-indicator" style="background-color: {{ $statusColors[$status] ?? '#858796' }}"></span>
                                    <span class="badge badge-{{ $status }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td class="value-cell">{{ $count }}</td>
                                <td class="value-cell">{{ round(($count / $totalStudents) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
            <!-- Enrollment Trend -->
            <div class="grid-item">
                <div class="grid-item-header">
                    <h3 class="grid-item-title">Recent Enrollments</h3>
                </div>
                <div class="grid-item-body">
                    <table class="data-table">
                <thead>
                    <tr>
                                <th width="50%">Month</th>
                                <th width="50%">New Students</th>
                    </tr>
                </thead>
                <tbody>
                            @foreach($recentMonths as $month => $students)
                    <tr>
                                <td class="label-cell">{{ $month }}</td>
                        <td class="value-cell">{{ $students->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            </div>
            
            <!-- Class Size Distribution -->
            <div class="grid-item">
                <div class="grid-item-header">
                    <h3 class="grid-item-title">Course Size Distribution</h3>
                </div>
                <div class="grid-item-body">
                    <table class="data-table">
                <thead>
                    <tr>
                                <th width="60%">Class Size</th>
                                <th width="40%">Courses</th>
                    </tr>
                </thead>
                <tbody>
                            @foreach($sizeRanges as $range => $count)
                    <tr>
                                <td class="label-cell">
                                    <span class="color-indicator" style="background-color: {{ $colorPalette[$loop->index % count($colorPalette)] }}"></span>
                            {{ $range }}
                        </td>
                        <td class="value-cell">{{ $count }}</td>
                    </tr>
                    @endforeach
                </tbody>
                    </table>
                    <div class="table-note">Average: {{ $avgStudentsPerCourse }} students per course</div>
                </div>
            </div>
            
            <!-- Course Analysis Summary -->
            <div class="grid-item">
                <div class="grid-item-header">
                    <h3 class="grid-item-title">Course Analysis</h3>
                </div>
                <div class="grid-item-body">
                    <table class="data-table">
                        <tr>
                            <td class="label-cell"><strong>Total Courses:</strong></td>
                            <td class="value-cell">{{ $courseData->count() }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Fully Active Courses:</strong></td>
                            <td class="value-cell">{{ $courseData->filter(function($students) { 
                                return $students->count() > 0 && $students->where('status', 'active')->count() == $students->count();
                            })->count() }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Courses with 90%+ Active:</strong></td>
                            <td class="value-cell">{{ $courseData->filter(function($students) { 
                                return $students->count() > 0 && 
                                    ($students->where('status', 'active')->count() / $students->count()) >= 0.9;
                            })->count() }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Largest Course:</strong></td>
                            <td class="value-cell">{{ $courseData->first()->count() }} students</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Highest Active %:</strong></td>
                            <td class="value-cell">
                                @php
                                    $highestActive = 0;
                                    foreach ($courseData as $courseName => $courseStudents) {
                                        if ($courseStudents->count() >= 5) {
                                            $activePercent = $courseStudents->where('status', 'active')->count() / $courseStudents->count();
                                            $highestActive = max($highestActive, $activePercent);
                                        }
                                    }
                                    echo round($highestActive * 100);
                                @endphp%
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Student Summary -->
            <div class="grid-item">
                <div class="grid-item-header">
                    <h3 class="grid-item-title">Student Summary</h3>
                </div>
                <div class="grid-item-body">
                    <table class="data-table">
                        <tr>
                            <td class="label-cell"><strong>Total Students:</strong></td>
                            <td class="value-cell">{{ $totalStudents }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Active/Inactive Ratio:</strong></td>
                            <td class="value-cell">{{ $activeStudents }}:{{ $totalStudents - $activeStudents }}</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Top Course Enrollment:</strong></td>
                            <td class="value-cell">
                                {{ round(($totalTopCourses / $totalStudents) * 100) }}% of total
                        </td>
                    </tr>
                        <tr>
                            <td class="label-cell"><strong>Average per Course:</strong></td>
                            <td class="value-cell">{{ $avgStudentsPerCourse }} students</td>
                        </tr>
                        <tr>
                            <td class="label-cell"><strong>Active Rate:</strong></td>
                            <td class="value-cell">{{ $activeRate }}%</td>
                        </tr>
            </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="page-break"></div>
    
    <!-- Second Page Header -->
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('assets/images/logo.png') }}" alt="BukSU Logo" class="logo">
        </div>
        <div class="header-content">
            <h1>Detailed Enrollment Data</h1>
        <div class="school-name">Bukidnon State University School Management System</div>
            <div class="date">Report generated on: {{ date('F d, Y') }}</div>
        </div>
    </div>
    
    <!-- Two-column layout for detailed data -->
    <div class="two-column">
        <!-- Monthly Enrollment Table -->
        <div class="column">
            <h2>Monthly Enrollment Trend</h2>
            <p>Distribution of student enrollments by month, highlighting seasonal patterns in admission.</p>
            
    <table>
        <thead>
            <tr>
                        <th width="40%">Month</th>
                        <th width="30%">Enrollments</th>
                        <th width="30%">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollmentByMonth as $month => $monthStudents)
            <tr>
                        <td class="label-cell">{{ $month }}</td>
                        <td class="value-cell">{{ $monthStudents->count() }}</td>
                        <td class="value-cell">{{ round(($monthStudents->count() / $totalStudents) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
        </div>
        
        <!-- Course Analysis Table (Top 10) -->
        <div class="column">
            <h2>Top Courses Analysis</h2>
            <p>Comprehensive breakdown of the top 10 courses with the highest enrollment.</p>
            
            <table>
                <thead>
                    <tr>
                        <th width="40%">Course</th>
                        <th width="20%">Total</th>
                        <th width="20%">Active</th>
                        <th width="20%">Active %</th>
                    </tr>
                </thead>
                <tbody>
                    @php $courseCount = 0; @endphp
                    @foreach($courseData as $courseName => $courseStudents)
                        @if($courseCount < 10)
                        <tr>
                            <td class="label-cell">{{ $courseName }}</td>
                            <td class="value-cell">{{ $courseStudents->count() }}</td>
                            <td class="value-cell">{{ $courseStudents->where('status', 'active')->count() }}</td>
                            <td class="value-cell">{{ round(($courseStudents->where('status', 'active')->count() / $courseStudents->count()) * 100, 1) }}%</td>
                        </tr>
                        @php $courseCount++; @endphp
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Complete Course Enrollment -->
    <div class="section-container">
        <h2>Complete Course Enrollment Data</h2>
        <p>This table provides a comprehensive breakdown of student distribution across all courses offered at BukSU.</p>
        
    <table>
        <thead>
            <tr>
                    <th width="50%">Course Name</th>
                    <th width="16%">Total Students</th>
                    <th width="16%">Active Students</th>
                    <th width="18%">Active Rate</th>
            </tr>
        </thead>
        <tbody>
                @foreach($courseData as $courseName => $courseStudents)
                <tr>
                    <td class="label-cell">{{ $courseName }}</td>
                    <td class="value-cell">{{ $courseStudents->count() }}</td>
                    <td class="value-cell">{{ $courseStudents->where('status', 'active')->count() }}</td>
                    <td class="value-cell">{{ round(($courseStudents->where('status', 'active')->count() / $courseStudents->count()) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>© {{ date('Y') }} BukSkwela - Bukidnon State University School Management System. All rights reserved.</p>
        <p>This report is system-generated for administrative purposes only. No unauthorized distribution.</p>
    </div>
</body>
</html> 