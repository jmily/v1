<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 13/01/15
 * Time: 1:14 PM
 */

class SensorValueRepository
{
    public function getAllSensorValueByMeasureDateTime($measure,$from_time,$duration_time,$date)
    {
        $link = Database::DbConnection();
        $measureId = $measure->getId();
        $endAt = $from_time + $duration_time;
        if($date == null)
        {
            $query = "SELECT * FROM sensor_value WHERE measureId=$measureId AND reportTime >= $from_time AND reportTime <= $endAt AND reportDate IS NOT NULL";
        }
        else
        {
            $query = "SELECT * FROM sensor_value WHERE measureId=$measureId AND reportTime >= $from_time AND reportTime <= $endAt AND reportDate='$date'";
        }

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
            $sensorValue->setReportTime($row['reportTime']);
            $sensorValue->setReportDate($row['reportDate']);
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

    public function getAllSensorValueBySensorDateTime($sensor,$from_time,$duration_time,$date)
    {
        $link = Database::DbConnection();
        $endAt = $from_time + $duration_time;
        $sensorId = $sensor->getSensorId();
        if($date == null)
        {
            $query = "SELECT * FROM sensor_value WHERE sensorId='$sensorId' AND reportTime >= $from_time AND reportTime <= $endAt AND reportDate IS NOT NULL";
        }
        else
        {
            $query = "SELECT * FROM sensor_value WHERE sensorId='$sensorId' reportTime >= $from_time AND reportTime <= $endAt AND reportDate = '$date'";
        }
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensorValuess = null;
        while($row = $result->fetch_assoc())
        {
            $measureName = SensorRepository::getMeasureById($row['measureId']);
            $sensorValue = new SensorValue();
            $sensorValue->setMeasureId($row['measureId']);
            $sensorValue->setReportDate($row['reportDate']);
            $sensorValue->setReportTime($row['reportTime']);
            $sensorValue->setSensorId($row['sensorId']);
            $sensorValue->setDataValue($row['dataValue']);
            $sensorValue->setMeasureName($measureName);
            $sensorValues[] = $sensorValue;
        }
        Database::ConnectionClose($link);
        return $sensorValues;
    }

    public function getAllSensorValueBySensorMeasureDateTime($sensor,$measure,$from_time,$duration_time,$date)
    {
        $link = Database::DbConnection();
        $endAt = $from_time + $duration_time;
        $sensorId = $sensor->getSensorId();
        $measureId = $measure->getId();
        if($date == null)
        {
            $query = "SELECT * FROM sensor_value WHERE sensorId='$sensorId' AND measureId= $measureId AND reportTime >= $from_time AND reportTime <= $endAt AND reportDate IS NOT NULL";
        }
        else
        $query = "SELECT * FROM sensor_value WHERE sensorId='$sensorId' AND measureId= $measureId AND reportTime >= $from_time AND reportTime <= $endAt AND reportDate= '$date'";
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
            $sensorValue->setReportDate($row['reportDate']);
            $sensorValue->setReportTime($row['reportTime']);
            $sensorValues[] = $sensorValue;

        }
        Database::ConnectionClose($link);
        return $sensorValues;

    }

    public function insertSensorValue($sensorId,$measureId,$dataValue,$reportDate,$reportTime)
    {
        $link = Database::DbConnection();
        $query = "INSERT INTO sensor_value VALUES('','$sensorId',$measureId,$dataValue,'$reportDate',$reportTime)";
        $result = $link->query($query) or die($link->error.__LINE__);
        $success = FALSE;
        if($link->affected_rows > 0)
        {
            $success = TRUE;
        }

        Database::ConnectionClose($link);
        return $success;

    }

}