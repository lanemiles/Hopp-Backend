<?php

/*
Location Class
Updated 1/9/2015

This class is just used to provide a wrapper for the longitude, lattitude
and name of the user's location

*/

class Location {

	private $longitude;
	private $latitude;

	public function Location ($latitude, $longitude) {
		$this->longitude = $longitude;
		$this->latitude = $latitude;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function setLatitude($new) {
		$this->latitude = $new;
	}

	public function getCombinedCoordinates() {
		return $this->latitude . ", " . $this->longitude;
	}

	public function pointInPolygonFormat() {
        return $this->latitude . " " . $this->longitude;
    }

    public static function getLocationFromString($str) {
    	$latLong = explode(",", $str);
    	$lat = $latLong[0];
    	$longi = substr($latLong[1],1); 
    	return new Location($lat, $longi);
    }

}

?>