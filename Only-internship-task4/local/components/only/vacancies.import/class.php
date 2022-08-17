<?php

use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Only\Helpers;


/**
 * Компонент для импорта вакансий в инфоблок VACANCIES
 *
 * Class VacanciesImportComponent
 * @package Only\Components
 *
 */
class VacanciesImportComponent extends CBitrixComponent
{
    /** @var int $idIBlock ID информационного блока */
    private $idIBlock;


    /**
     * Метод executeComponent
     *
     * @return mixed|void
     * @throws Exception
     */
    public function executeComponent()
    {

        if (Loader::IncludeModule("iblock")) {
            $this->arResult['IS_IBLOCK_MODULE_LOAD'] = true;
        } else {
            $this->arResult['IS_IBLOCK_MODULE_LOAD'] = false;
        }

        if (Helpers::getIBlockIdByCode($this->arParams['IBLOCK_CODE'])) {
            $this->arResult['IS_THE_IBLOCK_EXIST'] = true;
            $this->idIBlock = Helpers::getIBlockIdByCode($this->arParams['IBLOCK_CODE']);
        } else {
            $this->arResult['IS_THE_IBLOCK_EXIST'] = false;
        }

        $request = Application::getInstance()->getContext()->getRequest();
        $action = $request->getPost('NEW_FILE_UPLOAD');

        if ($action) {
            $file_id = $action;
            $path = $_SERVER['DOCUMENT_ROOT'] . (CFile::getPath($file_id));
            $this->deleteAllIblockElements($this->idIBlock);
            $this->importFromFile($path, $this->idIBlock);
        }

        $this->includeComponentTemplate();
    }


