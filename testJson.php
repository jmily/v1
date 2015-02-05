<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 27/01/15
 * Time: 8:39 PM
 */

$data = array(
               array
              (
               "date" => "2015-02-01", "time" => 56400,"xCoord"=>37.526,"yCoord"=>-143.89,"sensorId"=>"1","values"=>array
                (
                  "smoke"=>24.00,
                  "batery"=>19.20,
                  "temperature"=>14.2
                 )
           ),
           array
              (
               "date" => "2015-01-31", "time" => 56400,"xCoord"=>37.526,"yCoord"=>-143.89,"sensorId"=>"1","values"=>array
                (
                  "smoke"=>14.00,
                  "batery"=>18.20,
                  "temperature"=>18.2
                 )
           ),
           array
           (
               "date" => "2014-04-23", "time"=>86400, "xCoord"=>38.98, "yCoord"=>-147.890,"sensorId"=>"1","values"=>array
           (
               "smoke"=>13.90,
               "batery"=>20.20
           )
           ),
           array
           (
               "date"=>"2015-02-28", "time"=>82900, "xCoord"=>32.12, "yCoord"=>-143.234,"sensorId"=>"2","values"=>array
               (
                   "smoke"=>10.02
               )
           ),
          array(
               "date"=>"2015-01-31","time"=>82000,"xCoord"=>37.526,"yCoord"=>-143.89,"sensorId"=>"1","values"=>array
                (
                "temperature"=>20.15
                )
                )
           );

$data_string = json_encode($data);

//echo $data_string;

//$ch = curl_init('http://118.138.243.17/v1/json');

$ch = curl_init('http://localhost/v1/json');


curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Length: ' . strlen($data_string))
);

$result = curl_exec($ch);

echo $result;


