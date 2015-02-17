<?php

/*
Check how long background things run
Updated 2/2/2015


*/
date_default_timezone_set('America/Los_Angeles');
$time = date("Y-m-d H:i:s");

$host   = "localhost";
$db = "HeatmapData";
$conn = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");

$sql = "INSERT INTO  `HeatmapData`.`Update Times` (
`updateID` ,
`time`
)
VALUES (
NULL ,  '$time'
);
";
print $sql;
$q   = $conn->query($sql); 

?>