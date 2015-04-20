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

$listOfBannedWords = array("nigger", "nigg", "fag", "faggot", "gay", "cock", "penis", "vagina", "cunt", "bitch",
						   "rape", "retard", "dick");

$shouldPost = true;

foreach($listOfBannedWords as $a) {
    if (stripos($messageBody,$a) !== false) {
    	$shouldPost = false;
    }
}

if ($shouldPost) {
	$message = new Message ($messageID,$userID,$messageBody);
	$message->addMessageToDatabase();
}



?>