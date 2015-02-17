<?php

/*
Get Location Data
Updated 1/10/2015

This is called by the mobile device and returns the JSON data for all locations.

*/

require_once('PartyLocationClass.php');
PartyLocation::getPartyLocationJSON();

?>