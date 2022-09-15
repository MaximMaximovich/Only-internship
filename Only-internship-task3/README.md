# Задача:
Сделать шаблон комплексного компонента news используя верстку из папки /assignment. 
На главной подключить news.list, на детальной news.detail
(Дополнительно) Сделать разбивку по разделам

# Решение:
Кастомизированный шаблон сайта /local/templates/task3_template/

Кастомизированный шаблон компонента bitrix:news: /local/templates/task3_template/components/bitrix/news/news_custom/

Кастомизированный шаблон компонента bitrix:news.detail: /local/templates/task3_template/components/bitrix/news/news_custom/bitrix/news.detail/cus_detail/template.php 

Кастомизированный шаблон компонента bitrix:news.list: /local/templates/task3_template/components/bitrix/news/news_custom/bitrix/news.list/cus_list/template.php

Для подготовки вывода новостей на странице с сортировкой по разделам использовался фаил result_modifier.php:
 /local/templates/task3_template/components/bitrix/news/news_custom/bitrix/news.list/cus_list/result_modifier.php

Миграция для инфоблока на котором осуществлялись проверки /local/php_interface/migrations/Version20220809011421.php
Миграция сделана при помощи модуля andreyryabin/sprint.migration

Страница на которой подключался компонент  - /task3/index.php
К /task3/index.php был применен кастомизированный шаблон сайта.