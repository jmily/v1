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
require_once 'entity/SensorValueRepository.php';
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
            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensorsByTime($from_time,$duration_time,$date);
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
                        "date" => $sensor->getReportDate(),
                        "time" => (int)$sensor->getReportTime(),
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

        //echo $from_time;

        if($measure == "allMeasures" )
        {
            $sensorRepository = new SensorRepository();
            $allSensors = $sensorRepository->getAllSensorsByTime($from_time,$duration_time,$date);

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
                        "date" => $sensor->getReportDate(),
                        "time" => (int)$sensor->getReportTime(),
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
            $measureRepository = new MeasureRepository();
            $sensorValueRepository = new SensorValueRepository();
            $sensorRepository = new SensorRepository();
            $m = $measureRepository->getMeasureByName($measure);
            $sensorValues = $sensorValueRepository->getAllSensorValueByMeasure($m);
            $output = null;
            $sensors = null;


            if($sensorValues != null)
            {
                foreach($sensorValues as $sensorValue)
                {
                    $sensor = $sensorRepository->getSensorByValueTime($from_time,$duration_time,$date,$sensorValue);
                    if($sensor != null)
                    {
                        $sensors[] = $sensor;
                    }

                }
                if($sensors != null)
                {
                    foreach($sensors as $sensor)
                    {
                        $values = $sensorRepository->getSensorValues($sensor);
                        $sensor->setSensorValues($values);

                        $valueOutput = null;
                        foreach ($sensor->getSensorValues() as $value)
                        {
                            $key = $value->getMeasureName();
                            if($key == $measure)
                            {
                               $valueOutput[$key] = (double)$value->getDataValue();
                            }

                        }
                        if($valueOutput !=null)
                        {
                            $output[] = array(
                                "date" => $sensor->getReportDate(),
                                "time" => (int)$sensor->getReportTime(),
                                "sensorId" => $sensor->getSensorId(),
                                "values" => (object)$valueOutput
                            );
                        }
                        else
                        {
                            $output = 'no data found...';
                        }

                    }
                }
            }
            else
            {
                $output = 'no data found...';
            }
            if($output == null)
            {
                $output = 'no data found...';
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

        if($measure == "allMeasures")
        {
            $sensorRepository = new SensorRepository();
            $sensorValueRepository = new SensorValueRepository();
            $sensor = $sensorRepository->getSensorByIdTime($from_time,$duration_time,$date,$sensorId);
            if($sensor != null) {
                $values = $sensorValueRepository->getAllSensorValueBySensor($sensor);
                $sensor->setSensorValues($values);

                $valueOutput = null;
                foreach ($sensor->getSensorValues() as $value) {
                    $key = $value->getMeasureName();
                    $valueOutput[$key] = (double)$value->getDataValue();

                }

                $output[] = array(
                    "date" => $sensor->getReportDate(),
                    "time" => (int)$sensor->getReportTime(),
                    "sensorId" => $sensor->getSensorId(),
                    "values" => (object)$valueOutput
                );

                header('Content-Type: application/json');
                echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);
            }
            else
            {
                echo 'no data found...';
            }
        }
        else if($measure == "allMeasure")
        {
            echo 'you may ask for allMeasures instead of allMeasure...';
        }

        else
        {
            $sensorRepository = new SensorRepository();
            $measureRepository = new MeasureRepository();
            $sensorValueRepository = new SensorValueRepository();
            $sensor = $sensorRepository->getSensorByIdTime($from_time,$duration_time,$date,$sensorId);
            if($sensor != null)
            {
                $m = $measureRepository->getMeasureByName($measure);
                $values = $sensorValueRepository->getAllSensorValueBySensorMeasure($sensor,$m);
                $sensor->setSensorValues($values);

                if($sensor->getSensorValues() != null)
                {
                    foreach ($sensor->getSensorValues() as $value)
                    {
                        $key = $value->getMeasureName();
                        if($key == $measure)
                        {
                            $valueOutput[$key] = (double)$value->getDataValue();
                        }

                    }
                    if($valueOutput !=null)
                    {
                        $output[] = array(
                            "date" => $sensor->getReportDate(),
                            "time" => (int)$sensor->getReportTime(),
                            "sensorId" => $sensor->getSensorId(),
                            "values" => (object)$valueOutput
                        );
                        header('Content-Type: application/json');
                        echo json_encode($output, JSON_PRETTY_PRINT, JSON_FORCE_OBJECT);
                    }
                }
                else
                {
                    echo 'no data found...';
                }


            }
            else
            {
                echo 'no data found...';
            }
        }


    }
    else
    {
        echo "We are only using streamId = 0 as the default server currently!";
    }

});


$app->run();