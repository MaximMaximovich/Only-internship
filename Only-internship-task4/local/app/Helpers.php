<?php

namespace Only;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CIBlockFindTools;
use Exception;

class Helpers
{
  /**
   * Метод возвращает ID инфоблока по символьному коду
   *
   * @param $code
   *
   * @throws ArgumentException
   * @throws LoaderException
   * @throws ObjectPropertyException
   * @throws SystemException
   * @throws Exception
   *
   * @returm int/void
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
     * Метод возвращает ID элемента по символьному коду
     *
     * @param $code
     * @param $iblockCode
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     *
     * @returm int/void
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
            "IBLOCK_ID" => Helpers::getIBlockIdByCode($iblockCode))
        );

        // если элемента с заданным code нет то возвращаем false
        if (!$return) {
            return false;
        }

        return $return;
    }

}
