<?php

/*
Building Class
Updated 1/9/2015

This class will be used to keep track of all the relevant information for each place.


*/
require_once('PointLocationClass.php');
require_once('LocationClass.php');
require_once('UserClass.php');
require_once('MessageClass.php');


class PartyLocation {
	
	//instance variables
	private $partyLocationID;
	private $arrayOfLocationObjs;
	private $name;
	private $numPeople;
	private $listOfUserIDs;
	private $centerCoordinate;
	private $listOfDrinkTypes;
	private $dressCode;
	private $listOfPartyTypes;
	private $hoppLevel;
	private $percentGuys;

	//connection obj
	private static $connection = null;

	//constructor
	public function PartyLocation($partyID, $name, $cords, $centerCoord) {
		$this->partyLocationID = $partyID;
		$this->name = $name;
		$this->arrayOfLocationObjs = $cords;
		$this->numPeople = 0;
		$this->listOfUserIDs = array();
		$this->listOfDrinkTypes = array();
		$this->dressCode = "";
		$this->listOfPartyTypes = array();
		$this->centerCoordinate = $centerCoord;
		$this->hoppLevel = 0;
		$this->percentGuys = 0;
	}

	//getters
	public function getPartyName () {
		return $this->name;
	}

	public function getCenterCoordinate() {
		return $this->centerCoordinate;
	}

	public function getPartyLocationID () {
		return $this->partyLocationID;
	}

	public function getNumPeople() {
		return $this->numPeople;
	}

	public function getHoppLevel() {
		return $this->hoppLevel;
	}

	public function getDressCode() {
		return $this->dressCode;
	}

	public function getPartyTypeList() {
		return $this->partyTypeList;
	}

	public function getDrinkList() {
		return $this->drinkList;
	}

	public function getPercentGuys() {
		return $this->percentGuys;
	}

	public function getPercentGirls() {
		return (100 - $this->percentGuys);
	}

	public function getOutlineArray() {
		$returnArray = array();
		foreach ($this->arrayOfLocationObjs as $key => $locationObj) {
			$temp['Latitude'] = $locationObj->getLatitude();
			$temp['Longitude'] = $locationObj->getLongitude();
			array_push($returnArray, $temp);
		}
		return $returnArray;
	}

	public function setNumPeople($num) {
		$this->numPeople = $num;
	}

	public function setUserIDList($list) {
		$this->listOfUserIDs = $list;
	}

	public function setDrinkTypes($list) {
		$this->listOfDrinkTypes = $list;
	}

	public function setDressCode($style) {
		$this->dressCode = $style;
	}

	public function setPartyTypes($types) {
		$this->listOfPartyTypes = $types;
	}

	public function setHoppLevel($level) {
		$this->hoppLevel = $level;
	}

	public function setPercentGuys($percent) {
		$this->percentGuys = $percent;
	}

	//add party to database
	public function addPartyLocationToDatabase() {
		//now add this user to the database
		$conn = self::getSQLConnection();
		$outlineArray = serialize($this->arrayOfLocationObjs);
		$userIDList = serialize($this->listOfUserIDs);
		$centerCord = serialize($this->centerCoordinate);
		$drinkList = serialize($this->listOfDrinkTypes);
		$partyTypeList = serialize($this->listOfPartyTypes);
		$sql = "INSERT INTO  `HeatmapData`.`PartyLocations` ( `partyLocationID` , `name` , `numPeople` , `partyOutlineLocations` , `listOfUserIDs` , `centerCoordinate`, `drinkList`, `partyTypeList`, `dressCode`, `hoppLevel`, `percentGuys`)
		VALUES ($this->partyLocationID ,  '$this->name',  '$this->numPeople',  '$outlineArray',  '$userIDList',  '$centerCord', '$drinkList', '$partyTypeList', '$this->dressCode', '$this->hoppLevel', '$this->percentGuys');";
		$q   = $conn->query($sql); 
	}

	public function synchronizeData() {
		$conn = self::getSQLConnection();
		$outlineArray = serialize($this->arrayOfLocationObjs);
		$userIDList = serialize($this->listOfUserIDs);
		$centerCord = serialize($this->centerCoordinate);
		$drinkList = serialize($this->listOfDrinkTypes);
		$partyTypeList = serialize($this->listOfPartyTypes);
		$dressCode = $this->dressCode;
		$sql = "UPDATE  `HeatmapData`.`PartyLocations` SET  `partyLocationID` =  '$this->partyLocationID',
		`name` =  '$this->name',
		`numPeople` =  '$this->numPeople',
		`partyOutlineLocations` =  '$outlineArray',
		`listOfUserIDs` =  '$userIDList',
		`centerCoordinate` =  '$centerCord' ,
		`drinkList` =  '$drinkList' ,
		`partyTypeList` =  '$partyTypeList' ,
		`dressCode` =  '$dressCode',
		`hoppLevel` = '$this->hoppLevel',
		`percentGuys` = '$this->percentGuys'
		WHERE  `PartyLocations`.`partyLocationID` = '$this->partyLocationID';";
		$q   = $conn->query($sql); 
	}

