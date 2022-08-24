<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
$arClasses = array(
  'IblockClassHandler' => '/lib/Handlers/IblockClassHandler.php',
);
CModule::AddAutoloadClasses("dev.site", $arClasses);

