<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

//формирование массива параметров
$arComponentParameters = array(
  "GROUPS" => array(
    "OPTIONS" => array(
      "NAME" => Loc::getMessage('ONLY_VACANCIES_IMPORT_OPTIONS_NAME'),
      "SORT" => "300",
    ),
  ),
  'PARAMETERS' => array(
    "POSITIONS_HL_NAME"    =>  array(
      "PARENT"    =>  "OPTIONS",
      "NAME"      =>  Loc::getMessage('ONLY_VACANCIES_IMPORT_PARAMETERS_IBLOCK_CODE'),
      "TYPE"      =>  "STRING",
      "DEFAULT"   =>  "VACANCIES"
    ),
  ),
);
?>




