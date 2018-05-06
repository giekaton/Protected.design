<?php

//Connect to Mysql
include_once "_auth.php";

// sql to create 'protected_designs' table

$sql = "CREATE TABLE `protected_designs` (
 `ID` INT(10) NOT NULL AUTO_INCREMENT,
 `timestamp` VARCHAR(64) NOT NULL,
 `status` VARCHAR(32) NOT NULL,
 `protection_type` VARCHAR(1) NOT NULL,
 `paid` INT(1) NOT NULL,
 `hash` VARCHAR(64) NOT NULL UNIQUE,
 `grouped_hash` VARCHAR(64) NOT NULL,
 `shortlink` VARCHAR(12) NOT NULL,
 `message` VARCHAR(128) NOT NULL,
 `file_size` INT(32) NOT NULL,
 `preview` INT(1) NOT NULL,
 `preview_url` VARCHAR(256) NOT NULL,
 `tx_hex` VARCHAR(1000) NOT NULL,
 `tx_block` VARCHAR(32) NOT NULL,
 `tx_timestamp_hex` VARCHAR(32) NOT NULL,
 `tx_timestamp` VARCHAR(32) NOT NULL,
 `tx_hash` VARCHAR(100) NOT NULL,
 `errors` INT(2) NOT NULL,
 `error_log` VARCHAR(9999) NOT NULL,
 `apiresult` MEDIUMTEXT NOT NULL,
 `paymentresult` MEDIUMTEXT NOT NULL,
 `otherresult` MEDIUMTEXT NOT NULL,
 `payment_id` VARCHAR(64) NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";


$sql2 = "CREATE TABLE `pd_settings` (
`ID` INT(11) NOT NULL AUTO_INCREMENT,
`donations` VARCHAR(64) NOT NULL,
`timestamp` VARCHAR(64) NOT NULL,
PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


// Execute queries
if ($conn->query($sql) === TRUE) {
    echo "Table 'protected_designs' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

if ($conn->query($sql2) === TRUE) {
    echo "Table 'pd_settings' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


$conn->close();
?>