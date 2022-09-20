<?php
require_once 'ydisk/YandexDisk.php';
class Model_Delete extends Model
{
    function delete($path_file) {
        $disk = new YandexDisk();
        $disk->deleteFile($path_file);
    }
}