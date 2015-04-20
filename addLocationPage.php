<?php

/*
Add Party Location Page
Updated 1/24/2015

This is used to create the non-technical backend for adding locations.





*/
print "<h1>Add a Location</h1>";
$array = array();
$host   = "localhost";
$db = "HeatmapData";
$conn = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");
$sql = 'SELECT `Name` from `PartyLocations`';
$q   = $conn->query($sql); 
while($r = $q->fetch(PDO::FETCH_ASSOC)){

	if (in_array($r['Name'], $array)) {

	} else {
		array_push($array, $r['Name']);
	}
    
 

}
print "<b>Locations Already Input:</b>" . "<br>";
foreach ($array as $value) {
	print $value . "<br>";
}

print "<hr><br>";
print "<form action='addPartyLocation.php' method='get'>";

print "<b>Location Name:</b> <input name='name'></input>";
print "<hr><b>Enter Outline (Go In Order) Must Finish Where Started</b>";
print "<br>";
print "1: <input name='locationList[]'></input>" . "<br>";
print "2: <input name='locationList[]'></input>" . "<br>";
print "3: <input name='locationList[]'></input>" . "<br>";
print "4: <input name='locationList[]'></input>" . "<br>";
print "5: <input name='locationList[]'></input>" . "<br>";
print "6: <input name='locationList[]'></input>" . "<br>";
print "7: <input name='locationList[]'></input>" . "<br>";
print "8: <input name='locationList[]'></input>" . "<br>";
print "9: <input name='locationList[]'></input>" . "<br>";
print "10: <input name='locationList[]'></input>" . "<br>";
print "11: <input name='locationList[]'></input>" . "<br>";
print "12: <input name='locationList[]'></input>" . "<br>";
print "13: <input name='locationList[]'></input>" . "<br>";
print "14: <input name='locationList[]'></input>" . "<br>";
print "15: <input name='locationList[]'></input>" . "<br>";
print "16: <input name='locationList[]'></input>" . "<br>";
print "17: <input name='locationList[]'></input>" . "<br>";
print "18: <input name='locationList[]'></input>" . "<br>";
print "19: <input name='locationList[]'></input>" . "<br>";
print "20: <input name='locationList[]'></input>" . "<br>";
print "<hr><b>Enter Center (Where Pin Goes)</b>";
print "<br>";
print "First: <input name='center'></input>" . "<br>";
print "<input type='submit'></input>";
?>

<br><br><br><a href='admin.php'>Back to Admin Panel</a><br>