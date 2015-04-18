<?php

/*
Upvote Message
Updated 2/21/2015

This will be called from the mobile device and will upvote the message.

*/
require_once ('MessageClass.php');
require_once ('UserClass.php');

if (isset($_GET['userID'])) {
	$userID = $_GET['userID'];
} 
if (isset($_GET['messageID'])) {
	$messageID = $_GET['messageID'];
} 

$user = User::getUser($userID);
$user->upvoteMessageWithID($messageID);


?>