<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

\Bitrix\Main\Loader::registerAutoLoadClasses('only.cprop', [
  'CIBlockPropertyCProp' => 'lib/CIBlockPropertyCProp.php',
  'CUserTypeCProp' => 'lib/CUserTypeCProp.php',
]);