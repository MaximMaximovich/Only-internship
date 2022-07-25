<?php

namespace Only;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
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
      throw new Exception('IBlock with code"' . $code . '" not found');
    }

    return $return['ID'];
  }

}
