<?php
require_once 'ydisk/YandexDisk.php';
class Model_Rename extends Model
{
    function rename($path, $name) {
        $disk = new YandexDisk();
        $disk->renameFile($path, $name);
    }
}
