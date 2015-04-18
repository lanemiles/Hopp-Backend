<?php

$mainArray = array();

require('rollingCurl.php');

$person = "Delaney";

$startYear = 2013;
$currentYear = Date("Y");



$diff = $currentYear - $startYear;

$bigArray = array();

for ($i = 0; $i <= $diff; $i++) {

	$tempYear = $startYear + $i;
	$numVotes = getVotesFromYear($tempYear);

	$tempArray = array();


	for ($q = 1; $q <= $numVotes; $q++) {
		$url = makeUrl($tempYear,$q);
		array_push($tempArray,$url);
	}

	array_push($bigArray,$tempArray);

}

function getVotesFromYear($year) {

	$url = "http://www.howdwevote.com/numberOfVotesYearly.php?year=" . $year;
$html = file_get_contents($url);
$json = json_decode($html,true);
$votes = $json["Number of Votes"];
return $votes;

}

$urlList = array();



foreach ($bigArray as $key => $value) {
	foreach ($value as $left => $right) {
		array_push($urlList, $right);
	}
}



// a little example that fetches a bunch of sites in parallel and echos the page title and response info for each request
function request_callback($response, $info, $request) {
        // parse the page title out of the returned HTML
       getVoteInformation ($response);
}

             
$rc = new RollingCurl("request_callback");
$rc->window_size = 40;
foreach ($urlList as $url) {
    $request = new RollingCurlRequest($url);
    $rc->add($request);
}
$rc->execute();


function makeUrl ($year, $num) {

	
	$url = "http://clerk.house.gov/evs/" . $year . "/roll";


	if ($num < 10) {
		$num = "00" . $num;
	} else if ($num < 100) {
		$num = "0" . $num;
	}

	$finalUrl =  $url . $num . ".xml";
	return $finalUrl;

}



