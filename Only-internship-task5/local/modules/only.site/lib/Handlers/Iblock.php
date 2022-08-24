<?php

namespace Only\Site\Handlers;


use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CIBlockElement;
use CIBlockSection;


class Iblock
{
    /** @var int $handlerDisallow флаг для отключения обработчика */
    protected static $handlerDisallow = 0;

    /**
     * Обработчик событий OnAfterIBlockElementAdd и OnAfterIBlockElementUpdate
     *
     * @param $arFields
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function addLog(&$arFields)
    {
        /* проверяем, что обработчик уже запущен */
        if (!self::isEnabledHandler())
            return;
        /* взводим флаг запуска */
        self::disableHandler();

        if (!Loader::includeModule("only.site"))
            return;

        if (is_array($arFields))
            $f = $arFields;
        else // условие для $method == delete
        {
            $rs = CIBlockElement::GetById($arFields);
            $f = $rs->Fetch();
        }

        if (!$f["ID"])
            return;

        $d = debug_backtrace();
        // переменная операции выполнения add, update, delete
        $method = strtolower($d[3]["function"]);

        // Получаем ID инфоблока с логами по его символьному коду
        $logsIblockID = \Only\Site\Helpers\Iblock::getIBlockIdByCode("LOG");
        // Имя раздела лога равно имени инфоблока отслеживаемого элемента
        $sectionName = \Only\Site\Helpers\Iblock::getIBlockNameById($arFields["IBLOCK_ID"]);
        // Символьный код раздела лога равен символьному коду инфоблока отслеживаемого элемента
        $sectionCode = \Only\Site\Helpers\Iblock::getIBlockCodeById($arFields["IBLOCK_ID"]);

        // получаем требуемые массивы из инфоблока LOG
        $result = SectionTable::getList(
          array(
            'select' => array('ID', 'NAME'),
            'filter' => array("IBLOCK_ID" => $logsIblockID, "DEPTH_LEVEL" => 1),
          )
        );

        $arRes = $result->fetchAll();
        $sectionNames = [];
        $sectionIds = [];
        foreach ($arRes as $arResEl) {
            $sectionIds[] = $arResEl["ID"];
            $sectionNames[] = $arResEl["NAME"];
        }

        $sectionId = null;

        if (in_array($sectionName, $sectionNames)) {
            $sectionId = $sectionIds[array_search($sectionName, $sectionNames)];
        }

        // подготовка параметров для создания/обновления раздела
        $arSectionFields = array(
          "ACTIVE" => "Y",
          "IBLOCK_SECTION_ID" => $sectionId,
          "CODE" => $sectionCode,
          "IBLOCK_ID" => $logsIblockID,
          "NAME" => $sectionName,
          "SORT" => "ASC",
          "DESCRIPTION" => Loc::getMessage('ONLY_DEV_SYTE_MODULE_HANDLERS_IBLOCK') . $sectionName,
          "DESCRIPTION_TYPE" => "text"
        );
        // создаем/обновляем раздел
        $bs = new CIBlockSection;
        if ($sectionId > 0) {
            $bs->Update($sectionId, $arSectionFields);
        } else {
            $sectionId = $bs->Add($arSectionFields);
        }

        // Формируем PREVIEW_TEXT
        $elemIblockName = \Only\Site\Helpers\Iblock::getIBlockNameById($arFields["IBLOCK_ID"]);
        // Массив с ID разделов логируемого элемента
        $sectionsIds = $this->getSectionsIds($arFields["ID"]);
        $sectionsString = "";
        foreach ($sectionsIds as $sectionsId) {
            $sectionsName = \Only\Site\Helpers\Iblock::getSectionNameById($sectionsId);
            $sectionsString .= $sectionsName . "->";
        }
        $previewText = $elemIblockName . "->" . $sectionsString . $arFields["NAME"];

        // Создаем элемент лога
        global $USER;
        $arLoadProductArray = [
          "MODIFIED_BY" => $USER->GetID(),
          "IBLOCK_SECTION_ID" => $sectionId,
          "IBLOCK_ID" => $logsIblockID,
          "PROPERTY_VALUES" => false,
          "NAME" => $arFields["ID"],
          "CODE" => "element_" . $arFields["ID"],
          "PREVIEW_TEXT" => $previewText,
          "ACTIVE" => "Y",
        ];

        $el = new CIBlockElement;
        if ($method == "add" || $method == "update") {
            $el->Add($arLoadProductArray);
        }

        /* вновь разрешаем запускать обработчик */
        self::enableHandler();

    }


    /**
     * Метод возвращает массив с ID всех разделов елемента по его ID
     *
     * @param $elementId
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    function getSectionsIds($elementId)
    {
        if (!Loader::includeModule('iblock')) {
            return false;
        }


        $element = ElementTable::getRow([
          'select' => [
            'IBLOCK_SECTION_ID',

          ],
          'filter' => [
            '=ID' => $elementId,
          ],
        ]);


        if ($element == null)
            return false;

        $parentSections = [];

        $parentSectionIterator = SectionTable::getList([
          'select' => [
            'SECTION_ID' => 'SECTION_SECTION.ID',
            'IBLOCK_SECTION_ID' => 'SECTION_SECTION.IBLOCK_SECTION_ID',
          ],
          'filter' => [
            '=ID' => $element['IBLOCK_SECTION_ID'],
          ],
          'runtime' => [
            'SECTION_SECTION' => [
              'data_type' => '\Bitrix\Iblock\SectionTable',
              'reference' => [
                '=this.IBLOCK_ID' => 'ref.IBLOCK_ID',
                '>=this.LEFT_MARGIN' => 'ref.LEFT_MARGIN',
                '<=this.RIGHT_MARGIN' => 'ref.RIGHT_MARGIN',
              ],
              'join_type' => 'inner'
            ],
          ],
        ]);

        while ($parentSection = $parentSectionIterator->fetch()) {
            $parentSections[$parentSection['SECTION_ID']] = $parentSection;
        }

        $lastEl = array_pop($parentSections);

        $sectionsIds = [];
        foreach ($lastEl as $key => $value) {
            $sectionsIds [] = $value;
        }

//        echo '<pre>';print_r(array_reverse($sectionsIds));echo '</pre>';

        return array_reverse($sectionsIds);

    }


    /**
     * Метод для отключения обработчика
     */
    public static function disableHandler()
    {
        self::$handlerDisallow--;
    }

    /**
     * Метод для включения обработчика
     */
    public static function enableHandler()
    {
        self::$handlerDisallow++;
    }

    /**
     * Метод для определения статуса работы обработчика
     * @return bool
     */
    public static function isEnabledHandler()
    {
        return (self::$handlerDisallow >= 0);
    }
}