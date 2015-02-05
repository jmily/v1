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
require_once 'entity/SensorValue.php';
require_once 'model/Database.php';
require_once 'entity/MeasureRepository.php';
require_once 'entity/SensorRepository.php';
require_once 'entity/SensorValueRepository.php';


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
                              "xCoord"=>(double)$sensor->getLatitude(),
                              "yCoord"=>(double)$sensor->getLongitude());
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
            "xCoord" => (double)$sensor->getLatitude(),
            "yCoord" => (double)$sensor->getLongitude());
        header('Content-Type: application/json');
        echo json_encode($output, JSON_PRETTY_PRINT);
    }
});

$app->get('/:streamId/values/', function ($streamId) use ($app) {
    date_default_timezone_set('Australia/Melbourne');
    if($streamId == 0) {
        $request = $app->request();
        $from_time = $request->get('from_time');
        $duration_time = $request->get('duration_time');
        $to_time = $request->get('to_time');
        $date = $request->get('date');

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
        $endAt = $from_time + $duration_time;

        $sensorRepository = new SensorRepository();
        $allSensors = $sensorRepository->getAllSensors();

        if($allSensors == null)
        {
            echo "no data found during the time period...";
        }
        else {
            $output = null;
            foreach ($allSensors as $sensor) {
                $dateList = $sensorRepository->getDates();

                foreach($dateList as $day)
                {
                    $times = $sensorRepository->getTimesByDate($day);
                    foreach($times as $time)
                    {
                        $values = $sensorRepository->getSensorValuesByDateTime($sensor,$day,$time);
                        $sensor->setSensorValues($values);
                        if($sensor->getSensorValues() != null)
                        {
                            $valueOutput = null;
                            foreach($sensor->getSensorValues() as $value)
                            {
                                $key = $value->getMeasureName();

                                $reportTime = $value->getReportTime();


                                if( ($reportTime <= $endAt) && ($reportTime >= $from_time))
                                {
                                    $valueOutput[$key] = (double)$value->getDataValue();
                                }

                            }

                            if($valueOutput != null)
                            {
                                if($date != null)
                                {
                                    if($day == $date)
                                    {
                                        $output[] = array(
                                            "date" => $day,
                                            "time" => (int)$time,
                                            "sensorId" => $sensor->getSensorId(),
                                            "values" => (object)$valueOutput
                                        );
                                    }
                                }
                                else
                                {
                                    $output[] = array(
                                        "date" => $day,
                                        "time" => (int)$time,
                                        "sensorId" => $sensor->getSensorId(),
                                        "values" => (object)$valueOutput
                                    );
                                }
                            }
                        }

                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);

        }

    }
    else
    {
        echo "We are only using streamId = 0 as the default server currently!";
    }

});

$app->get('/:streamId/values/:measure/', function ($streamId,$measure) use ($app) {
    date_default_timezone_set('Australia/Melbourne');
    if($streamId == 0) {
        $request = $app->request();
        $from_time = $request->get('from_time');
        $duration_time = $request->get('duration_time');
        $to_time = $request->get('to_time');
        $date = $request->get('date');

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

        $endAt = $from_time + $duration_time;

        //echo $from_time;

        if($measure == "allMeasures" )
        {
            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensors();

            if($allSensors == null)
            {
                echo "no data found during the time period...";
            }
            else {
                $output = null;
                foreach ($allSensors as $sensor) {
                    $dateList = $sensorRepository->getDates();

                    foreach($dateList as $day)
                    {
                        $times = $sensorRepository->getTimesByDate($day);
                        foreach($times as $time)
                        {
                            $values = $sensorRepository->getSensorValuesByDateTime($sensor,$day,$time);
                            $sensor->setSensorValues($values);
                            if($sensor->getSensorValues() != null)
                            {
                                $valueOutput = null;
                                foreach($sensor->getSensorValues() as $value)
                                {
                                    $key = $value->getMeasureName();

                                    $reportTime = $value->getReportTime();


                                        if( ($reportTime <= $endAt) && ($reportTime >= $from_time))
                                        {
                                            $valueOutput[$key] = (double)$value->getDataValue();
                                        }

                                }

                                if($valueOutput != null)
                                {
                                    if($date != null)
                                    {
                                        if($day == $date)
                                        {
                                        $output[] = array(
                                            "date" => $day,
                                            "time" => (int)$time,
                                            "sensorId" => $sensor->getSensorId(),
                                            "values" => (object)$valueOutput
                                        );
                                        }
                                    }
                                    else
                                    {
                                        $output[] = array(
                                            "date" => $day,
                                            "time" => (int)$time,
                                            "sensorId" => $sensor->getSensorId(),
                                            "values" => (object)$valueOutput
                                        );
                                    }
                                }
                            }

                        }
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);

            }

        }

        else
        {

            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensors();

            if($allSensors == null)
            {
                echo "no data found during the time period...";
            }
            else {
                $output = null;
                foreach ($allSensors as $sensor) {
                    $dateList = $sensorRepository->getDates();

                    foreach($dateList as $day)
                    {

                        $times = $sensorRepository->getTimesByDate($day);
                        foreach($times as $time)
                        {
                            $values = $sensorRepository->getSensorValuesByDateTime($sensor,$day,$time);
                            $sensor->setSensorValues($values);
                            if($sensor->getSensorValues() != null)
                            {
                                $valueOutput = null;
                                foreach($sensor->getSensorValues() as $value)
                                {
                                    $key = $value->getMeasureName();

                                    $reportTime = $value->getReportTime();


                                    if( ($reportTime <= $endAt) && ($reportTime >= $from_time) && ($key == $measure) )
                                    {
                                        $valueOutput[$key] = (double)$value->getDataValue();
                                    }

                                }

                                if($valueOutput != null)
                                {
                                    if($date != null)
                                    {

                                        if($day == $date)
                                        {
                                            $output[] = array(
                                                "date" => $day,
                                                "time" => (int)$time,
                                                "sensorId" => $sensor->getSensorId(),
                                                "values" => (object)$valueOutput
                                            );
                                        }
                                    }
                                    else
                                    {

                                        $output[] = array(
                                            "date" => $day,
                                            "time" => (int)$time,
                                            "sensorId" => $sensor->getSensorId(),
                                            "values" => (object)$valueOutput
                                        );
                                    }
                                }
                            }

                        }
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);

            }

            
        }


    }
    else
    {
        echo "We are only using streamId = 0 as the default server currently!";
    }

});


// continuing...
$app->get('/:streamId/values/:measure/:sensorId/', function ($streamId,$measure,$sensorId) use ($app) {
    date_default_timezone_set('Australia/Melbourne');
    if($streamId == 0) {
        $request = $app->request();
        $from_time = $request->get('from_time');
        $duration_time = $request->get('duration_time');
        $to_time = $request->get('to_time');
        $date = $request->get('date');

        $hour = date('G') * 3600;
        $minute = date('i') * 60;
        $second = date('s') * 1;

        $default_from_time = $hour + $minute + $second;
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

        $endAt = $from_time + $duration_time;

        if($measure == "allMeasures")
        {

            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensors();

            if($allSensors == null)
            {
                echo "no data found during the time period...";
            }
            else {
                $output = null;
                   $sensor = $sensorRepository->getSensorById($sensorId);
                    $dateList = $sensorRepository->getDates();
                    foreach($dateList as $day)
                    {
                        $times = $sensorRepository->getTimesByDate($day);
                        foreach($times as $time)
                        {
                            $values = $sensorRepository->getSensorValuesByDateTime($sensor,$day,$time);
                            $sensor->setSensorValues($values);
                            if($sensor->getSensorValues() != null)
                            {
                                $valueOutput = null;
                                foreach($sensor->getSensorValues() as $value)
                                {
                                    $key = $value->getMeasureName();

                                    $reportTime = $value->getReportTime();


                                    if( ($reportTime <= $endAt) && ($reportTime >= $from_time))
                                    {
                                        $valueOutput[$key] = (double)$value->getDataValue();
                                    }

                                }

                                if($valueOutput != null)
                                {
                                    if($date != null)
                                    {
                                        if($day == $date)
                                        {
                                            $output[] = array(
                                                "date" => $day,
                                                "time" => (int)$time,
                                                "sensorId" => $sensor->getSensorId(),
                                                "values" => (object)$valueOutput
                                            );
                                        }
                                    }
                                    else
                                    {
                                        $output[] = array(
                                            "date" => $day,
                                            "time" => (int)$time,
                                            "sensorId" => $sensor->getSensorId(),
                                            "values" => (object)$valueOutput
                                        );
                                    }
                                }
                            }

                        }
                    }


                header('Content-Type: application/json');
                echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);

            }
        }
        else if($measure == "allMeasure")
        {
            echo 'you may ask for allMeasures instead of allMeasure...';
        }

        else
        {

            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensors();

            if($allSensors == null)
            {
                echo "no data found during the time period...";
            }
            else {
                $output = null;
                $sensor = $sensorRepository->getSensorById($sensorId);
                    $dateList = $sensorRepository->getDates();

                    foreach($dateList as $day)
                    {

                        $times = $sensorRepository->getTimesByDate($day);
                        foreach($times as $time)
                        {
                            $values = $sensorRepository->getSensorValuesByDateTime($sensor,$day,$time);
                            $sensor->setSensorValues($values);
                            if($sensor->getSensorValues() != null)
                            {
                                $valueOutput = null;
                                foreach($sensor->getSensorValues() as $value)
                                {
                                    $key = $value->getMeasureName();

                                    $reportTime = $value->getReportTime();


                                    if( ($reportTime <= $endAt) && ($reportTime >= $from_time) && ($key == $measure) )
                                    {
                                        $valueOutput[$key] = (double)$value->getDataValue();
                                    }

                                }

                                if($valueOutput != null)
                                {
                                    if($date != null)
                                    {

                                        if($day == $date)
                                        {
                                            $output[] = array(
                                                "date" => $day,
                                                "time" => (int)$time,
                                                "sensorId" => $sensor->getSensorId(),
                                                "values" => (object)$valueOutput
                                            );
                                        }
                                    }
                                    else
                                    {

                                        $output[] = array(
                                            "date" => $day,
                                            "time" => (int)$time,
                                            "sensorId" => $sensor->getSensorId(),
                                            "values" => (object)$valueOutput
                                        );
                                    }
                                }
                            }

                        }
                    }


                header('Content-Type: application/json');
                echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);

            }
        }


    }
    else
    {
        echo "We are only using streamId = 0 as the default server currently!";
    }

});

$app->post('/json',function() use($app)
{
    $json = json_decode($app->request->getBody(),true);



    foreach($json as $obj)
    {
        $sensor = new Sensor();
        $sensor->setSensorId($obj['sensorId']);
        $sensor->setLatitude($obj['yCoord']);
        $sensor->setLongitude($obj['xCoord']);
        $reportDate = $obj['date'];
        $reportTime = $obj['time'];


        $sensorRepository = new SensorRepository();
        $flag = $sensorRepository->getSensorById($sensor->getSensorId());
       // echo "hellp";

        if($flag != null)
        {
            $sensorId = $flag->getSensorId();
            foreach($obj['values'] as $key => $value)
            {
                $measureRepository = new MeasureRepository();
                $sensorValueRepository = new SensorValueRepository();
                $measure = $measureRepository->getMeasureByName($key);
                if($measure != null)
                {
                    $valueInsertSuccess = $sensorValueRepository->insertSensorValue($sensorId,$measure->getId(),$value,$reportDate,$reportTime);
                    if(!$valueInsertSuccess)
                    {
                        echo 'Mysql Error when inserting data value, please check the query';
                    }

                }
                else if($measure == null)
                {
                    $measureInsert = $measureRepository->insertMeasure($key);
                    if(!$measureInsert)
                    {
                        echo 'Mysql Error when inserting data value, please check the query';
                    }
                    else
                    {
                        $newMeasure = $measureRepository->getMeasureByName($key);
                        $valueInsertSuccess = $sensorValueRepository->insertSensorValue($sensorId,$newMeasure->getId(),$value,$reportDate,$reportTime);
                        if(!$valueInsertSuccess)
                        {
                            echo 'Mysql Error when inserting data value, please check the query';
                        }
                    }


                }
            }

        }
        else if($flag == null)
        {
            $insertSuccess = $sensorRepository->insertSensor($sensor);
            if(!$insertSuccess)
            {
                echo 'Mysql Error, please check the query';
            }
            else
            {
                foreach($obj['values'] as $key => $value)
                {
                    $measureRepository = new MeasureRepository();
                    $sensorValueRepository = new SensorValueRepository();
                    $measure = $measureRepository->getMeasureByName($key);
                    if($measure != null)
                    {
                        $valueInsertSuccess = $sensorValueRepository->insertSensorValue($sensor->getSensorId(),$measure->getId(),$value,$reportDate,$reportTime);
                        if(!$valueInsertSuccess)
                        {
                            echo 'Mysql Error when inserting data value, please check the query';
                        }

                    }
                    else if($measure == null)
                    {
                        $measureInsert = $measureRepository->insertMeasure($key);
                        if(!$measureInsert)
                        {
                            echo 'Mysql Error when inserting data value, please check the query';
                        }
                        else
                        {
                            $newMeasure = $measureRepository->getMeasureByName($key);
                            $valueInsertSuccess = $sensorValueRepository->insertSensorValue($sensor->getSensorId(),$newMeasure->getId(),$value,$reportDate,$reportTime);
                            if(!$valueInsertSuccess)
                            {
                                echo 'Mysql Error when inserting data value, please check the query';
                            }
                        }


                    }
                }

            }
        }

    }
});

$app->run();