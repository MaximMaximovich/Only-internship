# Задача:
Сделать шаблон компонента form.result.new, интегрированный с формой используя верстку из папки /assignment

# Решение:
Шаблон компонента form.result.new: /local/templates/task2_template/components/bitrix/form.result.new/custom_form/template.php

Кастомизированный шаблон сайта /local/templates/task2_template/

Миграция для инфоблока веб-формы которую требовалось создать и на которой осуществлялись проверки /local/php_interface/migrations/Version20220802062729.php
Миграция сделана при помощи модуля andreyryabin/sprint.migration

Файл отображения успешного результата работы формы (использовался при отладке) success.php

Страница на которой подключался компонент  - /task2/index.php

К обоим /task2/index.php и success.php был применен кастомизированный шаблон сайта.
В кастомизированный шаблон сайта ничего кроме стилей и картинок из задания не вносил. Cделан только для проверки работы формы