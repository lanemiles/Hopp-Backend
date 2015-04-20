<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<?php

require_once('PartyLocationClass.php');


print "<br><br><br>";

if (isset($_GET['partyLocation'])) {
	$partyLocation = intval($_GET['partyLocation']);
} 

if (isset($_GET['level'])) {
	$level = intval($_GET['level']);
} 

if (isset($_GET['percentGuys'])) {
	$percentGuys = intval($_GET['percentGuys']);
} 

if (isset($_GET['dressCode'])) {
	$dressCode = $_GET['dressCode'];
} 

if (isset($_GET['drinkList'])) {
	$drinkList = $_GET['drinkList'];
} 

if (isset($_GET['partyType'])) {
	$partyType = $_GET['partyType'];
} 


$partyToEdit = PartyLocation::getPartyWithID($partyLocation);


$partyToEdit->setHoppLevel($level);
$partyToEdit->setPercentGuys($percentGuys);
$partyToEdit->setDressCode($dressCode);
$partyToEdit->setDrinkTypes($drinkList);
$partyToEdit->setPartyTypes($partyType);
$partyToEdit->synchronizeData();

print '<script type="text/javascript">
<!--
window.location = "/Hopp/partyAdmin.php";
//-->
</script>';

?>

