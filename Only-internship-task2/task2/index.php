<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Задание 2");
?>

<? $APPLICATION->IncludeComponent(
  "bitrix:form.result.new",
  "custom_form",
  array(
    "SEF_MODE" => "Y",
    "WEB_FORM_ID" => "1",
    "LIST_URL" => "",
    "EDIT_URL" => "",
    "SUCCESS_URL" => "/success.php",
    "CHAIN_ITEM_TEXT" => "",
    "CHAIN_ITEM_LINK" => "",
    "IGNORE_CUSTOM_TEMPLATE" => "Y",
    "USE_EXTENDED_ERRORS" => "Y",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "3600",
    "SEF_FOLDER" => "/",
    "VARIABLE_ALIASES" => array(),

  )
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>