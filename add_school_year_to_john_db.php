<?php

// Basic configuration
$host = '127.0.0.1';
$username = 'root';  // Default username, change if necessary
$password = '';     // Default empty password, change if necessary
$database = 'tenant_john';

try {
    // Create connection
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to $database\n";
    
    // Check if columns already exist
    $stmt = $conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_SCHEMA = :dbname 
                           AND TABLE_NAME = 'courses' 
                           AND COLUMN_NAME = 'school_year_start'");
    $stmt->bindParam(':dbname', $database);
    $stmt->execute();
    $columnExists = (int)$stmt->fetchColumn();
    
    if ($columnExists === 0) {
        // Add school_year_start column
        $conn->exec("ALTER TABLE courses ADD COLUMN school_year_start YEAR NULL");
        echo "Added school_year_start column to courses table\n";
    } else {
        echo "school_year_start column already exists\n";
    }
    
    // Check for school_year_end
    $stmt = $conn->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_SCHEMA = :dbname 
                           AND TABLE_NAME = 'courses' 
                           AND COLUMN_NAME = 'school_year_end'");
    $stmt->bindParam(':dbname', $database);
    $stmt->execute();
    $columnExists = (int)$stmt->fetchColumn();
    
    if ($columnExists === 0) {
        // Add school_year_end column
        $conn->exec("ALTER TABLE courses ADD COLUMN school_year_end YEAR NULL");
        echo "Added school_year_end column to courses table\n";
    } else {
        echo "school_year_end column already exists\n";
    }
    
    // Show the table structure
    $stmt = $conn->prepare("DESCRIBE courses");
    $stmt->execute();
    $tableStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCurrent structure of courses table:\n";
    echo "--------------------------------\n";
    echo "Field | Type | Null | Key | Default\n";
    echo "--------------------------------\n";
    
    foreach ($tableStructure as $column) {
        echo "{$column['Field']} | {$column['Type']} | {$column['Null']} | {$column['Key']} | {$column['Default']}\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 