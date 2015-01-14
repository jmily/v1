<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 13/01/15
 * Time: 1:14 PM
 */

class SensorValueRepository
{
    public function getAllSensorValueByMeasure($measure)
    {
        $link = Database::DbConnection();
        $measureId = $measure->getId();
        $query = "SELECT * FROM sensor_value WHERE measureId=$measureId";
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensorValues = null;
        while($row = $result->fetch_assoc())
        {
            $measureName = SensorRepository::getMeasureById($row['measureId']);
            $sensorValue = new SensorValue();
            $sensorValue->setDataValue($row['dataValue']);
            $sensorValue->setSensorId($row['sensorId']);
            $sensorValue->setMeasureId($measureId);
            $sensorValue->setMeasureName($measureName);
            $sensorValues[] = $sensorValue;

        }
        Database::ConnectionClose($link);
        return $sensorValues;
    }

    public function getAllSensorValueBySensor($sensor)
    {
        $link = Database::DbConnection();
        $sensorId = $sensor->getSensorId();
        $query = "SELECT * FROM sensor_value WHERE sensorId='$sensorId'";
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensorValues = null;
        while($row = $result->fetch_assoc())
        {
            $measureName = SensorRepository::getMeasureById($row['measureId']);
            $sensorValue = new SensorValue();
            $sensorValue->setDataValue($row['dataValue']);
            $sensorValue->setSensorId($row['sensorId']);
            $sensorValue->setMeasureId($row['measureId']);
            $sensorValue->setMeasureName($measureName);
            $sensorValues[] = $sensorValue;

        }
        Database::ConnectionClose($link);
        return $sensorValues;

    }

    public function getAllSensorValueBySensorMeasure($sensor,$measure)
    {
        $link = Database::DbConnection();
        $sensorId = $sensor->getSensorId();
        $measureId = $measure->getId();
        $query = "SELECT * FROM sensor_value WHERE sensorId='$sensorId' AND measureId='$measureId'";
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensorValues = null;
        while($row = $result->fetch_assoc())
        {
            $measureName = SensorRepository::getMeasureById($row['measureId']);
            $sensorValue = new SensorValue();
            $sensorValue->setDataValue($row['dataValue']);
            $sensorValue->setSensorId($row['sensorId']);
            $sensorValue->setMeasureId($row['measureId']);
            $sensorValue->setMeasureName($measureName);
            $sensorValues[] = $sensorValue;

        }
        Database::ConnectionClose($link);
        return $sensorValues;

    }

}