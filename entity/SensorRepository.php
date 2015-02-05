<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 10/01/15
 * Time: 10:38 AM
 */

class SensorRepository
{


    public function getDates()
    {
        $link = Database::DbConnection();
        $query = "SELECT DISTINCT reportDate FROM sensor_value ORDER BY reportDate DESC";
        $result = $link->query($query) or die($link->error.__LINE__);
        $dates = null;
        while($row = $result->fetch_assoc())
        {
            $dates[] = $row['reportDate'];
        }
        Database::ConnectionClose($link);
        return $dates;
    }

    public function getTimesByDate($date)
    {
        $link = Database::DbConnection();
        $query = "SELECT DISTINCT reportTime FROM sensor_value WHERE reportDate='$date' ";
        $result = $link->query($query) or die($link->error.__LINE__);
        $times = null;
        while($row = $result->fetch_assoc())
        {
            $times[] = $row['reportTime'];
        }
        Database::ConnectionClose($link);
        return $times;
    }

    /**
     * @return array|null
     */
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
            $sensors[] = $sensor;
        }
        Database::ConnectionClose($link);
        return $sensors;
    }

    /**
     * @param $from_time
     * @param $duration_time
     * @param $date
     * @return array|null
     */
    public function getAllSensorsByTime($from_time,$duration_time,$date)
    {
        $link = Database::DbConnection();
        $endAt = $from_time + $duration_time;
        if($date == null)
        {
            $query = "SELECT * FROM sensor WHERE reportTime >= $from_time AND reportTime <= $endAt AND reportDate IS NOT NULL";
        }
        else
        {
            $query = "SELECT * FROM sensor WHERE reportTime >= $from_time AND reportTime <= $endAt AND reportDate = '$date'";
        }
        $result = $link->query($query) or die($link->error.__LINE__);
        $sensors = null;
        while($row = $result->fetch_assoc())
        {
            $sensor = new Sensor();
            $sensor->setSensorId($row['sensorId']);
            $sensor->setLongitude($row['longitude']);
            $sensor->setLatitude($row['latitude']);
            $sensors[] = $sensor;
        }
        Database::ConnectionClose($link);
        return $sensors;
    }

    /**
     * @param $sensorId
     * @return null|Sensor
     */
    public function getSensorById($sensorId)
    {
        $link = Database::DbConnection();
        $query = "SELECT * FROM sensor WHERE sensorId = '$sensorId'";
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

    /**
     * @param $from_time
     * @param $duration_time
     * @param $date
     * @param $sensorValue
     * @return null|Sensor
     */
    public function getSensorByValue($sensorValue)
    {
        $link = Database::DbConnection();
        $sensorId = $sensorValue->getSensorId();
        $query = "SELECT * FROM sensor WHERE sensorId='$sensorId'";
        $result = $link->query($query) or die($link->error.__LINE__);
        $s = null;
        $values[] = $sensorValue;

        while($row = $result->fetch_assoc())
        {
            $sensor = new Sensor();
            $sensor->setSensorId($row['sensorId']);
            $sensor->setLongitude($row['longitude']);
            $sensor->setLatitude($row['latitude']);
            $sensor->setSensorValues($values);
            $s = $sensor;
        }
        Database::ConnectionClose($link);
        return $s;

    }


    /**
     * @param $sensor
     * @return array|null
     */
    public function getSensorValues($sensor)
    {
        $link = Database::DbConnection();
        $sensorId = $sensor->getSensorId();
        $query = "SELECT * FROM sensor_value WHERE sensorId = '$sensorId'";
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

    public function getSensorValuesByDateTime($sensor,$date,$time)
    {
        $link = Database::DbConnection();
        $sensorId = $sensor->getSensorId();

        $query = "SELECT * FROM sensor_value WHERE sensorId = '$sensorId' AND reportTime = $time AND reportDate='$date' ";

        $result = $link->query($query) or die($link->error.__LINE__);
        $values = null;
        while($row = $result->fetch_assoc())
        {

            $measureName = SELF::getMeasureById($row['measureId']);
            $value = new SensorValue();
            $value->setSensorId($sensorId);
            $value->setDataValue($row['dataValue']);
            $value->setMeasureId($row['measureId']);
            $value->setReportDate($row['reportDate']);
            $value->setReportTime($row['reportTime']);
            $value->setMeasureName($measureName);
            $values[] = $value;
        }
        Database::ConnectionClose($link);
        return $values;

    }

    /**
     * @param $measureId
     * @return null
     */
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

    /**
     * @param $from_time
     * @param $duration_time
     * @param $date
     * @param $sensorId
     * @return null|Sensor
     */
    public function getSensorByIdTime($from_time,$duration_time,$date,$sensorId)
    {
        $link = Database::DbConnection();
        $endAt = $from_time + $duration_time;
        if($date == null)
        {
            $query = "SELECT * FROM sensor WHERE reportTime >= $from_time AND reportTime <= $endAt AND sensorId='$sensorId' AND reportDate IS NOT NULL";
        }
        else
        {
            $query = "SELECT * FROM sensor WHERE reportTime >= $from_time AND reportTime <= $endAt AND sensorId='$sensorId' AND reportDate='$date'";
        }
        $result = $link->query($query) or die($link->error.__LINE__);
        $s = null;

        while($row = $result->fetch_assoc())
        {
            $sensor = new Sensor();
            $sensor->setReportDate($row['reportDate']);
            $sensor->setSensorId($row['sensorId']);
            $sensor->setLongitude($row['longitude']);
            $sensor->setLatitude($row['latitude']);
            $sensor->setReportTime($row['reportTime']);
            $s = $sensor;
        }
        Database::ConnectionClose($link);
        return $s;
    }

    /**
     * @param $sensor
     * @return bool
     */
    public function insertSensor($sensor)
    {
        $link = Database::DbConnection();
        $sensorId = $sensor->getSensorId();
        $longitude = $sensor->getLongitude();
        $latitude = $sensor->getLatitude();
        $query = "INSERT INTO sensor VALUES('$sensorId',$longitude,$latitude)";
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