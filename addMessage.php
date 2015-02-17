<?php

/*
Add Message
Updated 1/11/2015

This will be called from the mobile device and will add a message to the database.

*/
require_once ('MessageClass.php');

if (isset($_GET['userID'])) {
	$userID = $_GET['userID'];
} 
if (isset($_GET['messageID'])) {
	$messageID = $_GET['messageID'];
} 
if (isset($_GET['messageBody'])) {
	$messageBody = addslashes($_GET['messageBody']);
}

$message = new Message ($messageID,$userID,$messageBody);
$message->addMessageToDatabase();
var_dump($message);


?>