function getVoteInformation($html) {

//get the person whose votes we are looking for
global $person;
global $mainArray;

//create the array that this vote information will be stored in
$tempArray = array();

//load the roll call vote information
$xml = simplexml_load_string($html);

//now, we need to determine what type of vote this is

//check if election
if ($xml->{'vote-metadata'}->{'vote-question'} == "Election of the Speaker") {
    $type = "Election";
} elseif (isset($xml->{'vote-metadata'}->{'amendment-num'})) {
    $type = "Amendment";
} elseif (strpos($xml->{'vote-metadata'}->{'vote-desc'},"vote was vacated") == 5) {
    $type = "Vacated";
} elseif ($xml->{'vote-metadata'}->{'vote-type'} == "QUORUM") {
    $type = "Quorum";
} else {
    $type = "Regular";
}

//some things exist regardless
$rollcallNum =  (string) $xml->{'vote-metadata'}->{'rollcall-num'};
$date =  (string) $xml->{'vote-metadata'}->{'action-date'};
$date = getDates($date);
$year = substr($date,0,4);
$index = $rollcallNum . $year;

$tempArray['rollcallNum'] = $rollcallNum;
$tempArray['date'] = $date;

//now, rest is dependent on type

//if its vacant, do nothing but put bill title as vacant

if ($type == "Vacated") {
    $billTitle = "Vote was vacated. See Congressional record for " . $date;
    $vote = "Other";
    $tempArray['billTitle'] = $billTitle;
    $tempArray['vote'] = $vote;
} else if ($type == "Amendment") {

    $billNumber =   (string) $xml->{'vote-metadata'}->{'legis-num'};
    $voteQuestion =  (string) $xml->{'vote-metadata'}->{'vote-question'};
    $amendmentNum = (string) $xml->{'vote-metadata'}->{'amendment-num'};
    $amendmentAuthor = (string) $xml->{'vote-metadata'}->{'amendment-author'};
    $voteResult =  (string) $xml->{'vote-metadata'}->{'vote-result'};
    $republicanYeas = (int) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[0]->{'yea-total'};
    $republicanNays = (int) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[0]->{'nay-total'};
    $democraticYeas = (int) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[1]->{'yea-total'};
    $democraticNays = (int) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[1]->{'nay-total'};

    for ($i = 0; $i < 435; $i++) {

    if (($xml->{'vote-data'}->{'recorded-vote'}[$i]->{'legislator'}) == $person) {
        $vote =  (string) $xml->{'vote-data'}->{'recorded-vote'}[$i]->{'vote'};

        break;
    }


}


    $tempArray['billNumber'] = $billNumber;
    $tempArray['voteQuestion'] = $voteQuestion;
    $tempArray['amendmentNum'] = $amendmentNum;
    $tempArray['amendmentAuthor'] = $amendmentAuthor;
    $tempArray['voteResult'] = $voteResult;
    $tempArray['republicanYeas'] = $republicanYeas;
    $tempArray['republicanNays'] = $republicanNays;
    $tempArray['democraticYeas'] = $democraticYeas;
    $tempArray['democraticNays'] = $democraticNays;
    $tempArray['vote'] = $vote;

      $numDems = 191;
    $numReps = 240;

    $halfDems = round($numDems / 2);
    $halfReps = round($numReps / 2);

    if ($republicanYeas > $halfReps && $democraticYeas > $halfDems) {
        $split = 0;
    } else if ($republicanNays > $halfReps && $democraticNays > $halfDems) {
        $split = 0;
    } else {
        $split = 1;
    }
    
    
    
    if ($democraticYeas > $halfDems && ($tempArray['vote']=="Nay" || $tempArray['vote']=="No")) {
        $delaneyDev = 1;
    } else if ($democraticNays > $halfDems && ($tempArray['vote']=="Yea" || $tempArray['vote']=="Aye")) {
        $delaneyDev = 1;
    } else {
        $delaneyDev = 0;
    }


    $tempArray['splitByParty'] = $split;
    $tempArray['delaneyBreak'] = $delaneyDev;

} else if ($type == "Quorum") {



    $billNumber =  (string) $xml->{'vote-metadata'}->{'legis-num'};
    $voteQuestion =  (string) $xml->{'vote-metadata'}->{'vote-question'};
    $voteResult =  (string) $xml->{'vote-metadata'}->{'vote-result'};
    $republicanYeas = (string) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[0]->{'present-total'};
    $democraticYeas = (string) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[1]->{'present-total'};

    for ($i = 0; $i < 435; $i++) {

    if (($xml->{'vote-data'}->{'recorded-vote'}[$i]->{'legislator'}) == $person) {
        $vote = (string) $xml->{'vote-data'}->{'recorded-vote'}[$i]->{'vote'};

        break;
    }



}

    $tempArray['democraticYeas'] = $democraticYeas;
    $tempArray['republicanYeas'] = $republicanYeas;
    $tempArray['billNumber'] = $billNumber;
    $tempArray['voteResult'] = $voteResult;
    $tempArray['voteQuestion'] = $voteQuestion;
    $tempArray['vote'] = $vote;


} else if ($type == "Election") {

    $voteQuestion =  (string) $xml->{'vote-metadata'}->{'vote-question'};

    for ($i = 0; $i < 435; $i++) {

    if (($xml->{'vote-data'}->{'recorded-vote'}[$i]->{'legislator'}) == $person) {
        $vote =  (string) $xml->{'vote-data'}->{'recorded-vote'}[$i]->{'vote'};

        break;
    }

}

    $tempArray['vote'] = $vote;
    $tempArray['voteQuestion'] = $voteQuestion;

} else {

    $billNumber =  (string) $xml->{'vote-metadata'}->{'legis-num'};
    $billTitle =  (string)  $xml->{'vote-metadata'}->{'vote-desc'};
    $voteQuestion =  (string) $xml->{'vote-metadata'}->{'vote-question'};
    $voteResult =  (string) $xml->{'vote-metadata'}->{'vote-result'};
    $republicanYeas = (string) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[0]->{'yea-total'};
    $republicanNays = (string) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[0]->{'nay-total'};
    $democraticYeas = (string) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[1]->{'yea-total'};
    $democraticNays = (string) $xml->{'vote-metadata'}->{'vote-totals'}->{'totals-by-party'}[1]->{'nay-total'};

    for ($i = 0; $i < 435; $i++) {

    if (($xml->{'vote-data'}->{'recorded-vote'}[$i]->{'legislator'}) == $person) {
        $vote =  (string) $xml->{'vote-data'}->{'recorded-vote'}[$i]->{'vote'};

        break;
    }

}
    $tempArray['billTitle'] = $billTitle;
    $tempArray['billNumber'] = $billNumber;
    $tempArray['voteQuestion'] = $voteQuestion;
    $tempArray['voteResult'] = $voteResult;
    $tempArray['republicanYeas'] = $republicanYeas;
    $tempArray['republicanNays'] = $republicanNays;
    $tempArray['democraticYeas'] = $democraticYeas;
    $tempArray['democraticNays'] = $democraticNays;
    $tempArray['vote'] = $vote;

    $numDems = 191;
    $numReps = 240;

    $halfDems = round($numDems / 2);


    $halfReps = round($numReps / 2);



    if (($republicanYeas > $halfReps) && ($democraticYeas > $halfDems)) {
        $split = 0;
    } elseif (($republicanNays > $halfReps) && ($democraticNays > $halfDems)) {
        $split = 0;
    } else {
        $split = 1;
    }
    
    
    
    if ($democraticYeas > $halfDems && ($tempArray['vote']=="Nay" || $tempArray['vote']=="No")) {
        $delaneyDev = 1;
    } elseif ($democraticNays > $halfDems && ($tempArray['vote']=="Yea" || $tempArray['vote']=="Aye")) {
        $delaneyDev = 1;
    } else {
        $delaneyDev = 0;
    }


    $tempArray['splitByParty'] = $split;
    $tempArray['delaneyBreak'] = $delaneyDev;


}

    $tempArray['type'] = $type;
    $mainArray[$index] = $tempArray;


}

