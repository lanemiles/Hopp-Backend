<?php

if (isset($_GET['year'])) {

	$year = $_GET['year'];

} else {

	$year = null;

}

$url = "http://clerk.house.gov/evs/" . $year . "/index.asp";
$html = file_get_contents($url);


$aPlace = strpos($html,"<A HREF");

$rest = substr($html,$aPlace+68,7);


$votes = preg_replace("/[^0-9]/", "", $rest);


$array = array();

$array['Number of Votes'] = $votes;

$final = json_encode($array);

print $final;




?>