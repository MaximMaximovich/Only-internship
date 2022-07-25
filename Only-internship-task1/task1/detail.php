<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$APPLICATION->SetTitle("Детальная");

use Only\Helpers;
?>

<?
$APPLICATION->IncludeComponent(
  "bitrix:news.detail",
  "cust_news_detail",
  array(
    "IBLOCK_TYPE" => "test_pages",
    "IBLOCK_ID" => Helpers::getIBlockIdByCode('testpage1'),
    "ELEMENT_CODE" => $_REQUEST["ELEMENT_CODE"],
    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
    "ADD_SECTIONS_CHAIN" => "N",
    "SET_BROWSER_TITLE" => "Y",
    "SET_META_DESCRIPTION" => "Y",
    "SET_TITLE" => "Y",
    "ADD_ELEMENT_CHAIN" => "Y",
  ),
  false
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
