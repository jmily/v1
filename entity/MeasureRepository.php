<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 22/12/14
 * Time: 4:52 PM
 */

class MeasureRepository
{
    public function getAllMeasures()
    {
        $link = Database::DbConnection();
        $query = "SELECT * FROM measure ";
        $result = $link->query($query) or die($link->error.__LINE__);
        $measures = null;
        while($row = $result->fetch_assoc())
        {
            $measure = new Measure();
            $measure->setId($row['measureId']);
            $measure->setName($row['name']);
            $measures[] = $measure;
        }
        Database::ConnectionClose($link);
        return $measures;
    }
}