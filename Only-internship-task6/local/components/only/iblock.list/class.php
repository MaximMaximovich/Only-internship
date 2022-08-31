<?php

namespace Only\Components;

use Bitrix\Iblock;
use \Bitrix\Main\Loader;
use \CBitrixComponent;
use CDatabase;
use CIBlock;
use \CIBlockElement;
use CIBlockFormatProperties;
use CPageOption;
use CSite;
use CTextParser;
use CUser;
use DateTime;
use \Exception;

/**
 * Class IblockListComponent
 * @package YLab\Components
 * Компонент отображения списка элементов ИБ
 */
class IblockListComponent extends CBitrixComponent
{

    /** @var boolean $bUSER_HAVE_ACCESS флаг доступа пользователя */
    private $bUSER_HAVE_ACCESS;
    /** @var array $arrFilter массив фильтра по полям */
    private $arrFilter;
    /** @var array $arNavParams параметры навигации ИБ */
    private $arNavParams;


    /**
     * @return CUser
     * Обёртка для удобства использования над глобальной переменной $USER
     */
    private function user()
    {
        global $USER;
        return $USER;
    }

    /**
     * @return CDatabase
     * Обёртка для удобства использования над глобальной переменной $DB
     */
    private function db()
    {
        global $DB;
        return $DB;
    }


    /**
     * Метод для подготовки параметров
     *
     * @param $arParams
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function onPrepareComponentParams($arParams)
    {
        CPageOption::SetOptionString("main", "nav_page_in_session", "N");

        if (!isset($arParams["CACHE_TIME"]))
            $arParams["CACHE_TIME"] = 36000000;

        $arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);

        $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
        $arParams["SET_LAST_MODIFIED"] = $arParams["SET_LAST_MODIFIED"] === "Y";

        $arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
        if ($arParams["SORT_BY1"] == '')
            $arParams["SORT_BY1"] = "ACTIVE_FROM";
        if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
            $arParams["SORT_ORDER1"] = "DESC";

        if ($arParams["SORT_BY2"] == '') {
            if (mb_strtoupper($arParams["SORT_BY1"]) == 'SORT') {
                $arParams["SORT_BY2"] = "ID";
                $arParams["SORT_ORDER2"] = "DESC";
            } else {
                $arParams["SORT_BY2"] = "SORT";
            }
        }
        if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
            $arParams["SORT_ORDER2"] = "ASC";

        if ($arParams["FILTER_NAME"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
            $this->arrFilter = array();
        } else {
            $this->arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];

            if (!is_array($this->arrFilter))
                $this->arrFilter = array();
        }

        $arParams["CHECK_DATES"] = $arParams["CHECK_DATES"] != "N";

        if (!is_array($arParams["FIELD_CODE"]))
            $arParams["FIELD_CODE"] = array();
        foreach ($arParams["FIELD_CODE"] as $key => $val)
            if (!$val)
                unset($arParams["FIELD_CODE"][$key]);

        if (empty($arParams["PROPERTY_CODE"]) || !is_array($arParams["PROPERTY_CODE"]))
            $arParams["PROPERTY_CODE"] = array();
        foreach ($arParams["PROPERTY_CODE"] as $key => $val)
            if ($val === "")
                unset($arParams["PROPERTY_CODE"][$key]);


        $arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
        if ($arParams["NEWS_COUNT"] <= 0)
            $arParams["NEWS_COUNT"] = 20;

        $arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"] == "Y";
        if (!$arParams["CACHE_FILTER"] && count($this->arrFilter) > 0)
            $arParams["CACHE_TIME"] = 0;

        $arParams["SET_TITLE"] = $arParams["SET_TITLE"] != "N";
        $arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
        $arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
        $arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
        $arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);

        if ($arParams["ACTIVE_DATE_FORMAT"] == '')
            $arParams["ACTIVE_DATE_FORMAT"] = $this->db()->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
        $arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
        $arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"] == "Y";


        $arParams["CHECK_PERMISSIONS"] = ($arParams["CHECK_PERMISSIONS"] ?? '') != "N";

        $this->arNavParams = array(
          "nTopCount" => $arParams["NEWS_COUNT"],
          "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
          "bShowAll" => "Y"
        );

        $arParams["USE_PERMISSIONS"] = ($arParams["USE_PERMISSIONS"] ?? '') == "Y";
        if (!is_array(($arParams["GROUP_PERMISSIONS"] ?? null)))
            $arParams["GROUP_PERMISSIONS"] = array(1);

        $this->bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];

        if ($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"])) {
            $arUserGroupArray = $this->user()->GetUserGroupArray();
            foreach ($arParams["GROUP_PERMISSIONS"] as $PERM) {
                if (in_array($PERM, $arUserGroupArray)) {
                    $this->bUSER_HAVE_ACCESS = true;
                    break;
                }
            }
        }

        return $arParams;
    }

    /**
     * Метод executeComponent
     *
     * @return mixed|void
     * @throws Exception
     */
    public function executeComponent()
    {
        if ($this->startResultCache(
          false,
          array(($this->arParams["CACHE_GROUPS"] === "N" ? false : $this->user()->GetGroups()),
            $this->bUSER_HAVE_ACCESS,
            $this->arrFilter,
          )
        )) {
            if (!Loader::includeModule("iblock")) {
                $this->abortResultCache();
                ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
                return;
            }

            if (is_numeric($this->arParams["IBLOCK_ID"])) {
                $rsIBlock = CIBlock::GetList(array(), array(
                  "ACTIVE" => "Y",
                  "ID" => $this->arParams["IBLOCK_ID"],
                ));
            } elseif (!is_numeric($this->arParams["IBLOCK_ID"]) && !empty($this->arParams["IBLOCK_TYPE"])) {
                $rsIBlock = CIBlock::GetList(array(), array(
                  "ACTIVE" => "Y",
                  "TYPE" => $this->arParams["IBLOCK_TYPE"],
                ));
            } else {
                $rsIBlock = CIBlock::GetList(array(), array(
                  "ACTIVE" => "Y",
                  "CODE" => $this->arParams["IBLOCK_ID"],
                  "SITE_ID" => SITE_ID,
                ));

            }

            $this->arResult["ITEMS"] = array();

            while ($res = $rsIBlock->GetNext()) {
                // группировка элементов по ID инфоблоков, из которых они были полученны
                $id = (int)$res['ID'];
                $this->arResult["ITEMS"][$id] = $this->getItems($res);
            }

            $this->includeComponentTemplate();
        }
    }


