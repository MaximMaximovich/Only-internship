<?php
require_once 'ydisk/YandexDisk.php';
class Model_Upload extends Model
{
    function upload($file) {
        $disk = new YandexDisk();
        $disk->createFile($file);
    }
}
