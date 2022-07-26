<?php

namespace Sprint\Migration\Schema;

use Bitrix\Main\Loader;
use Sprint\Migration\AbstractSchema;
use Sprint\Migration\HelperManager;

class IblockSchema extends AbstractSchema
{

    private $iblockIds = [];

    protected function isBuilderEnabled()
    {
        return (Loader::includeModule('iblock'));
    }

    protected function initialize()
    {
        $this->setTitle('Схема инфоблоков');
    }

    public function getMap()
    {
        return ['iblock_types', 'iblocks/'];
    }

    public function outDescription()
    {
        $schemaTypes = $this->loadSchema('iblock_types', [
            'items' => [],
        ]);

        $this->out('Типы инфоблоков: %d', count($schemaTypes['items']));

        $schemaIblocks = $this->loadSchemas('iblocks/', [
            'iblock' => [],
            'fields' => [],
            'props' => [],
            'element_form' => [],
        ]);

        $this->out('Инфоблоков: %d', count($schemaIblocks));

        $cntProps = 0;
        $cntForms = 0;
        foreach ($schemaIblocks as $schemaIblock) {
            $cntProps += count($schemaIblock['props']);

            if (!empty($schemaIblock['element_form'])) {
                $cntForms++;
            }
        }

        $this->out('Свойств инфоблоков: %d', $cntProps);
        $this->out('Форм редактирования: %d', $cntForms);
    }

    public function export()
    {
        $helper = HelperManager::getInstance();

        $types = $helper->Iblock()->getIblockTypes();
        $exportTypes = [];
        foreach ($types as $type) {
            $exportTypes[] = $helper->Iblock()->exportIblockType($type['ID']);
        }

        $this->saveSchema('iblock_types', [
            'items' => $exportTypes,
        ]);

        $iblocks = $helper->Iblock()->getIblocks();
        foreach ($iblocks as $iblock) {
            if (!empty($iblock['CODE'])) {
                $this->saveSchema('iblocks/' . strtolower($iblock['IBLOCK_TYPE_ID'] . '-' . $iblock['CODE']), [
                    'iblock' => $helper->Iblock()->exportIblock($iblock['ID']),
                    'fields' => $helper->Iblock()->exportIblockFields($iblock['ID']),
                    'props' => $helper->Iblock()->exportProperties($iblock['ID']),
                    'element_form' => $helper->UserOptions()->exportElementForm($iblock['ID']),
                ]);
            }
        }

    }

    public function import()
    {
        $schemaTypes = $this->loadSchema('iblock_types', [
            'items' => [],
        ]);

        $schemaIblocks = $this->loadSchemas('iblocks/', [
            'iblock' => [],
            'fields' => [],
            'props' => [],
            'element_form' => [],
        ]);

        foreach ($schemaTypes['items'] as $type) {
            $this->addToQueue('saveIblockType', $type);
        }

        foreach ($schemaIblocks as $schemaIblock) {
            $iblockUid = $this->getUniqIblock($schemaIblock['iblock']);

            $this->addToQueue('saveIblock', $schemaIblock['iblock']);
            $this->addToQueue('saveIblockFields', $iblockUid, $schemaIblock['fields']);
        }

        foreach ($schemaIblocks as $schemaIblock) {
            $iblockUid = $this->getUniqIblock($schemaIblock['iblock']);
            $this->addToQueue('saveProperties', $iblockUid, $schemaIblock['props']);
            $this->addToQueue('saveElementForm', $iblockUid, $schemaIblock['element_form']);
        }

        foreach ($schemaIblocks as $schemaIblock) {
            $iblockUid = $this->getUniqIblock($schemaIblock['iblock']);

            $skip = [];
            foreach ($schemaIblock['props'] as $prop) {
                $skip[] = $this->getUniqProp($prop);
            }

            $this->addToQueue('cleanProperties', $iblockUid, $skip);
        }

        $skip = [];
        foreach ($schemaIblocks as $schemaIblock) {
            $skip[] = $this->getUniqIblock($schemaIblock['iblock']);
        }

        $this->addToQueue('cleanIblocks', $skip);


        $skip = [];
        foreach ($schemaTypes['items'] as $type) {
            $skip[] = $this->getUniqIblockType($type);
        }

        $this->addToQueue('cleanIblockTypes', $skip);
    }