    /**
     * Метод возвращающий массив элементов инфоблока arResult["ITEMS"]
     *
     * @param array $res
     * @return array
     */
    public function getItems(array $res)
    {
        $res["USER_HAVE_ACCESS"] = $this->bUSER_HAVE_ACCESS;
        //SELECT
        $arSelect = array_merge($this->arParams["FIELD_CODE"], array(
          "ID",
          "IBLOCK_ID",
          "NAME",
          "ACTIVE_FROM",
          "TIMESTAMP_X",
          "DETAIL_PAGE_URL",
          "LIST_PAGE_URL",
          "DETAIL_TEXT",
          "DETAIL_TEXT_TYPE",
          "PREVIEW_TEXT",
          "PREVIEW_TEXT_TYPE",
          "PREVIEW_PICTURE",
        ));
        $bGetProperty = !empty($this->arParams["PROPERTY_CODE"]);
        //WHERE
        $arFilter = array(
          "IBLOCK_ID" => $res["ID"],
          "IBLOCK_LID" => SITE_ID,
          "ACTIVE" => "Y",
          "CHECK_PERMISSIONS" => $this->arParams['CHECK_PERMISSIONS'] ? "Y" : "N",
        );

        //ORDER BY
        $arSort = array(
          $this->arParams["SORT_BY1"] => $this->arParams["SORT_ORDER1"],
          $this->arParams["SORT_BY2"] => $this->arParams["SORT_ORDER2"],
        );
        if (!array_key_exists("ID", $arSort))
            $arSort["ID"] = "DESC";

        $shortSelect = array('ID', 'IBLOCK_ID');

        foreach (array_keys($arSort) as $index) {
            if (!in_array($index, $shortSelect)) {
                $shortSelect[] = $index;
            }
        }

        $listPageUrl = '';
        $arResult["ITEMS"] = array();
        $arResult["ELEMENTS"] = array();

        $rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter, $this->arrFilter), false, $this->arNavParams, $shortSelect);
        while ($row = $rsElement->Fetch()) {
            $id = (int)$row['ID'];
            $arResult["ITEMS"][$id] = $row;
            $arResult["ELEMENTS"][] = $id;
        }
        unset($row);


        if (!empty($arResult['ITEMS'])) {
            $elementFilter = array(
              "IBLOCK_ID" => $arResult["ID"],
              "IBLOCK_LID" => SITE_ID,
              "ID" => $arResult["ELEMENTS"]
            );
            if (isset($this->arrFilter['SHOW_NEW'])) {
                $elementFilter['SHOW_NEW'] = $this->arrFilter['SHOW_NEW'];
            }

            $obParser = new CTextParser;

            // выборка элементов по $elementFilter и $arSelect
            $iterator = CIBlockElement::GetList(array(), $elementFilter, false, false, $arSelect);


            while ($arItem = $iterator->GetNext()) {

                if ($this->arParams["PREVIEW_TRUNCATE_LEN"] > 0)
                    $arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $this->arParams["PREVIEW_TRUNCATE_LEN"]);

                if ($arItem["ACTIVE_FROM"] <> '')
                    $arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($this->arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
                else
                    $arItem["DISPLAY_ACTIVE_FROM"] = "";

                Iblock\InheritedProperty\ElementValues::queue($arItem["IBLOCK_ID"], $arItem["ID"]);

                $arItem["FIELDS"] = array();

                $bGetProperty = !empty($this->arParams["PROPERTY_CODE"]);
                if ($bGetProperty) {
                    $arItem["PROPERTIES"] = array();
                }
                $arItem["DISPLAY_PROPERTIES"] = array();

                if ($this->arParams["SET_LAST_MODIFIED"]) {
                    $time = DateTime::createFromUserTime($arItem["TIMESTAMP_X"]);
                    if (
                      !isset($arResult["ITEMS_TIMESTAMP_X"])
                      || $time->getTimestamp() > $arResult["ITEMS_TIMESTAMP_X"]->getTimestamp()
                    )
                        $arResult["ITEMS_TIMESTAMP_X"] = $time;
                }

                if ($listPageUrl === '' && isset($arItem['~LIST_PAGE_URL'])) {
                    $listPageUrl = $arItem['~LIST_PAGE_URL'];
                }

                $id = (int)$arItem["ID"];
                $arResult["ITEMS"][$id] = $arItem;
            }
            unset($obElement);
            unset($iterator);

            if ($bGetProperty) {
                unset($elementFilter['IBLOCK_LID']);
                CIBlockElement::GetPropertyValuesArray(
                  $arResult["ITEMS"],
                  $arResult["ID"],
                  $elementFilter
                );
            }
        }

        $arResult['ITEMS'] = array_values($arResult['ITEMS']);


        foreach ($arResult["ITEMS"] as &$arItem) {
            if ($bGetProperty) {
                foreach ($this->arParams["PROPERTY_CODE"] as $pid) {
                    $prop = &$arItem["PROPERTIES"][$pid];
                    if (
                      (is_array($prop["VALUE"]) && count($prop["VALUE"]) > 0)
                      || (!is_array($prop["VALUE"]) && $prop["VALUE"] <> '')
                    ) {
                        $arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "news_out");
                    }
                }
            }

            $ipropValues = new Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
            $arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();
            Iblock\Component\Tools::getFieldImageData(
              $arItem,
              array('PREVIEW_PICTURE', 'DETAIL_PICTURE'),
              Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
              'IPROPERTY_VALUES'
            );

            foreach ($this->arParams["FIELD_CODE"] as $code)
                if (array_key_exists($code, $arItem))
                    $arItem["FIELDS"][$code] = $arItem[$code];
        }
        unset($arItem);

        return $arResult['ITEMS'];
    }


}
