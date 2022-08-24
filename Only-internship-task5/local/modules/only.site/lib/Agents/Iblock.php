<?php

namespace Only\Site\Agents;


use Bitrix\Main\Loader;

class Iblock
{
    public static function clearOldLogs()
    {
        global $DB;

        if (Loader::includeModule('iblock') && Loader::includeModule('only.site')) {
            $iblockId = \Only\Site\Helpers\Iblock::getIBlockIdByCode("LOG");
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('FULL'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'DESC'], [
              'IBLOCK_ID' => $iblockId,
            ], false, false, ['ID', 'IBLOCK_ID']);

            $i = 1;
            while ($arLog = $rsLogs->Fetch()) {
                if ($i > 10) {
                    \CIBlockElement::Delete($arLog['ID']);
                }
                $i++;
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }

}
