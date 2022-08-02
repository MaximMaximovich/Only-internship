<?

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="contact-form">
    <? if ($arResult["isFormErrors"] == "Y"): ?><?= $arResult["FORM_ERRORS_TEXT"]; ?><? endif; ?>

    <?= $arResult["FORM_NOTE"] ?>

    <? if ($arResult["isFormNote"] != "Y") {
        ?>

        <div class="contact-form__head">
            <div class="contact-form__head-title"><?= $arResult["FORM_TITLE"] ?></div>
            <div class="contact-form__head-text"><?= $arResult["FORM_DESCRIPTION"] ?></div>
        </div>

        <?=str_replace('<form', '<form class="contact-form__form"', $arResult["FORM_HEADER"]) ?>

            <div class="contact-form__form-inputs">

                <div class="input contact-form__input">
                    <label class="input__label" for="medicine_name">
                        <div class="input__label-text"><?= $arResult["QUESTIONS"]["NAME"]["CAPTION"] ?>*</div>
                        <?=str_replace('<input', '<input class="input__input" type="text" id="medicine_name" value=""
                         required=""', $arResult["QUESTIONS"]["NAME"]["HTML_CODE"]) ?>
                        <div class="input__notification"><?=Loc::GetMessage("FORM_NOTIFICATION_TEXT_LENGTH")?></div>
                    </label>
                </div>

                <div class="input contact-form__input">
                    <label class="input__label" for="medicine_company">
                        <div class="input__label-text"><?= $arResult["QUESTIONS"]["COMPANY_AND_POSITION"]["CAPTION"] ?>*</div>
                        <?=str_replace('<input', '<input class="input__input" type="text" id="medicine_company" value=""
                               required=""', $arResult["QUESTIONS"]["COMPANY_AND_POSITION"]["HTML_CODE"]) ?>
                        <div class="input__notification"><?=Loc::GetMessage("FORM_NOTIFICATION_TEXT_LENGTH")?></div>
                    </label>
                </div>

                <div class="input contact-form__input">
                    <label class="input__label" for="medicine_email">
                        <div class="input__label-text"><?= $arResult["QUESTIONS"]["EMAIL"]["CAPTION"] ?>*</div>
                        <?=str_replace('<input', '<input class="input__input" type="email" id="medicine_email" value=""
                               required=""', $arResult["QUESTIONS"]["EMAIL"]["HTML_CODE"]) ?>
                        <div class="input__notification"><?=Loc::GetMessage("FORM_NOTIFICATION_INVALID_MAIL_FORMAT")?></div>
                    </label>
                </div>

                <div class="input contact-form__input">
                    <label class="input__label" for="medicine_phone">
                        <div class="input__label-text"><?= $arResult["QUESTIONS"]["PHONE"]["CAPTION"] ?>*</div>
                        <?$htmlPattern = "<input class=\"input__input\" type=\"tel\" id=\"medicine_phone\"
                               data-inputmask=\"'mask': '+79999999999', 'clearIncomplete': 'true'\" maxlength=\"12\"
                               x-autocompletetype=\"phone-full\" value=\"\" required=\"\""?>
                        <?=str_replace('<input', $htmlPattern, $arResult["QUESTIONS"]["PHONE"]["HTML_CODE"]) ?>
                    </label>
                </div>
            </div>

            <div class="contact-form__form-message">
                <div class="input"><label class="input__label" for="medicine_message">
                        <div class="input__label-text"><?= $arResult["QUESTIONS"]["MESSAGE"]["CAPTION"] ?></div>
                        <?=str_replace('<textarea', '<textarea class="input__input" type="text" id="medicine_message" 
                                  value=""', $arResult["QUESTIONS"]["MESSAGE"]["HTML_CODE"]) ?>
                        <div class="input__notification"></div>
                    </label></div>
            </div>

            <div class="contact-form__bottom">
                <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что
                    ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку
                    персональных
                    данных&raquo;.
                </div>

                <input class="form-button contact-form__bottom-button" <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?>
                       type="submit"
                       name="web_form_submit"
                       value="<?= htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>"
                       data-success="Отправлено" data-error="Ошибка отправки"/>
            </div>
<!--        <p>-->
<!--            --><?//= $arResult["REQUIRED_SIGN"]; ?><!-- - --><?//= GetMessage("FORM_REQUIRED_FIELDS") ?>
<!--        </p>-->
        <?= $arResult["FORM_FOOTER"] ?>
        <?
    } //endif (isFormNote)?>
</div>


