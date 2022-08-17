<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage("ONLY_VACANCIES_IMPORT_COMPONENT_NAME"),
    "DESCRIPTION" => Loc::getMessage("ONLY_VACANCIES_IMPORT_COMPONENT_DESCRIPTION"),
    "COMPLEX" => "N",
    "PATH" => [
        "ID" => Loc::getMessage("ONLY_VACANCIES_IMPORT_COMPONENT_PATH_ID"),
        "NAME" => Loc::getMessage("ONLY_VACANCIES_IMPORT_COMPONENT_PATH_NAME"),
    ],

];
?>
