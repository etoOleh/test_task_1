<?php

use Bitrix\Main\Loader;
use Orm\Local\ProfileTable;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Type\DateTime;
use Orm\Local\LatenessTable;
use Bitrix\Main\Config\Option;


function AgentCheckLateness()
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
                    '>DATE_START' => $todayTime,
                ])
                ->setSelect([
                    'ID',
                    'PROFILE_ID',
                    'DATE_START',
                ])
                ->exec();

        //проверка на то один он тут или нет

        if ($latenessRes->getSelectedRowsCount() >= 1) {
            return;
        }

        if ($dataProfileRes['DATE_START'] > $targetTime) {

            LatenessTable::add([
                'PROFILE_ID' => $dataProfileRes['ID'],
                'DATE_START' => new DateTime(),
            ]);

        }
    }

    return 'AgentCheckLateness();';
}

function test2()
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

    return 'test2();';
}
