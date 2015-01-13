<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 22/12/14
 * Time: 4:34 PM
 */
require 'vendor/autoload.php';
require_once 'Config.php';
require_once 'entity/Measure.php';
require_once 'entity/Sensor.php';
require_once 'model/Database.php';
require_once 'entity/MeasureRepository.php';
require_once 'entity/SensorRepository.php';
require_once 'entity/SensorValue.php';

$app = new \Slim\Slim();

$app->get('/measures/', function () {
    $measureRepository = new MeasureRepository();
    $allMeasures = $measureRepository->getAllMeasures();
    $output = null;
    foreach($allMeasures as $measure)
    {
        $output[] = $measure->getName();
    }

    header('Content-Type: application/json');
    echo json_encode($output);
});

$app->get('/sensors/', function () {
    $sensorRepository = new SensorRepository();
    $allSensors = $sensorRepository->getAllSensors();
    $output = null;
    foreach($allSensors as $sensor)
    {
            $output[] = array("sensorId"=>$sensor->getSensorId(),
                              "xCoord"=>$sensor->getLatitude(),
                              "yCoord"=>$sensor->getLongitude());
    }
    header('Content-Type: application/json');
    echo json_encode($output,JSON_PRETTY_PRINT);

   // print_r($allSensors);
    //echo sizeof($allSensors);

});

$app->get('/sensors/:sensorId/', function ($sensorId) {
    $sensorRepository = new SensorRepository();
    $sensor = $sensorRepository->getSensorById($sensorId);
    if($sensor == null)
    {
        echo "sorry, no such a sensorId in the database...";
    }
    else {
        $output = null;
        $output[] = array("sensorId" => $sensor->getSensorId(),
            "xCoord" => $sensor->getLatitude(),
            "yCoord" => $sensor->getLongitude());
        header('Content-Type: application/json');
        echo json_encode($output, JSON_PRETTY_PRINT);
    }
});


$app->get('/:streamId/values/:measure/', function ($streamId,$measure) use ($app) {
    date_default_timezone_set('Australia/Melbourne');
    if($streamId == 0) {
        $request = $app->request();
        $from_time = $request->get('from_time');
        $duration_time = $request->get('duration_time');
        $to_time = $request->get('to_time');

        $hour = date('G') * 3600;
        $minute = date('i') * 60;
        $second = date('s') * 1;

        $default_from_time = $hour+$minute+$second;
        $default_duration = 3600;

        if($from_time == null)
        {
            $from_time = $default_from_time;
        }
        if( ($duration_time == null) && ($to_time == null))
        {
            $duration_time = $default_duration;
        }
        if(($duration_time == null) &&($to_time !=null))
        {
            $duration_time = $to_time - $from_time;
        }

        //echo $from_time;

        if($measure == "allMeasures")
        {
            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensorsByTime($from_time,$duration_time);
            if($allSensors == null)
            {
                echo "no data found during the time period...";
            }
            else {
                $output = null;
                foreach ($allSensors as $sensor) {
                    $values = $sensorRepository->getSensorValues($sensor);
                    $sensor->setSensorValues($values);

                    $valueOutput = null;
                    foreach ($sensor->getSensorValues() as $value) {
                        $key = $value->getMeasureName();
                        $valueOutput[$key] = (double)$value->getDataValue();

                    }

                    $output[] = array(
                        "time" => $sensor->getReportTime(),
                        "sensorId" => $sensor->getSensorId(),
                        "values" => (object)$valueOutput
                    );

                }

                header('Content-Type: application/json');
                echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);

            }

        }
        else
        {
            
        }


    }
    else
    {
        echo "We are only using streamId = 0 as the default server currently!";
    }

});


$app->get('/:streamId/values/:measure/:sensorId/', function ($streamId,$measure,$sensorId) use ($app) {
    $request = $app->request();
    $from_time = $request->get('from_time');

    echo $from_time;

});


$app->run();