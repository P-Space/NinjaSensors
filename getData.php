<?php

//SensorFlare PHP Script
//This script is used at Patras' Hackerspace in order to get data (such as temperature, humidity, etc) 
//from sensors that are connected to a NinjaBlock and connect to SensorFlare

header('Content-type: application/json');

//function to check if a key exists and return its value
function check_get_value($key_name, $array_obj)
{
	$val="";

	if(array_key_exists($key_name, $array_obj))
		$val=$array_obj[$key_name];

	return $val;
}
		
$type="";

if(isset($_GET["type"]))
	$type=$_GET["type"];

$response = array();
$success="true";

//used for sensorflare.com
$temperatureSensorId=""; //temperature sensor id, as appeared on sensorflare's dashboard, e.g. 0112AA000635/0301/0/31
$humiditySensorId=""; //humidity sensor id, as appeared on sensorflare's dashboard
$authUsername=""; //enter your username here
$authPassword=""; //enter your password here
$sensorsApiURLpart1="http://www.sensorflare.com/api/resource/"; //api URL
$sensorsApiURLpart2="/report/latest"; //get the latest values.

//initialize cURL
$ch = curl_init();

if($type=="temperature")
{
	$url=$sensorsApiURLpart1.$temperatureSensorId.$sensorsApiURLpart2;
	$ispost=0;
	//cURL parameters for authentication
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "$authUsername:$authPassword");
}
elseif($type=="humidity")
{
	$url=$sensorsApiURLpart1.$humiditySensorId.$sensorsApiURLpart2;
	$ispost=0;
	//cURL parameters for authentication
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "$authUsername:$authPassword");
}

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, $ispost);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//execute post
$result = curl_exec($ch);

//see if everything went ok
$error_no = curl_errno($ch);

//close connection
curl_close($ch);

if ($error_no==0)
{
	$resp_obj=json_decode($result, true);
	
	$response['value']=check_get_value('latest', $resp_obj);
	$response['time']=check_get_value('latestTime', $resp_obj);
}
else
{
	$success="false";
}

echo json_encode(array("data"=>$response, "success"=>$success, "error"=>$error_no));

?>