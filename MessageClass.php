<?php

/*
Message Class
Updated 1/11/2015

This class is used to handle the news feed component. 

*/

require_once('UserClass.php');

class Message {

	private $messageID;
	private $userID;
	private $timestamp;
	private $prettyTime;
	private $messageBody;
	private $voteCount;
	private $location;

	private static $connection;

	public function Message ($messageID, $userID, $messageBody) {
		$this->messageID = $messageID;
		$this->userID = $userID;
		$this->messageBody = $messageBody;
		$postedBy = User::getUser($this->userID);
		$this->location = $postedBy->getLocationName();
		date_default_timezone_set("America/Los_Angeles");
		$this->timestamp = date("Y-m-d H:i:s");
		$this->prettyTime = date("g:i A");
		$this->voteCount = 0;

		//give our user points
		$user = User::getUser($userID);
		$user->giveNPoints(10);
	}

	public function addMessageToDatabase() {
		//now add this user to the database
		
		$conn = self::getSQLConnection();
		$sql = "INSERT INTO `HeatmapData`.`Messages` (`messageID`, `userID`, `prettyTime`, `timestamp`, `messageBody`, `voteCount`,`Location`) 
		VALUES ('$this->messageID','$this->userID' , '$this->prettyTime', '$this->timestamp', '$this->messageBody', '$this->voteCount', '$this->location');";
		$q   = $conn->query($sql);
		var_dump($sql);
	}

	public function getUserID() {
		return $this->userID;
	}

	public function getPrettyTime() {
		return $this->prettyTime;
	}

	public function getMessageID() {
		return $this->messageID;
	}

	public function getMessageLocation() {
		return $this->location;
	}

	public function getMessageBody() {
		return $this->messageBody;
	}

	public function getVoteCount() {
		return $this->voteCount;
	}

	public function upVote() {
		$this->voteCount++;
		$this->synchronizeData();
	}

	public function downVote() {
		$this->voteCount--;
		$this->synchronizeData();
	}

	public function synchronizeData() {
		$conn = self::getSQLConnection();
		$sql = "UPDATE `HeatmapData`.`Messages` SET `voteCount` = '$this->voteCount' WHERE `messageID` = '$this->messageID'";
		$q   = $conn->query($sql);
	}

	//setters for getting from sql
	public function setPrettyTime($time) {
		$this->prettyTime = $time; 
	}

	public function setLocation($location) {
		$this->location = $location; 
	}


	public function setTimestamp($time) {
		$this->timestamp = $time;
	}

	public function setVoteCount($count) {
		$this->voteCount = $count;
	}

	public static function getSQLConnection() {
		if (is_null(self::$connection)) {
		$host   = "localhost";
		$db = "HeatmapData";
		self::$connection = new PDO("mysql:host=$host;dbname=$db","lanemiles","Baxter!12");
		}
		return self::$connection;	
	}

	public static function getMessage($messageID) {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `Messages` where `messageID` = '$messageID' ";
		$q   = $conn->query($sql); 
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$message = new Message($r["messageID"], $r["userID"], $r["messageBody"]);
			$message->setPrettyTime($r['prettyTime']);
			$message->setTimestamp($r['timestamp']);
			$message->setVoteCount($r['voteCount']);
			$message->setLocation($r['Location']);
			return $message;
		}
		
	}

	public static function getVoteHistoryForUser($userID) {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `MessageVotes` where `userID` = '$userID'";
		$q   = $conn->query($sql); 
		$array = array();
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
	
			$temp['messageID'] = $r["messageID"];
			$temp['vote'] = $r["vote"];
			
			array_push($array, $temp);
		}

		return $array;
	}

	public static function getMessagesJSON() {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `Messages` ORDER BY `messageID` DESC";
		$q   = $conn->query($sql); 
		$array = array();
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
			array_push($array, $temp);
		}
		$array = json_encode($array);
		print '{"Data" :';
		print $array;
		print '}';
	}

	public static function getMessagesForPartyLocation($partyName) {
		$conn = self::getSQLConnection();
		$sql = "SELECT * FROM  `Messages` WHERE `Location` = '$partyName' ORDER BY `messageID` DESC LIMIT 20";
		$q   = $conn->query($sql); 
		$array = array();
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
			array_push($array, $temp);
		}
		$array = json_encode($array);
		print '{"Data" :';
		print $array;
		print '}';
	}

	

}


?>