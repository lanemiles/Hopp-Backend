<?php

/*
Add Party Location
Updated 1/24/2015

This is used to create the non-technical backend for adding locations.

*/
if (isset($_GET['locationList'])) {

	$locationList = $_GET['locationList'];
	$locationList = array_filter($locationList);
	
} 
if (isset($_GET['center'])) {

	$center = $_GET['center'];

} 

if (isset($_GET['name'])) {

	$name = $_GET['name'];

} 

$id = time();

require('LocationClass.php');
require('PartyLocationClass.php');


$arrayOfLocs = array();
foreach ($locationList as $key => $string) {
	$temp = Location::getLocationFromString($string);

	array_push($arrayOfLocs, $temp);
}

$center = Location::getLocationFromString($center);

foreach ($locationList as $value) {
}


$temp1 = new PartyLocation($id, $name, $arrayOfLocs, $center);
$temp1->addPartyLocationToDatabase();
 print "<script type='text/javascript'>window.location = 'http://www.lanemiles.com/Hopp/addLocationPage.php'</script>";


?>