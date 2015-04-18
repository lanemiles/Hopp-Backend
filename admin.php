<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<form method='get' action='manualUpdate.php'>


	

		<?php

			//get all party locations and print 
			print "Party Location : <select name='partyLocation'>";
	print "<option></option>";

$host   = "localhost";
$db = "HeatmapData";




$conn = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");


$sql = "SELECT * FROM `PartyLocations` ORDER BY Name ASC";
$q   = $conn->query($sql); 

while($r = $q->fetch(PDO::FETCH_ASSOC)){

	foreach ($r as $key => $value)
 {
 	if ($key == "name") {
 		$name = $value;
 	} elseif ($key == "partyLocationID") {
 		$id = $value;
 	} 
}


print "<option value=". $id. ">" . $name . "</option>";

	

}



print "</select>";

		?>

		<br><br>

		Hopp Level : <select name='level'>
			<option></option>
			<option value='0'>0</option>
			<option value='1'>1</option>
			<option value='2'>2</option>
			<option value='3'>3</option>
		</select>

		<br><br>

		Percent Guys (0-100) : <br> <input name='percentGuys'></input>

		<br><br>

		Dress Code : <select name='dressCode'>
			<option></option>
			<option value='casual'>Casual</option>
			<option value='formal'>Formal</option>
			<option value='theme'>Theme</option>
		</select>

		<br><br>

		Drink List : <select multiple name='drinkList[]'>
			<option></option>
			<option value='beer'>Beer</option>
			<option value='wine'>Wine</option>
			<option value='hard'>Liquor</option>
			<option value='none'>None</option>
		</select>

		<br><br>

		Party Type : <select multiple name='partyType[]'>
			<option></option>
			<option value='pregame'>Pregame</option>
			<option value='dorm'>Dorm Party</option>
			<option value='dance'>Dance Party</option>
			<option value='after'>After Party</option>
		</select>

		<br><br>

		<input type='submit'></input>

		</form>

	</form>

<br><br><a href='partyDetails.php'>Recent Party Information</a>