function getDates($date) {

    $dateVals = explode("-",$date);
    $monthsToNums = array("Jan" => 1,
        "Feb" => 2,
        "Mar" => 3,
        "Apr" => 4,
        "May" => 5,
        "Jun" => 6,
        "Jul" => 7,
        "Aug" => 8,
        "Sep" => 9,
        "Oct" => 10,
        "Nov" => 11,
        "Dec" => 12);

    if ($monthsToNums[$dateVals[1]] < 10) {
        $month = "0" . $monthsToNums[$dateVals[1]];
    } else {
        $month = $monthsToNums[$dateVals[1]];
    }

    if ($dateVals[0] < 10) {
        $day = "0" . $dateVals[0];
    } else {
        $day = $dateVals[0];
    }

    $dateString = $dateVals[2] . "-" . $month . "-" . $day;
    return $dateString;

}

//now, at this point we have main array with all the vote info indexed with rollcall num and then year

function indexToId($index) {
    $year = substr($index,strlen($index)-4);
    $num = substr($index,0,strlen($index)-4);

$host   = "localhost";
$db = "votes";
include("names.php");

$conn = new PDO("mysql:host=$host;dbname=$db",$user,$pass);

    $sql = "SELECT id from `rollcall` WHERE Year(`Date`) = $year AND `Number` = $num";
    $q   = $conn->query($sql); 
while($r = $q->fetch(PDO::FETCH_ASSOC)){


    foreach ($r as $key => $value)
 {
        return $value;
    }
 }


}

foreach ($mainArray as $key => $value) {
    $id = indexToId($key);

    if ($id == "") {
        createNewEntry($value);
    } else {
        entryExists($value);
    }
}


