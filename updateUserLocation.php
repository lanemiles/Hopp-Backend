<?php

/*
Update User Location
Updated 1/10/2015

This will be called from the mobile device and will update the location coordinates
and also the party location where the user is.

*/

//get parameters from iPhone
	//userID (devide unique vendor ID)
	//latitude
	//longitude

if (isset($_GET['userID'])) {
	$userID = $_GET['userID'];
} 
if (isset($_GET['latitude'])) {
	$latitude = $_GET['latitude'];
} 
if (isset($_GET['longitude'])) {
	$longitude = $_GET['longitude'];
}

//now we get our user
require_once('UserClass.php');

//we should note here what's happening in SQL
$userCurrPlace = User::getUser($userID)->getLocationName();

$newPlace = PartyLocation::getPartyForLocation(new Location($latitude, $longitude));
if ($newPlace == null) {
	$newPlace = "UNKNOWN";
} else {
	$newPlace = $newPlace->getPartyName();
}
date_default_timezone_set('America/Los_Angeles');
$time = date("Y-m-d H:i:s");


$host   = "localhost";
$db = "HeatmapData";
$conne = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");

$sql = "INSERT INTO `HeatmapData`.`Location Updates` (
`updateID`,`userID`,`oldPlaceName`,`newPlaceName`,`time`)
VALUES (NULL,'$userID','$userCurrPlace','$newPlace','$time');";

$q   = $conne->query($sql); 

//here is the actual code
$getUser = User::getUser($userID);
$getUser->updateLocation($latitude, $longitude);

?>