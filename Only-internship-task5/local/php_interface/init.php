<?php
// composer autoload
use Bitrix\Main\Loader;
use Only\Site\Handlers\Iblock;

require_once __DIR__ . '/../composer/vendor/autoload.php';

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "addLog");
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "addLog");

function addLog($arFields)
{
    if (Loader::includeModule('only.site')) {
        (new Iblock)->addLog($arFields);
    }
}