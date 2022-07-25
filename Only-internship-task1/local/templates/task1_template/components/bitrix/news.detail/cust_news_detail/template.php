<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="article-item article-list__item""
   data-anim="anim-3">
    <div class="article-item__background"><img src="<?= $arResult['DETAIL_PICTURE']['SRC'] ?>"
                                               alt="<?= $arResult['DETAIL_PICTURE']['ALT'] ?>"/></div>
    <div class="article-item__wrapper">
        <div class="article-item__title"><?= $arResult['NAME'] ?></div>
        <div class="article-item__content"><?= $arResult['DETAIL_TEXT'] ?></div>
    </div>
</div>


