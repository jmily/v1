<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 11/01/15
 * Time: 5:56 PM
 */

class SensorValue
{
    private $sensorId;
    private $measureId;
    private $dataValue;
    private $measureName;
    private $reportDate;
    private $reportTime;

    /**
     * @return mixed
     */
    public function getMeasureName()
    {
        return $this->measureName;
    }

    /**
     * @param mixed $measureName
     */
    public function setMeasureName($measureName)
    {
        $this->measureName = $measureName;
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
    public function getMeasureId()
    {
        return $this->measureId;
    }

    /**
     * @param mixed $measureId
     */
    public function setMeasureId($measureId)
    {
        $this->measureId = $measureId;
    }

    /**
     * @return mixed
     */
    public function getDataValue()
    {
        return $this->dataValue;
    }

    /**
     * @param mixed $dataValue
     */
    public function setDataValue($dataValue)
    {
        $this->dataValue = $dataValue;
    }

    /**
     * @return mixed
     */
    public function getReportDate()
    {
        return $this->reportDate;
    }

    /**
     * @param mixed $reportDate
     */
    public function setReportDate($reportDate)
    {
        $this->reportDate = $reportDate;
    }

    /**
     * @return mixed
     */
    public function getReportTime()
    {
        return $this->reportTime;
    }

    /**
     * @param mixed $reportTime
     */
    public function setReportTime($reportTime)
    {
        $this->reportTime = $reportTime;
    }


}