<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 10/01/15
 * Time: 10:30 AM
 */

class Sensor
{
    private $sensorId;
    private $longitude;
    private $latitude;
    private $sensorValues;

    public function __construct()
    {

    }


    /**
     * @return mixed
     */
    public function getSensorId()
    {
        return $this->sensorId;
    }

    /**
     * @param mixed $sensorId
     */
    public function setSensorId($sensorId)
    {
        $this->sensorId = $sensorId;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }


    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getSensorValues()
    {
        return $this->sensorValues;
    }

    /**
     * @param mixed $sensorValues
     */
    public function setSensorValues($sensorValues)
    {
        $this->sensorValues = $sensorValues;
    }



}