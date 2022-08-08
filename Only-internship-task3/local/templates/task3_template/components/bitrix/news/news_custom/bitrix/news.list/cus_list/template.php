<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? foreach ($arResult["ITEMS"] as $arItem): ?>
<div class="article-card">
    <h2><?= $arItem["NAME"] ?></h2>
    <? foreach ($arItem["ELEMENTS"] as $article) : ?>
    <div class="article-card__date"><?= strtolower(FormatDate("d M Y", MakeTimeStamp($article["ACTIVE_FROM"])))?></div>
        <h3><?= $article["NAME"] ?><h3>
        <p><?= $article["PREVIEW_TEXT"] ?><p>
        <a class="article-card__button" href="<?= $article["DETAIL_PAGE_URL"] ?>"><?= GetMessage('MEWS_DETAIL_LINK') ?></a>

    <? endforeach; ?>
</div>
<? endforeach; ?>
