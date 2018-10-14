<?php

include ('database.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

header('Content-type: application/json');


function GetForecast(){

	$city = $_POST["city"];
			try{
			if (isset($city)){
				$response = file_get_contents("http://api.openweathermap.org/data/2.5/forecast?q=". $city .",jam&units=metric&APPID=483b5400f4501ce57d4b5df961ba0562"); //Query the endpoint
			}
			else{
				$response = file_get_contents('http://api.openweathermap.org/data/2.5/forecast?q=Kingston,jam&units=metric&APPID=483b5400f4501ce57d4b5df961ba0562');
			}
			}
			catch(Exception $e){
				echo 'Something went wrong!';
			}

			$array = json_decode($response,true); //Parse the json data received


			for ($x = 0; $x < $array["cnt"]; $x++) {
				$datetime = ($array["list"][$x]["dt_txt"]);
				$exact_time = explode(" ",$datetime);

				if (strcmp($exact_time[1],"12:00:00")==0){
					$weather_description = ($array["list"][$x]["weather"][0]["description"]);
			    	$weather_icon = ($array["list"][$x]["weather"][0]["icon"]);
					$temperature_info = ($array["list"][$x]["main"]["temp"]);
					$dayname = date('D', strtotime($datetime));
					$dateonly = $exact_time[0];
					$newArray[]=array("description"=>$weather_description,"icon"=>$weather_icon,"temperature"=>$temperature_info,"full_date"=>$datetime,"dayofweek"=>$dayname, "date_only"=>$dateonly); //Construct array of elements that will be used to display the forecast in the browser
				}
			}
	echo (json_encode(($newArray),128)); //Return array of elements on AJAX call

}
	GetForecast();

	/*PORTION OF SCRIPT RESPONSIBLE FOR EMAILING EMPLOYEES*/
	$weather_condition_lst = array();
	$db = new Database();
	$list = $db->getEmployees(); //Getting employee information from database

	for ($t = 0; $t < count($list); $t++){
		$employee_city = $list[$t][2];
		$response = file_get_contents("http://api.openweathermap.org/data/2.5/forecast?q=". $employee_city .",jam&units=metric&APPID=483b5400f4501ce57d4b5df961ba0562"); //Query the endpoint
		$array = json_decode($response,true);




		for ($i = 0; $i < $array["cnt"]; $i++){
			$datetime = ($array["list"][$i]["dt_txt"]);
			$exact_time = explode(" ",$datetime);

			if (strcmp($exact_time[1],"12:00:00")==0){
					$weather_condition = ($array["list"][$i]["weather"][0]["main"]);
					array_push($weather_condition_lst,$weather_condition);
			}

		}
			if ( (strcmp($weather_condition_lst[0],"Rain")==0) || (strcmp($weather_condition_lst[0],"Thunderstorm")==0) || (strcmp($weather_condition_lst[0],"Drizzle")==0) ) { //Constructing messages to be sent to employees based on the weather conditions and the employee roles
							$employee_role = $list[$t][4];
							$employee_email = $list[$t][1];

							if (strcmp($employee_role,"IT")==0){
								$message="We are expecting inclimate weather so you should stay off the streets.";
							}
							else{
								$message="Due to inclimate weather forecasts you will only be working for 4 hours instead of 8.";
							}

							$mail = new PHPMailer;
							$mail->isSMTP();
							$mail->Host = 'smtp.gmail.com'; //Using gmail as mail server
							$mail->Port = 587;
							$mail->SMTPSecure = 'tls';
							$mail->SMTPAuth = true;
							$mail->Username = "gennedykrace@gmail.com"; //Created gmail account credentials
							$mail->Password = "kracegennedy123";
							$mail->setFrom('gennedykrace@gmail.com');
							$mail->addAddress($employee_email);
							$mail->Subject = 'Schedule Change Advisory';
							$mail->msgHTML($message);
							$mail->send();

			}
			else if ( (strcmp($weather_condition_lst[0],"Clear")==0)){

							$employee_email = $list[$t][1];
							$mail = new PHPMailer;
							$mail->isSMTP();
							$mail->Host = 'smtp.gmail.com';
							$mail->Port = 587;
							$mail->SMTPSecure = 'tls';
							$mail->SMTPAuth = true;
							$mail->Username = "gennedykrace@gmail.com";
							$mail->Password = "kracegennedy123";
							$mail->setFrom('gennedykrace@gmail.com');
							$mail->addAddress($employee_email);
							$mail->Subject = 'Schedule Change Advisory';
							$mail->msgHTML("The weather forecast for tomorrow is fair so you will be working 8 hours.");
							$mail->send();

			}
}

?>