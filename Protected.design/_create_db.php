<?php

//Connect to Mysql
include_once "includes/_auth.php";

// sql to create 'protected_designs' table

$sql = "CREATE TABLE `protected_designs` (
 `ID` INT(10) NOT NULL AUTO_INCREMENT,
 `timestamp` VARCHAR(64) NOT NULL,
 `status` VARCHAR(32) NOT NULL,
 `paid` INT(1) NOT NULL,
 `hash` VARCHAR(64) NOT NULL UNIQUE,
 `shortlink` VARCHAR(12) NOT NULL,
 `message` VARCHAR(128) NOT NULL,
 `file_size` INT(32) NOT NULL,
 `preview` INT(1) NOT NULL,
 `tx_hex` VARCHAR(1000) NOT NULL,
 `tx_hash` VARCHAR(100) NOT NULL,
 `apiresult` MEDIUMTEXT NOT NULL,
 `errors` INT(2) NOT NULL,
 `paymentresult` MEDIUMTEXT NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Execute query
if ($conn->query($sql) === TRUE) {
    echo "Table 'protected_designs' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>