    /**
     * Метод осуществляет импорт вакансий из файла по полному пути $path
     *
     * @param $path
     * @param $iblockId
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function importFromFile($path, $iblockId)
    {
        $PROP = [];

        if (($handle = fopen($path, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row == 0) {
                    $row++;
                    continue;
                }
                $row++;

                // Для каждой строки с данными формируем массив куда заносим ячейки строки
                $PROP['ACTIVITY'] = $data[9];
                $PROP['FIELD'] = $data[11];
                $PROP['OFFICE'] = $data[1];
                $PROP['LOCATION'] = $data[2];
                $PROP['REQUIRE'] = $data[4];
                $PROP['DUTY'] = $data[5];
                $PROP['CONDITIONS'] = $data[6];
                $PROP['EMAIL'] = $data[12];
                $PROP['DATE'] = date('d.m.Y');
                $PROP['TYPE'] = $data[8];
                $PROP['SALARY_TYPE'] = '';
                $PROP['SALARY_VALUE'] = $data[7];
                $PROP['SCHEDULE'] = $data[10];

                // преобразование данных $PROP для каждого элемента массива
                foreach ($PROP as $key => &$value) {
                    // убираем пробелы вначале и в конце
                    $value = trim($value);
                    // убираем переносы
                    $value = str_replace(html_entity_decode("\n"), "", html_entity_decode($value));
                    // если в элементе занесен список, формируем массив из элементов этого списка
                    if (stripos($value, '•') !== false) {
                        $value = explode('•', $value);
                        array_splice($value, 0, 1);
                        // убираем пробелы вначале и в конце для каждого эл. списка
                        foreach ($value as &$str) {
                            $str = trim($str);
                        }
                    }
                    // Если элемент $PROP это элемент типа список, то необходимо заменить VALUE
                    // the элемента на ID элемента списка, соответствующему этому VALUE
                    if ($key == "ACTIVITY") {
                        $value = $this->getEnumElementIdByValue($iblockId, "ACTIVITY", $value);
                    }
                    if ($key == "FIELD") {
                        $value = $this->getEnumElementIdByValue($iblockId, "FIELD", $value);
                    }
                    if ($key == "OFFICE") {
                        $offIDsAndValues = $this->getEnumIdsValuesByEnumCode($iblockId, "OFFICE");
                        foreach ($offIDsAndValues as $el) {
                            similar_text($el['VALUE'], $value, $percentage);
                            if ($percentage > 80) {
                                $value = $el['ID'];
                                break;
                            }
                        }
                    }
                    if ($key == "LOCATION") {
                        $locIDsAndValues = $this->getEnumIdsValuesByEnumCode($iblockId, "LOCATION");
                        foreach ($locIDsAndValues as $el) {
                            if (strncasecmp($el['VALUE'], $value, 5) == 0) {
                                $value = $el['ID'];
                                break;
                            }
                        }
                    }
                    if ($key == "TYPE") {
                        $value = $this->getEnumElementIdByValue($iblockId, "TYPE", $value);
                    }
                    if ($key == "SCHEDULE") {
                        $value = $this->getEnumElementIdByValue($iblockId, "SCHEDULE", $value);
                    }

                }

                // Установка $PROP['SALARY_VALUE'] и $PROP['SALARY_TYPE'] в зависимости от условий
                if ($PROP['SALARY_VALUE'] == '-') {
                    $PROP['SALARY_VALUE'] = '';
                } elseif ($PROP['SALARY_VALUE'] == Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_SALARY_VALUE_BY_AGREEMENT')) {
                    $PROP['SALARY_VALUE'] = '';
                    $PROP['SALARY_TYPE'] = $this->getEnumElementIdByValue($iblockId, "SALARY_TYPE",
                      Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_SALARY_VALUE_CONTRACTUAL'));
                } else {
                    $arSalary = explode(' ', $PROP['SALARY_VALUE']);
                    if ($arSalary[0] == Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_SALARY_VALUE_FROM_SMALL')) {
                        $PROP['SALARY_TYPE'] = $this->getEnumElementIdByValue($iblockId, "SALARY_TYPE",
                          Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_SALARY_VALUE_FROM'));
                        array_splice($arSalary, 0, 1);
                        $PROP['SALARY_VALUE'] = implode(' ', $arSalary);
                    } elseif ($arSalary[0] == Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_SALARY_VALUE_TO_SMALL')) {
                        $PROP['SALARY_TYPE'] = $this->getEnumElementIdByValue($iblockId, "SALARY_TYPE",
                          Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_SALARY_VALUE_TO'));
                        array_splice($arSalary, 0, 1);
                        $PROP['SALARY_VALUE'] = implode(' ', $arSalary);
                    } else {
                        $PROP['SALARY_TYPE'] = $this->getEnumElementIdByValue($iblockId, "SALARY_TYPE", "=");
                    }
                }

                global $USER;
                $arLoadProductArray = [
                  "MODIFIED_BY" => $USER->GetID(),
                  "IBLOCK_SECTION_ID" => false,
                  "IBLOCK_ID" => $this->idIBlock,
                  "PROPERTY_VALUES" => $PROP,
                  "NAME" => $data[3],
                  "ACTIVE" => end($data) ? 'Y' : 'N',
                ];

                $el = new CIBlockElement;
                if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                    echo Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_ADD_MESSAGE') . $PRODUCT_ID . "<br>";
                } else {
                    echo Loc::getMessage('ONLY_VACANCIES_IMPORT_CLASS_ADD_ERROR') . $el->LAST_ERROR . '<br>';
                }
            }
            fclose($handle);
        }
    }


    /**
     * Метод получения ID элемента поля типа список по его значению
     *
     * @param $iblockId
     * @param $enumPropertyCode
     * @param $enumElementValue
     * @return mixed|null
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getEnumElementIdByValue($iblockId, $enumPropertyCode, $enumElementValue)
    {

        $elements = PropertyEnumerationTable::getList([
          "select" => ["ID", "VALUE"],
          "filter" => [
            "PROPERTY.CODE" => $enumPropertyCode,
            "PROPERTY.IBLOCK_ID" => $iblockId,
          ],
          "cache" => [
            "ttl" => 3600
          ]
        ]);

        $arrElements = $elements->fetchAll();

        $ID = null;

        foreach ($arrElements as $arrElement) {
            $nbsp = html_entity_decode("&nbsp;");
            $str = str_replace($nbsp, " ", html_entity_decode($arrElement['VALUE']));
            $str = trim($str);
            $v = strcasecmp($str, trim($enumElementValue));
            if ($v == 0) {
                $ID = $arrElement['ID'];
            }
        }

        return $ID;
    }


    /**
     * Метод получения массива с кодами и значениями элемента типа список по коду элемента
     *
     * @param $iblockId
     * @param $enumPropertyCode
     * @return array
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getEnumIdsValuesByEnumCode($iblockId, $enumPropertyCode)
    {

        $elements = PropertyEnumerationTable::getList([
          "select" => ["ID", "VALUE"],
          "filter" => [
            "PROPERTY.CODE" => $enumPropertyCode,
            "PROPERTY.IBLOCK_ID" => $iblockId,
          ],
          "cache" => [
            "ttl" => 3600
          ]
        ]);

        return $elements->fetchAll();
    }


    /**
     * Метод для удаления всех элементов инфоблока
     * @param $iblockId
     */
    public function deleteAllIblockElements($iblockId)
    {
        $rsElements = CIBlockElement::GetList(
          [],
          ['IBLOCK_ID' => $iblockId],
          false,
          false,
          ['ID']
        );
        while ($element = $rsElements->GetNext()) {
            CIBlockElement::Delete($element['ID']);
        }
    }


}
