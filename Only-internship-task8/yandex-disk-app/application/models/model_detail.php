<?php
require_once 'ydisk/YandexDisk.php';
class Model_Detail extends Model
{
    function readDetail($path_file) {
        $data = [];
        $disk = new YandexDisk();
        $arr = $disk->readFile($path_file);

        $data['name'] = $arr['name'];
        $data['preview'] = $arr['preview'];
        $data['size'] = $this->formatBytes($arr['size']);
        $data['created'] = $this->convertData($arr['created']);
        $data['modified'] = $this->convertData($arr['modified']);
        $data['path'] = $this->pathFormat($arr['path']);

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
