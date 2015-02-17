<?php

/*
Check how long background things run
Updated 2/15/2015


*/
$APPLICATION_ID = "0UW66oQJdG531qkgHXNjhcPRceTeVHG8hplMedDk";
				$REST_API_KEY = "FeGB6mwMC0XVVo2RepLXA3oIpHndyDODPWiwtm1M";
				$time = date("H:i");

				$url = 'https://api.parse.com/1/push';
				$data = array(
				    'channel' => 'global',
				    'data' => array(
				        'content-available' => "1"
				    ),
				);
				$_data = json_encode($data);
				$headers = array(
				    'X-Parse-Application-Id: ' . $APPLICATION_ID,
				    'X-Parse-REST-API-Key: ' . $REST_API_KEY,
				    'Content-Type: application/json',
				    'Content-Length: ' . strlen($_data),
				);

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_exec($curl);



?>

