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
            float: left;
            margin-right: 2%;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .chart-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #003366;
            font-size: 14px;
            text-align: center;
        }
        .charts-row {
            width: 100%;
            overflow: hidden;
            margin-bottom: 20px;
            clear: both;
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
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        /* Tables for charts as fallback */
        .chart-table {
            width: 100%;
            margin-top: 10px;
            font-size: 10px;
        }
        .chart-table th {
            background-color: #003366;
            color: white;
            padding: 5px;
        }
        .chart-table td {
            padding: 5px;
        }
        .chart-table .value-cell {
            text-align: right;
            font-weight: bold;
        }
        .color-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
            border-radius: 2px;
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
    <div class="charts-row clearfix">
        <div class="chart-box">
            <div class="chart-title">Courses by Student Count</div>
            @php
                // Group courses by student count ranges
                $studentRanges = [
                    '0 students' => $courses->filter(function($course) { return $course->students->count() == 0; })->count(),
                    '1-5 students' => $courses->filter(function($course) { return $course->students->count() >= 1 && $course->students->count() <= 5; })->count(),
                    '6-10 students' => $courses->filter(function($course) { return $course->students->count() >= 6 && $course->students->count() <= 10; })->count(),
                    '11-20 students' => $courses->filter(function($course) { return $course->students->count() >= 11 && $course->students->count() <= 20; })->count(),
                    '21+ students' => $courses->filter(function($course) { return $course->students->count() > 20; })->count(),
                ];
                
                // Remove any range with 0 count
                $studentRanges = array_filter($studentRanges);
                
                // Colors for the chart
                $colors = ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b', '#36b9cc'];
                $total = array_sum($studentRanges);
            @endphp
            
            <!-- Fallback table for chart -->
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Student Range</th>
                        <th>Number of Courses</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 0; @endphp
                    @foreach($studentRanges as $range => $count)
                    <tr>
                        <td>
                            <span class="color-indicator" style="background-color: {{ $colors[$i % count($colors)] }}"></span>
                            {{ $range }}
                        </td>
                        <td class="value-cell">{{ $count }}</td>
                        <td class="value-cell">{{ round(($count / $total) * 100, 1) }}%</td>
                    </tr>
                    @php $i++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="chart-box">
            <div class="chart-title">Course Status Distribution</div>
            @php
                // Calculate status distribution for pie chart
                $statusCounts = [
                    'active' => $courses->where('status', 'active')->count(),
                    'inactive' => $courses->where('status', 'inactive')->count(),
                    'upcoming' => $courses->where('status', 'upcoming')->count(),
                    'completed' => $courses->where('status', 'completed')->count(),
                    'archived' => $courses->where('status', 'archived')->count()
                ];
                
                // Remove any status with 0 count
                $statusCounts = array_filter($statusCounts);
                $totalCourses = $courses->count();
            @endphp
            
            <!-- Fallback table for chart -->
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Courses</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 0; @endphp
                    @foreach($statusCounts as $status => $count)
                    <tr>
                        <td>
                            <span class="color-indicator" style="background-color: {{ $colors[$i % count($colors)] }}"></span>
                            {{ ucfirst($status) }}
                        </td>
                        <td class="value-cell">{{ $count }}</td>
                        <td class="value-cell">{{ round(($count / $totalCourses) * 100, 1) }}%</td>
                    </tr>
                    @php $i++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="charts-row clearfix">
        <div class="chart-box">
            <div class="chart-title">Students per Course</div>
            @php
                // Calculate the average, min, and max students per course
                $coursesWithStudents = $courses->filter(function($course) { return $course->students->count() > 0; });
                $avgStudentsPerCourse = $coursesWithStudents->count() > 0 
                    ? round($coursesWithStudents->sum(function($course) { return $course->students->count(); }) / $coursesWithStudents->count(), 1) 
                    : 0;
                $maxStudentsPerCourse = $courses->max(function($course) { return $course->students->count(); });
                $minStudentsPerCourse = $coursesWithStudents->count() > 0 
                    ? $coursesWithStudents->min(function($course) { return $course->students->count(); }) 
                    : 0;
            @endphp
            
            <!-- Simple stats table -->
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Courses</td>
                        <td class="value-cell">{{ $courses->count() }}</td>
                    </tr>
                    <tr>
                        <td>Courses with Students</td>
                        <td class="value-cell">{{ $coursesWithStudents->count() }}</td>
                    </tr>
                    <tr>
                        <td>Empty Courses</td>
                        <td class="value-cell">{{ $courses->count() - $coursesWithStudents->count() }}</td>
                    </tr>
                    <tr>
                        <td>Average Students per Course</td>
                        <td class="value-cell">{{ $avgStudentsPerCourse }}</td>
                    </tr>
                    <tr>
                        <td>Maximum Students in a Course</td>
                        <td class="value-cell">{{ $maxStudentsPerCourse }}</td>
                    </tr>
                    <tr>
                        <td>Minimum Students in a Course (with students)</td>
                        <td class="value-cell">{{ $minStudentsPerCourse }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="chart-box">
            <div class="chart-title">Top 5 Courses by Enrollment</div>
            @php
                // Get top 5 courses by student count
                $topCourses = $courses->sortByDesc(function($course) {
                    return $course->students->count();
                })->take(5);
            @endphp
            
            <!-- Top courses table -->
            <table class="chart-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Code</th>
                        <th>Students</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCourses as $course)
                    <tr>
                        <td>{{ strlen($course->name) > 30 ? substr($course->name, 0, 27) . '...' : $course->name }}</td>
                        <td><span class="course-code">{{ $course->code }}</span></td>
                        <td class="value-cell">{{ $course->students->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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