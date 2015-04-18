<?php

$host   = "localhost";
$db = "HeatmapData";
$conn = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");

print "<h1>Data from last 30 minutes</h1>";

$userList = array();

$sql = "SELECT `userID` FROM `Users`";
$q   = $conn->query($sql); 

while($r = $q->fetch(PDO::FETCH_ASSOC)){

	foreach ($r as $key => $value)
 {
 	array_push($userList, $value);
}
}



$locationList = array();


foreach ($userList as $key => $userID) {
	$sql = "SELECT `newPlaceName`, `time` FROM  `Location Updates` WHERE  `UserID` =  '$userID' ORDER BY `time` DESC LIMIT 1";

$q   = $conn->query($sql); 

while($r = $q->fetch(PDO::FETCH_ASSOC)){

	foreach ($r as $key => $value)
 {
 	if ($key == 'newPlaceName') {
		date_default_timezone_set("America/Los_Angeles");
		$now = new DateTime();
$then = new DateTime($r['time']); // "2012-07-18 21:11:12" for example
$diff = $now->diff($then);
$minutes = ($diff->format('%d') * 1440) + // total days converted to minutes
           ($diff->format('%h') * 60) +   // hours converted to minutes
            $diff->format('%i');          // minutes
if ($minutes <= 30) {
    if (!(isset($locationList[$value]))) {
 		$locationList[$value] = 1;
 	} else {
 		$locationList[$value] = $locationList[$value] + 1;
 	}
 }
}


 	
}
}

}
print "";
print "<table border=1>";
print "<tr><td>Party Location</td><td>Number of People There</td></tr>";
foreach ($locationList as $location => $numThere) {
	print "<tr><td>" . $location . "</td><td>" . $numThere . "</td></tr>";
}

print "</table>";

?>
<br><br><a href='admin.php'>Manual Entry Page</a>



