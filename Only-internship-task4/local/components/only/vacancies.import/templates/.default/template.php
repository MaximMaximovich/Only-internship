<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die;

use Bitrix\Main\Localization\Loc;

?>


<? if (!$arResult['IS_IBLOCK_MODULE_LOAD']) : ?>

    <?= Loc::getMessage('ONLY_VACANCIES_IMPORT_DEFAULT_TEMPLATE_ERROR1') ?>
    <?= '<br>' ?>

<? else: ?>

    <? if (empty($arParams['IBLOCK_CODE'])) : ?>
        <?= Loc::getMessage('ONLY_VACANCIES_IMPORT_DEFAULT_TEMPLATE_ERROR2') ?>
        <?= '<br>' ?>
    <? endif; ?>

    <? if (!$arResult['IS_THE_IBLOCK_EXIST']) : ?>
        <?= Loc::getMessage('ONLY_VACANCIES_IMPORT_DEFAULT_TEMPLATE_ERROR3') ?>
        <?= '<br>' ?>
    <? endif; ?>


    <? if (!empty($arParams['IBLOCK_CODE']) && $arResult['IS_THE_IBLOCK_EXIST']) : ?>

        <h3 class=""><?= Loc::getMessage('ONLY_VACANCIES_IMPORT_TEMPLATE_FORM_TITLE') ?></h3>
        <? if ($arResult['IMPORT_SUCCEED']) : ?>
            <div id="message"><?= Loc::getMessage('ONLY_VACANCIES_IMPORT_DEFAULT_TEMPLATE_MESSAGE1') ?></div>
            <?= '<br>' ?>
        <? endif; ?>
        <? if ($arResult['IMPORT_FAILED']) : ?>
            <div id="message"><?= Loc::getMessage('ONLY_VACANCIES_IMPORT_DEFAULT_TEMPLATE_ERROR4') ?></div>
            <?= '<br>' ?>
        <? endif; ?>

        <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" id="import-form">
            <div class="content-block">
                <div class="content-block-inner">
                    <? $APPLICATION->IncludeComponent("bitrix:main.file.input", "",
                      array(
                        "INPUT_NAME" => "NEW_FILE_UPLOAD",
                        "MULTIPLE" => "N",
                        "MODULE_ID" => "iblock",
                        "MAX_FILE_SIZE" => "",
                        "ALLOW_UPLOAD" => "F",
                        "ALLOW_UPLOAD_EXT" => 'csv',
                        "INPUT_VALUE" => $_POST['NEW_FILE_UPLOAD']
                      ),
                      false

                    ); ?>
                </div>
            </div>
            <input type="submit" id="import-button"
                   value=<?= Loc::getMessage('ONLY_VACANCIES_IMPORT_TEMPLATE_FORM_BUTTON_TITLE') ?>>
        </form>

    <? endif; ?>

<? endif; ?>