	//create the point in polygon formatted array
	public function getPointInPolygonArray() {
		$returnArray = array();
		foreach ($this->arrayOfLocationObjs as $key => $location) {
			array_push($returnArray, $location->pointInPolygonFormat());
		}
		return $returnArray;
	}


	//adding a user to a party location
	public function addUser ($userID) {
	
		if (!($this->userIsAlreadyHere($userID))) {
		//then add id to list
		array_push($this->listOfUserIDs, $userID);
		//then update people
		$this->numPeople++;
		
		//update database
		$this->synchronizeData();

	}

	}

	//remove a user from a party
	public function removeUser ($userID) {

		if ($this->userIsAlreadyHere($userID)) {

			//remove ID
			$this->listOfUserIDs = array_diff($this->listOfUserIDs, [$userID]);
		
			//then update people
			$this->numPeople--;

			//update DB	
			$this->synchronizeData();

		}
		
	}

	//clear a party and reset all
	public function resetParty () {
		$this->numPeople = 0;
		$this->listOfUserIDs = array();
		$this->synchronizeData();
	}

	//check if a user is already at a location
	public function userIsAlreadyHere($userID) {
		return in_array($userID, $this->listOfUserIDs);
	}

	//check if party contains location coordinates
	private function contains ($userLocObj) {
		$pointLocation = new pointLocation();
		$userLocation = $userLocObj->pointInPolygonFormat();
		$polygon = $this->getPointInPolygonArray();

		$isInPartyLocation = $pointLocation->pointInPolygon($userLocation, $polygon);	
		return $isInPartyLocation;
	}

