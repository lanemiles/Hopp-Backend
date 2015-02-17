<?php

/*
User Class
Updated 1/9/2015

This class will be used to keep track of all the relevant information for each user. It's main use
will be being a wrapper to access the underlying SQL data. It will contain useful static methods 
that will retrieve user info when called by other classes.

*/

require_once('PartyLocationClass.php');
require_once('LocationClass.php');

class User {

	//user instance variables
	private $userID;
	private $longName;
	private $shortName;
	private $age;
	private $locationObject;
	private $partyLocationID;
	private $partyLocationName;
	private $gender;

	//the static connection object
	private static $connection = null;

	//constructor 
	public function User ($userID, $longName, $shortName, $age, $latitude, $longitude, $gender) {
		
		//first set instance vars
		$this->userID = $userID;
		$this->longName = $longName;
		$this->shortName = $shortName;
		$this->age = $age;
		$this->gender = $gender;
		$this->locationObject = new Location($latitude, $longitude);
		$this->setPartyLocation();
	}

	private function setPartyLocation() {
		$partyLocation = PartyLocation::getPartyForLocation($this->locationObject);
		
		if ($partyLocation != null) {
			
			$this->partyLocationID = $partyLocation->getPartyLocationID();
			$this->partyLocationName = $partyLocation->getPartyName();
		} else {
		
			$this->partyLocationID = null;
			$this->partyLocationName = "Unknown";
		}
		$this->synchronizeLocationInformation();
		
	}

	//update users location
	//we need to remove first then add
	public function updateLocation($latitude, $longitude) {
		$this->locationObject = new Location($latitude, $longitude);

		//before we get new party, check if we are in one
		if ($this->partyLocationID != null) {
			
			//if in a party, remove ourselves
			$myParty = PartyLocation::getPartyWithID($this->partyLocationID);
			$myParty->removeUser($this->userID);
					
		}


		$this->setPartyLocation();
		$this->addUserToParty();
		
		$test = PartyLocation::getPartyWithID($this->partyLocationID);
		$this->synchronizeLocationInformation();
		
		
	
	}

	//add user to party
	public function addUserToParty() {
		if ($this->partyLocationID != null) {
			$myParty = PartyLocation::getPartyWithID($this->partyLocationID);
			$myParty->addUser($this->userID);
		}
	}

	public function synchronizeLocationInformation() {
		$conn = self::getSQLConnection();

		$locationPlace = $this->partyLocationName;
		$locationCoords = $this->locationObject->getCombinedCoordinates();
		$latitude = $this->locationObject->getLatitude();
		$longitude = $this->locationObject->getLongitude();
		
		$sql = "UPDATE `HeatmapData`.`Users` SET  `locationPlace` =  '$locationPlace', `locationCoords` = '$locationCoords', 
				`latitude` = '$latitude', `longitude` = '$longitude' WHERE  `Users`.`userID` = '$this->userID';";
		$q = $conn->query($sql); 
	}


	//add user to database
	public function addUserToDatabase() {
		//now add this user to the database
		
		$conn = self::getSQLConnection();
		$locationCoords = $this->locationObject->getCombinedCoordinates();
		$locationPlace = $this->partyLocationName;
		$latitude = $this->locationObject->getLatitude();
		$longitude = $this->locationObject->getLongitude();
		$sql = "INSERT INTO `HeatmapData`.`Users` (`userID`, `fullName`, `shortName`, `gender`, `latitude`, `longitude`, `locationCoords`, `locationPlace`, `age`) 
		VALUES ('$this->userID','$this->longName' , '$this->shortName', '$this->gender', '$latitude', '$longitude', '$locationCoords', '$locationPlace', '$this->age');";
		$q   = $conn->query($sql);
		$this->addUserToParty();
	}

	//now getters
	public function getLongName() {
		return $this->longName;
	}

	public function getShortName() {
		return $this->shortName;
	}

	public function getAge() {
		return $this->age;
	}

	public function getGender() {
		return $this->gender;
	}

	public function getUserID() {
		return $this->userID;
	}

	public function getLocation () {
		return $this->locationObject;
	}

	public function getLocationName() {
		return $this->partyLocationName;
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

	public static function getUser($userID) {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `Users` where `userID` = '$userID' ";
		$q   = $conn->query($sql); 
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$user = new User($r["userID"], $r["fullName"], $r["shortName"], $r["age"], $r["latitude"],$r["longitude"], $r["gender"]);
			return $user;
		}
		
	}

	public static function userExists($userID) {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `Users` where `userID` = '$userID' ";
		$q   = $conn->query($sql); 
		$r = $q->fetch(PDO::FETCH_ASSOC);
		return ($r != false);
	}

	public function getUserJSON() {
		$array['userID'] = $this->userID;
		$array['placeName'] = $this->partyLocationName;
		$array['latitude'] = $this->locationObject->getLatitude();
		$array['longitude'] = $this->locationObject->getLongitude();
		$array['gender'] = $this->gender;
		$array['age'] = $this->age;
		date_default_timezone_set("America/Los_Angeles");
		$array['time'] = date("g:i A"); 
		$array['shortName'] = $this->shortName;
		$array['fullName'] = $this->longName;
		$array = json_encode($array);
		print '{"Data" :';
		print $array;
		print '}';
	}

}








?>