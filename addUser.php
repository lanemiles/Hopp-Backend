<?php

/*
Add User
Updated 1/10/2015

This will be called from the mobile device and will register the user by adding them to the SQL.

*/

//get parameters from iPhone
	//userID (devide unique vendor ID)
	//long name (FB)
	//short name (FB)
	//age (FB)
	//gender (FB)
	//location coords (device GPS)
		//lattitude 
		//longitude

if (isset($_GET['userID'])) {
	$userID = $_GET['userID'];
} 
if (isset($_GET['longName'])) {
	$longName = $_GET['longName'];
} 
if (isset($_GET['shortName'])) {
	$shortName = $_GET['shortName'];
} 
if (isset($_GET['age'])) {
	$age = $_GET['age'];
} 
if (isset($_GET['gender'])) {
	$gender = $_GET['gender'];
} 
if (isset($_GET['latitude'])) {
	$latitude = $_GET['latitude'];
} 
if (isset($_GET['longitude'])) {
	$longitude = $_GET['longitude'];
}

//now we make our user
require_once('UserClass.php');
if (User::userExists($userID)) {
	
} else {
	$newUser = new User($userID, $longName, $shortName, $age, $latitude, $longitude, $gender);
$newUser->addUserToDatabase();
}



?>