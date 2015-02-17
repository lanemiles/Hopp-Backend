<?php

/*
Building Class
Updated 1/9/2015

This class will be used to keep track of all the relevant information for each place.
We will store the serialized version of the object in our database and then retrieve
it and unserialize before calling the various methods. We then will re-serialize before
putting back into the database.

*/
require_once('PointLocationClass.php');
require_once('LocationClass.php');
require_once('UserClass.php');


class PartyLocation {
	
	//instance variables
	private $partyLocationID;
	private $arrayOfLocationObjs;
	private $name;
	private $numPeople;
	private $listOfUserIDs;
	private $centerCoordinate;

	//connection obj
	private static $connection = null;

	//constructor
	public function PartyLocation($partyID, $name, $cords, $centerCoord) {
		$this->partyLocationID = $partyID;
		$this->name = $name;
		$this->arrayOfLocationObjs = $cords;
		$this->numPeople = 0;
		$this->listOfUserIDs = array();
		$this->centerCoordinate = $centerCoord;
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

	//add party to database
	public function addPartyLocationToDatabase() {
		//now add this user to the database
		$conn = self::getSQLConnection();
		$outlineArray = serialize($this->arrayOfLocationObjs);
		$userIDList = serialize($this->listOfUserIDs);
		$centerCord = serialize($this->centerCoordinate);
		$sql = "INSERT INTO  `HeatmapData`.`PartyLocations` ( `partyLocationID` , `name` , `numPeople` , `partyOutlineLocations` , `listOfUserIDs` , `centerCoordinate`)
		VALUES ($this->partyLocationID ,  '$this->name',  '$this->numPeople',  '$outlineArray',  '$userIDList',  '$centerCord');";
		$q   = $conn->query($sql); 
	}

	public function synchronizeData() {
		$conn = self::getSQLConnection();
		$outlineArray = serialize($this->arrayOfLocationObjs);
		$userIDList = serialize($this->listOfUserIDs);
		$centerCord = serialize($this->centerCoordinate);
		$sql = "UPDATE  `HeatmapData`.`PartyLocations` SET  `partyLocationID` =  '$this->partyLocationID',
		`name` =  '$this->name',
		`numPeople` =  '$this->numPeople',
		`partyOutlineLocations` =  '$outlineArray',
		`listOfUserIDs` =  '$userIDList',
		`centerCoordinate` =  '$centerCord' WHERE  `PartyLocations`.`partyLocationID` = '$this->partyLocationID';";
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
			$newParty = new PartyLocation($r['partyLocationID'], $r['name'], $outlineArray, $centerCord);
			$newParty->setNumPeople($r['numPeople']);
			$newParty->setUserIDList($userIDList);
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
			$name = $party->getPartyName();
			$numPeople = $party->getNumPeople();
			$centerLat = $party->getCenterCoordinate()->getLatitude();
			$centerLong = $party->getCenterCoordinate()->getLongitude();
			$temp['Name'] = $name;
			$temp['NumPeople'] = $numPeople;
			$temp['Latitude'] = floatval($centerLat);
			$temp['Longitude'] = floatval($centerLong);
			$temp['Outline'] = $party->getOutlineArray();
			$nameToArray[$name] = $temp;
			$nameToNum[$name] = $numPeople;
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
			$temp['NumPeople'] = $numPeople;
			$temp['Latitude'] = floatval($centerLat);
			$temp['Longitude'] = floatval($centerLong);
			$temp['Outline'] = $party->getOutlineArray();
			array_push($array, $temp);
		}
		$array = json_encode($array);
		print '{"Data" :';
		print $array;
		print '}';
	}


}

//PartyLocation::getPartyForLocation(new Location(34.100256, -117.709582));


?>