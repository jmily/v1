<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 10/01/15
 * Time: 10:38 AM
 */

class SensorRepository
{
    public function getAllSensors()
    {
        $link = Database::DbConnection();
        $query = "SELECT * FROM sensor ";
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensors = null;
        while($row = $result->fetch_assoc())
        {
            $sensor = new Sensor();
            $sensor->setSensorId($row['sensorId']);
            $sensor->setLongitude($row['longitude']);
            $sensor->setLatitude($row['latitude']);
            $sensor->setReportTime($row['reportTime']);
            $sensors[] = $sensor;
        }
        Database::ConnectionClose($link);
        return $sensors;
    }

    public function getAllSensorsByTime($from_time,$duration_time)
    {
        $link = Database::DbConnection();
        $endAt = $from_time + $duration_time;
        $query = "SELECT * FROM sensor WHERE reportTime > $from_time AND reportTime < $endAt";
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensors = null;
        while($row = $result->fetch_assoc())
        {
            $sensor = new Sensor();
            $sensor->setSensorId($row['sensorId']);
            $sensor->setLongitude($row['longitude']);
            $sensor->setLatitude($row['latitude']);
            $sensor->setReportTime($row['reportTime']);
            $sensors[] = $sensor;
        }
        Database::ConnectionClose($link);
        return $sensors;
    }

    public function getSensorById($sensorId)
    {
        $link = Database::DbConnection();
        $query = "SELECT * FROM sensor WHERE sensorId = $sensorId";
        $result = $link->query($query) or die($link->error.__LINE__);
        $s = null;
        while($row = $result->fetch_assoc())
        {
            $sensor = new Sensor();
            $sensor->setSensorId($row['sensorId']);
            $sensor->setLongitude($row['longitude']);
            $sensor->setLatitude($row['latitude']);
            $s = $sensor;
        }
        Database::ConnectionClose($link);
        return $s;
    }


    public function getSensorValues($sensor)
    {
        $link = Database::DbConnection();
        $sensorId = $sensor->getSensorId();
        $query = "SELECT * FROM sensor_value WHERE sensorId = $sensorId";
        $result = $link->query($query) or die($link->error.__LINE__);
        $values = null;
        while($row = $result->fetch_assoc())
        {

            $measureName = SELF::getMeasureById($row['measureId']);
            $value = new SensorValue();
            $value->setSensorId($sensorId);
            $value->setDataValue($row['dataValue']);
            $value->setMeasureId($row['measureId']);
            $value->setMeasureName($measureName);
            $values[] = $value;
        }
        Database::ConnectionClose($link);
        return $values;

    }

    public static function getMeasureById($measureId)
    {
        $link = Database::DbConnection();

        $query = "SELECT name FROM measure WHERE measureId = $measureId";
        $result = $link->query($query) or die($link->error.__LINE__);
        $name = null;
        while($row = $result->fetch_assoc())
        {
           $name = $row['name'];
        }
        Database::ConnectionClose($link);
        return $name;
    }

}