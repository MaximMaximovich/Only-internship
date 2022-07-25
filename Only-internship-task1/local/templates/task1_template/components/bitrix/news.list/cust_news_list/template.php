<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="article-list">

    <? foreach ($arResult['ITEMS'] as $arItem) : ?>
        <a class="article-item article-list__item" href="<?= $arItem['CODE'] ?>"
           data-anim="anim-3">
            <div class="article-item__background"><img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>"
                                                       data-src="<?= $arItem['PROPERTIES']['DATA_SRC']['VALUE'] ?>"
                                                       alt="<?= $arItem['PREVIEW_PICTURE']['ALT'] ?>"/></div>
            <div class="article-item__wrapper">
                <div class="article-item__title"><?= $arItem['NAME'] ?></div>
                <div class="article-item__content"><?= $arItem['PREVIEW_TEXT'] ?></div>
            </div>
        </a>
    <? endforeach; ?>

</div>
