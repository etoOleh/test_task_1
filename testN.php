<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Query;
use Orm\Local\ProfileTable;
use \Bitrix\Main\Engine\CurrentUser;
use Orm\Local\WorkDayTable;
use Bitrix\Main\Type\Date;

Loader::includeModule('iblock');

if (Loader::includeModule("orm.local")) {

    $userEntity = ProfileTable::getEntity();
    $dataUserRes =
        (new Query($userEntity))
            ->setFilter([
                'LOGIN' => CurrentUser::get()->getLogin(),
            ])
            ->setSelect(['ID', 'LOGIN'])
            ->whereNotNull('LOGIN')
            ->exec();

    if ($dataUserRes->getSelectedRowsCount() == 0) {
        ShowError(GetMessage("CHECK_IF_DATA_HERE"));
        return;
    }
    $currentUser = $dataUserRes->fetchAll()[0];

    $workDayEntity = WorkDayTable::getEntity();
    $dataWorkDayRes =
        (new Query($workDayEntity))
            ->setFilter([
                'PROFILE_ID' => $currentUser['ID'],
                '>DATE_START' => Date::createFromTimestamp(time())->toString(),
            ])
            ->setSelect(['ID', 'PROFILE_ID', 'DATE_START', 'DATE_STOP'])
            ->setOrder(['ID' => 'DESC'])
            ->whereNotNull('DATE_START')
            ->exec();
    $workDateData = $dataWorkDayRes->fetch();

//    if ($workDateData['DATE_START']->format('H-i-s') < 1) {
//        echo 'PENIS';
//    }

    if ($workDateData['DATE_START']->format('H-i-s') > Date::createFromTimestamp(time())->add('+9 hours')) {
        LatenessTable::add([
            'PROFILE_ID' => $currentUser['ID'],
            'DATE_START' => CurrentUser::get()->getFirstName(),
            'DATE_START' => new DateTime,
            'DATE_STOP' => null,
        ]);
    }

//    echo '<pre>' . print_r($workDateData['DATE_START']->format('H-i-s'), true) . '</pre>';
//    echo '<pre>' . print_r(  new Date, true) . '</pre>';
//    echo '<pre>' . print_r(  Date::createFromTimestamp(time())->add('+9 hours'), true) . '</pre>';










}










require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
