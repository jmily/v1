<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 26/01/15
 * Time: 9:20 AM
 */
$json = $_POST['json'];
//$json = json_decode($json,true);
print_r($json);

require_once 'entity/Sensor.php';
require_once 'entity/Measure.php';
require_once 'entity/SensorValue.php';
require_once 'entity/SensorRepository.php';
require_once 'entity/MeasureRepository.php';
require_once 'entity/SensorValueRepository.php';
require_once 'model/Database.php';
require_once 'Config.php';

foreach($json as $obj)
{
    $sensor = new Sensor();
    $sensor->setSensorId($obj['sensorId']);
    $sensor->setLatitude($obj['yCoord']);
    $sensor->setLongitude($obj['xCoord']);
    $sensor->setReportDate($obj['date']);
    $sensor->setReportTime($obj['time']);

    $sensorRepository = new SensorRepository();
    $flag = $sensorRepository->getSensorById($sensor->getSensorId());

    if($flag != null)
    {
        echo json_encode("sorry, sensorId: ".$sensor->getSensorId()." is not unique in the database");
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
                    $valueInsertSuccess = $sensorValueRepository->insertSensorValue($sensor->getSensorId(),$measure->getId(),$value);
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
                        $valueInsertSuccess = $sensorValueRepository->insertSensorValue($sensor->getSensorId(),$newMeasure->getId(),$value);
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

?>