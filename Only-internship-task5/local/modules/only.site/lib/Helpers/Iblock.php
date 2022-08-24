<?php

namespace Only\Site\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CIBlockFindTools;
use CIBlockSection;
use Exception;

class Iblock
{
    /**
     * Метод возвращает ID инфоблока по символьному коду
     *
     * @param $code
     * @return false|mixed|void
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIBlockIdByCode($code)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return;
        }
        $IB = \Bitrix\Iblock\IblockTable::getList([
          'select' => ['ID'],
          'filter' => ['CODE' => $code],
          'limit' => '1',
          'cache' => ['ttl' => 3600]
        ]);
        $return = $IB->fetch();
        if (!$return) {
            return false;
        }

        return $return['ID'];
    }


    /**
     * Метод возвращает CODE (символьный код) инфоблока по его ID
     *
     * @param $iblockId
     * @return false|mixed|void
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIBlockCodeById($iblockId)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return;
        }
        $IB = \Bitrix\Iblock\IblockTable::getList([
          'select' => ['CODE'],
          'filter' => ['ID' => $iblockId],
          'limit' => '1',
          'cache' => ['ttl' => 3600]
        ]);
        $return = $IB->fetch();
        if (!$return) {
            return false;
        }

        return $return['CODE'];
    }


    /**
     * Метод возвращает имя инфоблока по его ID
     *
     * @param $iblockId
     * @return false|mixed|void
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIBlockNameById($iblockId)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return;
        }
        $IB = \Bitrix\Iblock\IblockTable::getList([
          'select' => ['NAME'],
          'filter' => ['ID' => $iblockId],
          'limit' => '1',
          'cache' => ['ttl' => 3600]
        ]);
        $return = $IB->fetch();
        if (!$return) {
            return false;
        }

        return $return['NAME'];
    }


    /**
     * Метод возвращает название секции по её ID
     *
     * @param $sectionId
     * @return false|mixed
     * @throws LoaderException
     */
    public static function getSectionNameById($sectionId) {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return false;
        }
        $res = CIBlockSection::GetByID($sectionId);
        if($ar_res = $res->GetNext())
            return $ar_res['NAME'];
    }


    /**
     * Метод возвращает ID элемента по символьному коду
     *
     * @param $code
     * @param $iblockCode
     * @return int
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getElementIdByCode($code, $iblockCode): int
    {

        $objFindTools = new CIBlockFindTools();

        $return = $objFindTools->GetElementID(
          false,
          $code,
          false,
          false,
          array(
            "IBLOCK_ID" => Iblock::getIBlockIdByCode($iblockCode))
        );

        // если элемента с заданным code нет то возвращаем false
        if (!$return) {
            return false;
        }

        return $return;
    }
}