    protected function saveIblockType($fields = [])
    {
        $helper = HelperManager::getInstance();
        $helper->Iblock()->setTestMode($this->testMode);
        $helper->Iblock()->saveIblockType($fields);
    }

    protected function saveIblock($fields)
    {
        $helper = HelperManager::getInstance();
        $helper->Iblock()->setTestMode($this->testMode);
        $helper->Iblock()->saveIblock($fields);
    }

    protected function saveIblockFields($iblockUid, $fields)
    {
        $iblockId = $this->getIblockId($iblockUid);
        if (!empty($iblockId)) {
            $helper = HelperManager::getInstance();
            $helper->Iblock()->setTestMode($this->testMode);
            $helper->Iblock()->saveIblockFields($iblockId, $fields);
        }
    }

    protected function saveProperties($iblockUid, $properties)
    {
        $iblockId = $this->getIblockId($iblockUid);
        if (!empty($iblockId)) {
            $helper = HelperManager::getInstance();
            $helper->Iblock()->setTestMode($this->testMode);
            foreach ($properties as $property) {
                $helper->Iblock()->saveProperty($iblockId, $property);
            }
        }
    }

    protected function saveElementForm($iblockUid, $elementForm)
    {
        $iblockId = $this->getIblockId($iblockUid);
        if (!empty($iblockId)) {
            $helper = HelperManager::getInstance();
            $helper->UserOptions()->setTestMode($this->testMode);
            $helper->UserOptions()->saveElementForm($iblockId, $elementForm);
        }
    }

    protected function cleanProperties($iblockUid, $skip = [])
    {
        $iblockId = $this->getIblockId($iblockUid);
        if (!empty($iblockId)) {
            $helper = HelperManager::getInstance();
            $olds = $helper->Iblock()->getProperties($iblockId);
            foreach ($olds as $old) {
                if (!empty($old['CODE'])) {
                    $uniq = $this->getUniqProp($old);
                    if (!in_array($uniq, $skip)) {
                        $ok = ($this->testMode) ? true : $helper->Iblock()->deletePropertyById($old['ID']);
                        $this->outWarningIf($ok, 'Инфоблок %s: свойство %s удалено', $iblockId,
                            $this->getTitleProp($old));
                    }
                }
            }
        }
    }

    protected function cleanIblockTypes($skip = [])
    {
        $helper = HelperManager::getInstance();

        $olds = $helper->Iblock()->getIblockTypes();
        foreach ($olds as $old) {
            $uniq = $this->getUniqIblockType($old);
            if (!in_array($uniq, $skip)) {
                $ok = ($this->testMode) ? true : $helper->Iblock()->deleteIblockType($old['ID']);
                $this->outWarningIf($ok, 'Тип инфоблока %s: удален', $old['ID']);
            }
        }
    }

    protected function cleanIblocks($skip = [])
    {
        $helper = HelperManager::getInstance();

        $olds = $helper->Iblock()->getIblocks();
        foreach ($olds as $old) {
            if (!empty($old['CODE'])) {
                $uniq = $this->getUniqIblock($old);
                if (!in_array($uniq, $skip)) {
                    $ok = ($this->testMode) ? true : $helper->Iblock()->deleteIblock($old['ID']);
                    $this->outWarningIf($ok, 'Инфоблок %s: удален', $old['ID']);
                }
            }
        }
    }


    protected function getTitleProp($prop)
    {
        return empty($prop['CODE']) ? $prop['ID'] : $prop['CODE'];
    }

    protected function getUniqProp($prop)
    {
        return $prop['CODE'];
    }

    protected function getUniqIblockType($type)
    {
        return $type['ID'];
    }

    protected function getUniqIblock($iblock)
    {
        return $iblock['IBLOCK_TYPE_ID'] . ':' . $iblock['CODE'];
    }

    protected function getIblockId($iblockUid)
    {
        $helper = HelperManager::getInstance();

        if (isset($this->iblockIds[$iblockUid])) {
            return $this->iblockIds[$iblockUid];
        }

        list($type, $code) = explode(':', $iblockUid);

        $this->iblockIds[$iblockUid] = $helper->Iblock()->getIblockId($code, $type);
        return $this->iblockIds[$iblockUid];

    }

}