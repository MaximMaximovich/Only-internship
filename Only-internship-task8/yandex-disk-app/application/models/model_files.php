<?php
require_once 'ydisk/YandexDisk.php';

class Model_Files extends Model
{
    function getDataArr()
    {
        $data = [];
        $disk = new YandexDisk();
        $collection = $disk->getAll();
        $i = 0;
        foreach ($collection as $item) {
            $data[$i]['name'] = $item['name'];
            $data[$i]['size'] = $this->formatBytes($item['size']);
            $data[$i]['created'] = $this->convertData($item['created']);
            $data[$i]['modified'] = $this->convertData($item['modified']);
            $data[$i]['path'] = $this->pathFormat($item['path']);
            $i++;
        }

        return $data;
    }

    function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

    function convertData($datetimeISO8601)
    {
        $time = strtotime($datetimeISO8601);
        return date("m/d/y g:i A", $time);
    }

    function pathFormat($path)
    {
        $subject = $path;
        $search = " ";
        $filePath = str_replace($search, "%20", $subject) ;
        $search = 'disk:';
        return str_replace($search, "", $filePath) ;
    }
}
