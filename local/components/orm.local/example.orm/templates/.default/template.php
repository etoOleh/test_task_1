<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Orm\Local\OrmTable;

Loader::includeModule('iblock');

global $USER;

if ($USER->IsAdmin()) {
    echo '<pre>' . print_r($arResult["ITEMS"], 1) . '</pre>';
}


/**
 *
 * Добавление эл-нта
 *
 */
/**
$add1 = OrmTable::add([
'NAME' => 'Название',
'DATE_INSERT' => '2024-08-27 21:55:55',
]);
 **/