<?php

/*
Get Messages
Updated 1/11/2015

This will be called from the mobile device and return all the message in JSON format.

*/

require_once('MessageClass.php');
Message::getMessagesJSON();

?>