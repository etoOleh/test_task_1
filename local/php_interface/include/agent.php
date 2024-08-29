<?php

use Bitrix\Main\Loader;
use Orm\Local\ProfileTable;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Type\DateTime;
use Orm\Local\LatenessTable;
use Bitrix\Main\Config\Option;

/**
 * @throws \Bitrix\Main\ObjectException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\LoaderException
 * @throws \Bitrix\Main\ArgumentOutOfRangeException
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\SystemException
 */
function stopUserDayAgent()
{

    if (!Loader::includeModule("orm.local")) {
        return;
    }

    $i = 0;
    $currentDay = DateTime::createFromTimestamp(time())->format('d.m.Y');

    $currentDay2 = DateTime::createFromTimestamp(time())->add('+1 day')->format('d.m.Y');
    $targetTime = new DateTime($currentDay .' 09:00:00');
    $todayTime = new DateTime($currentDay);
    $lastTime = new DateTime($currentDay .' 00:00:00');


    $profileEntity = ProfileTable::getEntity();
    $profileRes =
        (new Query($profileEntity))
            ->setFilter([
                '>DATE_START' => $todayTime,
            ])
            ->setSelect([
                'ID',
                'LOGIN',
                'NAME',
                'LAST_NAME',
                'DATE_START' => 'Orm\Local\WorkDayTable:PROFILE.DATE_START',
                'DATE_STOP' => 'Orm\Local\WorkDayTable:PROFILE.DATE_STOP',
                'ID_WORKTABLE' => 'Orm\Local\WorkDayTable:PROFILE.ID',
            ])
            ->whereNotNull('LOGIN')
            ->whereNull('DATE_STOP')
            ->exec();


    $lastTime = new DateTime($currentDay2 .' 00:00:00');
    $count = Option::get("main", "count");

    if ($count == 24) {
        while ($dataProfileRes = $profileRes->fetch()) {
            Orm\Local\WorkDayTable::update(
                $dataProfileRes['ID_WORKTABLE'],
                [
                    'DATE_STOP' => $lastTime,
                ]);
        }
        Option::set("main", "count", 0);
    }

    $i++;
    Option::set("main", "count", $i);

    return 'stopUserDayAgent();';
}

/**
 * @throws \Bitrix\Main\ObjectException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\LoaderException
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\SystemException
 */
function checkLatenessAgent()
{
    if (!Loader::includeModule("orm.local")) {
        return;
    }

    $currentDay = DateTime::createFromTimestamp(time())->format('d.m.Y');
    $targetTime = new DateTime($currentDay .' 09:00:00');
    $todayTime = new DateTime($currentDay);


    $profileEntity = ProfileTable::getEntity();
    $profileRes =
        (new Query($profileEntity))
            ->setFilter([
                '>DATE_START' => $todayTime,
            ])
            ->setSelect([
                'ID',
                'LOGIN',
                'NAME',
                'LAST_NAME',
                'DATE_START' => 'Orm\Local\WorkDayTable:PROFILE.DATE_START'
            ])
            ->whereNotNull('LOGIN')
            ->exec();

    while ($dataProfileRes = $profileRes->fetch()) {
        $latenessEntity = LatenessTable::getEntity();
        $latenessRes =
            (new Query($latenessEntity))
                ->setFilter([
                    '=PROFILE_ID' => $dataProfileRes['ID'],
                    '>DATE' => $todayTime,
                ])
                ->setSelect([
                    'ID',
                    'PROFILE_ID',
                    'DATE',
                ])
                ->exec();

        //проверка на то один он тут или нет

        if ($latenessRes->getSelectedRowsCount() >= 1) {
            return;
        }

        if ($dataProfileRes['DATE_START'] > $targetTime) {

            LatenessTable::add([
                'PROFILE_ID' => $dataProfileRes['ID'],
                'DATE' => new DateTime(),
            ]);

        }
    }

    return 'checkLatenessAgent();';
}

function stopUserDayAgent_2()
{

    if (!Loader::includeModule("orm.local")) {
        return;
    }

    $currentDay = DateTime::createFromTimestamp(time())->format('d.m.Y');
    $currentDay2 = DateTime::createFromTimestamp(time())->add('-1 day')->format('d.m.Y');
    $lastTime = new DateTime($currentDay2 .' 00:00:00');


    $profileEntity = ProfileTable::getEntity();
    $profileRes =
        (new Query($profileEntity))
            ->setFilter([
                '<DATE_START' => $currentDay,
            ])
            ->setSelect([
                'ID',
                'LOGIN',
                'NAME',
                'LAST_NAME',
                'DATE_START' => 'Orm\Local\WorkDayTable:PROFILE.DATE_START',
                'DATE_STOP' => 'Orm\Local\WorkDayTable:PROFILE.DATE_STOP',
                'ID_WORKTABLE' => 'Orm\Local\WorkDayTable:PROFILE.ID',
            ])
            ->whereNotNull('LOGIN')
            ->whereNull('DATE_STOP')
            ->exec();
    while ($dataProfileRes = $profileRes->fetch()) {
        Orm\Local\WorkDayTable::update(
            $dataProfileRes['ID_WORKTABLE'],
            [
                'DATE_STOP' => $lastTime,
            ]);
    }



    return 'stopUserDayAgent_2();';
}