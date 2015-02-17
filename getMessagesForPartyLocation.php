<?php

/*
Get Messages By Location
Updated 1/24/2015

This will be called from the mobile device and will get the messages for 
the Location they just tapped on.

*/
require_once('MessageClass.php');

if (isset($_GET['partyName'])) {
	$partyName = $_GET['partyName'];
} 

require_once ('MessageClass.php');
require_once ('PartyLocationClass.php');

Message::getMessagesForPartyLocation($partyName);


?>