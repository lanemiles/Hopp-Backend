<?php

/*
User Info
Updated 1/11/2015

This will be called from the mobile device and return all the current user data in JSON format.

*/

require_once('UserClass.php');

if (isset($_GET['userID'])) {
	$userID = $_GET['userID'];
} 

$user = User::getUser($userID);
$user->getUserJSON();

?>