	//get party id for location
	public static function getPartyForLocation($userLocObj) {
		//to do this, we need to get all of the party locations
		$conn = self::getSQLConnection();
		$sql = "SELECT `partyLocationID` FROM  `PartyLocations`";
		$q   = $conn->query($sql); 
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$party = PartyLocation::getPartyWithID($r['partyLocationID']);
			$isInParty = $party->contains($userLocObj);
			//print "Checking " . $partyObj->getPartyName() . " and got " . var_dump($isInParty) . "<BR>";

			if ($isInParty) {
				return $party;
			}

		}
		return null;

	}

	//get the party location for the given ID
	public static function getPartyWithID($partyID) {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `PartyLocations` where `partyLocationID` = '$partyID' ";
		$q   = $conn->query($sql); 
		while($r = $q->fetch(PDO::FETCH_ASSOC)){

			//get and set from the SQL
			$outlineArray = unserialize($r['partyOutlineLocations']);
			$userIDList = unserialize($r['listOfUserIDs']);
			$centerCord = unserialize($r['centerCoordinate']);

			$drinkList = unserialize($r['drinkList']);
			$partyTypeList = unserialize($r['partyTypeList']);
			$dressCode = $r['dressCode'];
			$hoppLevel = $r['hoppLevel'];

			$newParty = new PartyLocation($r['partyLocationID'], $r['name'], $outlineArray, $centerCord);
			$newParty->setNumPeople($r['numPeople']);
			$newParty->setUserIDList($userIDList);


			$newParty->setDressCode($dressCode);
			$newParty->setPartyTypes($partyTypeList);
			$newParty->setDrinkTypes($drinkList);
			$newParty->setHoppLevel($hoppLevel);
			$newParty->setPercentGuys($r['percentGuys']);

			return $newParty;
		}
		
	}

	//this checks if the connection object exists, if not, creates it
	public static function getSQLConnection() {
		if (is_null(self::$connection)) {
		$host   = "localhost";
		$db = "HeatmapData";
		self::$connection = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");
		}
		return self::$connection;	
	}

	//this returns a list of locations with names and the number of people
	public static function getPartyLocationJSON() {
		$conn = self::getSQLConnection();
		$sql = "SELECT `partyLocationID` FROM  `PartyLocations`";
		$q   = $conn->query($sql); 
		$nameToArray = array();
		$nameToNum = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$party = PartyLocation::getPartyWithID($r['partyLocationID']);
			// if ($party->getHoppLevel() > 0 && $party->getPartyName() != "Clark III") {
			if (true) {
			$name = $party->getPartyName();
			$numPeople = $party->getNumPeople();
			$centerLat = $party->getCenterCoordinate()->getLatitude();
			$centerLong = $party->getCenterCoordinate()->getLongitude();
			$temp['Name'] = $name;
			$temp['Hopp Level'] = $party->getHoppLevel();
			$temp['Dress Code'] = $party->getDressCode();
			$temp['Party Types'] = $party->getPartyTypeList();
			$temp['Drink List'] = $party->getDrinkList();
			$temp['Percent Guys'] = $party->getPercentGuys();
			$temp['Percent Girls'] = $party->getPercentGirls();
			$temp['Latitude'] = floatval($centerLat);
			$temp['Longitude'] = floatval($centerLong);
			$temp['Outline'] = $party->getOutlineArray();
			$nameToArray[$name] = $temp;
			$nameToNum[$name] = $numPeople;
		}
	}
		arsort($nameToNum);
		$array = array();
		foreach ($nameToNum as $name => $num) {
			array_push($array, $nameToArray[$name]);
		}
		//$array = array_slice($array, 0, 5);
		$array = json_encode($array);
		print '{"Data" :';
		print $array;
		print '}';
	}

	public static function getDataForPartyWithName($name) {
		$conn = self::getSQLConnection();

		//first, lets get name and num people
		$sql = "SELECT `partyLocationID` FROM  `PartyLocations` where `Name` = '$name'";
		$q   = $conn->query($sql); 
		$array = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$party = PartyLocation::getPartyWithID($r['partyLocationID']);
			$name = $party->getPartyName();
			$numPeople = $party->getNumPeople();
			$centerLat = $party->getCenterCoordinate()->getLatitude();
			$centerLong = $party->getCenterCoordinate()->getLongitude();
			$temp['Name'] = $name;
			$temp['Hopp Level'] = $party->getHoppLevel();
			$temp['Dress Code'] = $party->getDressCode();
			$temp['Party Types'] = $party->getPartyTypeList();
			$temp['Drink List'] = $party->getDrinkList();
			$temp['Percent Guys'] = $party->getPercentGuys();
			$temp['Percent Girls'] = $party->getPercentGirls();
			array_push($array, $temp);
		}

		//and now the messages
		$sql = "SELECT * FROM  `Messages` WHERE `Location` = '$name' ORDER BY `messageID` DESC LIMIT 20";
		$q   = $conn->query($sql); 
		$array1 = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$message = new Message($r["messageID"], $r["userID"], $r["messageBody"]);
			$message->setPrettyTime($r['prettyTime']);
			$message->setTimestamp($r['timestamp']);
			$message->setVoteCount($r['voteCount']);
			$message->setLocation($r['Location']);
			$temp['messageID'] = $message->getMessageID();
			$temp['location'] = $message->getMessageLocation();
			$temp['messageBody'] = $message->getMessageBody();
			$temp['time'] = $message->getPrettyTime();
			$temp['voteCount'] = $message->getVoteCount();
			array_push($array1, $temp);			
		}
		array_push($array, $array1);
		$array = json_encode($array);
		print '{"Data" :';
		print $array;
		print '}';
	}

	public static function returnHoppLevelForPartyWithName($name) {
		$conn = self::getSQLConnection();

		//first, lets get name and num people
		$sql = "SELECT `partyLocationID` FROM  `PartyLocations` where `Name` = '$name'";
		$q   = $conn->query($sql); 
		$array = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$party = PartyLocation::getPartyWithID($r['partyLocationID']);
			$name = $party->getPartyName();
			$numPeople = $party->getNumPeople();
			$centerLat = $party->getCenterCoordinate()->getLatitude();
			$centerLong = $party->getCenterCoordinate()->getLongitude();
			$temp['Name'] = $name;
			$temp['Hopp Level'] = $party->getHoppLevel();
			$temp['Dress Code'] = $party->getDressCode();
			$temp['Party Types'] = $party->getPartyTypeList();
			$temp['Drink List'] = $party->getDrinkList();
			$temp['Percent Guys'] = $party->getPercentGuys();
			$temp['Percent Girls'] = $party->getPercentGirls();
			array_push($array, $temp);
		}

		return $temp['Hopp Level'];
	}



}



//PartyLocation::getPartyForLocation(new Location(34.100256, -117.709582));


?>