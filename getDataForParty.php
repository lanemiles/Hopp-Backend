<?php

/*
Get Data for Party
Updated 2/2/2015

This is called by the mobile device when the device transitions to the Party Info TVC.

*/

if (isset($_GET['partyName'])) {

	$partyName = $_GET['partyName'];
	
} 

require_once('PartyLocationClass.php');

PartyLocation::getDataForPartyWithName($partyName);

?>