function createNewEntry($value) {


    $host   = "localhost";
$db = "votes";

include("names.php");

$conn = new PDO("mysql:host=$host;dbname=$db",$user,$pass);

    if ($value['type'] == "Quorum") {

        $date = $value['date'];
        $rcNum = $value['rollcallNum'];
        $billNum = $value['billNumber'];
        $voteOn = $value['voteQuestion'];
        $dv = $value['vote'];
        $result = $value['voteResult'];
        $ry = $value['republicanYeas'];
        $dy = $value['democraticYeas'];


         $sql = "INSERT INTO `votes`.`rollcall` (`id`, `Date`, `Number`, `Bill Number`, `Vote On`, `Passage`, `Republican Yeas`, `Democratic Yeas`,  `Delaney Vote`) 
VALUES (NULL, '$date', '$rcNum', '$billNum', '$voteOn', '$result', '$ry', '$dy', '$dv');";
      $q   = $conn->query($sql); 
         }

         elseif ($value['type'] == "Vacated") {
        $date = $value['date'];
        $rcNum = $value['rollcallNum'];
        $title = $value['billTitle'];
        $dv = $value['vote'];



         $sql = "INSERT INTO `votes`.`rollcall` (`id`, `Date`, `Number`, `Bill Title`, `Delaney Vote`) 
VALUES (NULL, '$date', '$rcNum', '$title', '$dv');";
      $q   = $conn->query($sql); 
         } elseif ($value['type'] == "Election") {

        $date = $value['date'];
        $rcNum = $value['rollcallNum'];
        $voteOn = $value['voteQuestion'];
        $dv = $value['vote'];



         $sql = "INSERT INTO `votes`.`rollcall` (`id`, `Date`, `Number`,  `Vote On`, `Delaney Vote`) 
VALUES (NULL, '$date', '$rcNum', '$voteOn','$dv');";
      $q   = $conn->query($sql); 
         } elseif ($value['type'] == "Amendment") {

        $date = $value['date'];
        $rcNum = $value['rollcallNum'];
        $billNum = $value['billNumber'];
        $title = $value['billTitle'];
        $author = $value['amendmentAuthor'];
        $amendmentNum = $value['amendmentNum'];
        $amendmentVal = 1;
        $voteOn = $value['voteQuestion'];
        $dv = $value['vote'];
        $result = $value['voteResult'];
        $ry = $value['republicanYeas'];
        $dy = $value['democraticYeas'];
        $rn = $value['republicanNays'];
        $dn = $value['democraticNays'];
        $split = $value['splitByParty'];
        $dev = $value['DelaneyBreak'];


         $sql = "INSERT INTO `votes`.`rollcall` (`id`, `Date`, `Number`, `Bill Number`, `Vote On`, `Passage`, `Republican Yeas`, `Republican Nays`, `Democratic Yeas`, `Democratic Nays`,  `Delaney Vote`, `Amendment`, `Amendment Author`, `Amendment Number`, `Parties Split`, `Delaney Split Dems`) 
VALUES (NULL, '$date', '$rcNum', '$billNum', '$voteOn', '$result', '$ry','$rn', '$dy','$dn', '$dv', '$amendmentVal', '$author','$amendmentNum','$split','$dev');";
      $q   = $conn->query($sql); 
         } else {

        $date = $value['date'];
        $rcNum = $value['rollcallNum'];
        $billNum = $value['billNumber'];
        $title = $value['billTitle'];
        $voteOn = $value['voteQuestion'];
        $dv = $value['vote'];
        $result = $value['voteResult'];
        $ry = $value['republicanYeas'];
        $dy = $value['democraticYeas'];
        $rn = $value['republicanNays'];
        $dn = $value['democraticNays'];
        $split = $value['splitByParty'];
        $dev = $value['DelaneyBreak'];


         $sql = "INSERT INTO `votes`.`rollcall` (`id`, `Date`, `Number`, `Bill Number`, `Bill Title`, `Vote On`, `Passage`, `Republican Yeas`, `Republican Nays`, `Democratic Yeas`, `Democratic Nays`,  `Delaney Vote`, `Parties Split`, `Delaney Split Dems`) 
VALUES (NULL, '$date', '$rcNum', '$billNum', '$title', '$voteOn', '$result', '$ry','$rn', '$dy','$dn', '$dv','$split','$dev');";
      $q   = $conn->query($sql); 
         }

}

function entryExists($value) {


}

$host   = "localhost";
$db = "votes";
include("names.php");
date_default_timezone_set('America/New_York');
$now = date("Y-m-d H:i:s");
$conn = new PDO("mysql:host=$host;dbname=$db",$user,$pass);
$sql = "INSERT INTO  `votes`.`Updates` (
`updateID` ,
`date`
)
VALUES (
NULL ,  '$now'
);";
 $q   = $conn->query($sql); 


print "<script type='text/javascript'>window.location = 'http://www.howdwevote.com/index.php?updated=1'</script>";



?>