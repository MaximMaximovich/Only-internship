<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>

<main>
    <div class="wrapper">
        <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "planet", Array(
            "START FROM" => "0",
            "PATH" => "",
            "SITE_ID" => "s1"
          )
        );?>
        <h1 class="aside_title">Форма успешно отправлена</h1>
        <a href="/task2/index.php">Вернуться к форме</a>
    </div>
</main>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
