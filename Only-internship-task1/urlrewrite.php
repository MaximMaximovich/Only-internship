<?php
$arUrlRewrite = array(
  0 =>
    array(
      'CONDITION' => '#^/services/#',
      'RULE' => '',
      'ID' => 'bitrix:catalog',
      'PATH' => '/services/index.php',
      'SORT' => 100,
    ),
  1 =>
    array(
      'CONDITION' => '#^/products/#',
      'RULE' => '',
      'ID' => 'bitrix:catalog',
      'PATH' => '/products/index.php',
      'SORT' => 100,
    ),
  3 =>
    array(
      "CONDITION" => "#^/task1/([a-zA-Z0-9\\.\\-_]+)/?.*#",
      "RULE" => "ELEMENT_CODE=$1",
      "PATH" => "/task1/detail.php",
      'SORT' => 100,
    ),
  5 =>
    array(
      'CONDITION' => '#^/news/#',
      'RULE' => '',
      'ID' => 'bitrix:news',
      'PATH' => '/news/index.php',
      'SORT' => 100,
    ),
);
