<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

// Фильтр (пример) вывода элементов только для ИБ №6
global $arrFilter;
$arFilter = array("=IBLOCK_ID" => 6);


$APPLICATION->IncludeComponent(
  "only:iblock.list",
  "",
  array(
//    "IBLOCK_ID" => 6,
    "IBLOCK_TYPE" => "test_pages",
    "ACTIVE_DATE_FORMAT" => "d.m.Y",
    "AJAX_MODE" => "N",
    "AJAX_OPTION_ADDITIONAL" => "",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "CACHE_FILTER" => "N",
    "CACHE_GROUPS" => "Y",
    "CACHE_TIME" => "36000000",
    "CACHE_TYPE" => "A",
    "CHECK_DATES" => "Y",
    "DISPLAY_DATE" => "Y",
    "DISPLAY_NAME" => "Y",
    "DISPLAY_PICTURE" => "N",
    "DISPLAY_PREVIEW_TEXT" => "Y",
    "FIELD_CODE" => array("SHOW_COUNTER",""),
    "FILTER_NAME" => "arFilter",
    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
    "PREVIEW_TRUNCATE_LEN" => "",
    "SET_BROWSER_TITLE" => "Y",
    "SET_LAST_MODIFIED" => "N",
    "SET_META_DESCRIPTION" => "Y",
    "SET_META_KEYWORDS" => "Y",
    "SET_TITLE" => "Y",
    "SORT_BY1" => "SORT",
    "SORT_BY2" => "SORT",
    "SORT_ORDER1" => "DESC",
    "SORT_ORDER2" => "ASC",
  )
);


require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';