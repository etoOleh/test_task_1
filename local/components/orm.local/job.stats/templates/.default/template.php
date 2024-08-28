<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Query;
use Orm\Local\ProfileTable;
use \Bitrix\Main\Engine\CurrentUser;
use Orm\Local\WorkDayTable;
use Orm\Local\WorkDayPauseTable;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

Loader::includeModule('iblock');

global $USER;

echo 'kot';
?>

<form method="post">
    <input type="submit" name="start" id="start" value="RUN" /><br/>
</form>

<form method="post">
    <input type="submit" name="stop" id="stop" value="STOP" /><br/>
</form>

<form method="post">
    <input type="submit" name="pause_start" id="pause_start" value="PAUSE" /><br/>
</form>

<form method="post">
    <input type="submit" name="pause_stop" id="pause_stop" value="CONTINUE" /><br/>
</form>

<?php

//пользователя можно сюда вывести

$userEntity = ProfileTable::getEntity();
$workDayEntity = WorkDayTable::getEntity();
$workDayPauseEntity = WorkDayPauseTable::getEntity();

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


// так же можно вывести таблицу с началом раьботы
$dataWorkDayRes =
    (new Query($workDayEntity))
        ->setFilter([
            'PROFILE_ID' => $currentUser['ID'],
            '>DATE_START' => Date::createFromTimestamp(time())->toString(), //тут нужно еще доп условие
        ])
        ->setSelect(['ID', 'PROFILE_ID', 'DATE_START', 'DATE_STOP'])
        ->whereNotNull('DATE_START')
        ->exec();

$workDateData = $dataWorkDayRes->fetch();

$dataWorkPauseDayRes =
    (new Query($workDayPauseEntity))
        ->setSelect(['ID', 'WORKDAY_ID', 'DATE_START', 'DATE_STOP'])
        ->whereNotNull('DATE_START')
        ->setOrder(['ID' => 'DESC'])
        ->exec();

$workDateDataPause = $dataWorkPauseDayRes->fetch();


/**
 *
 * Функция для начала работы
 *
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 * @throws Exception
 */
function startWork($dataUserRes)
{

    if ($dataUserRes->getSelectedRowsCount() == 0)
    {
        ShowError(GetMessage("CHECK_IF_DATA_HERE"));
        return;

        // если пользвоателя нету то либо добавить либо ошибка (я поставил ошибку)

//        ProfileTable::add([
//        'LOGIN' => CurrentUser::get()->getLogin(),
//        'NAME' => CurrentUser::get()->getFirstName(),
//        'LAST_NAME' => CurrentUser::get()->getLastName(),
//      //  'OFFSET' => '',
//        ]);

    }

    if ($dataUserRes->getSelectedRowsCount() == 1) {
        ShowError(GetMessage("CHECK_IF_WORK_2"));
        return;
    }

    if ($dataUserRes->getSelectedRowsCount() > 1) {
        ShowError(GetMessage("CHECK_IF_WORK_2"));
        return;
    }

    while ($user = $dataUserRes->fetch()) {
        WorkDayTable::add([
            'PROFILE_ID' => $user['ID'],
            'NAME' => CurrentUser::get()->getFirstName(),
            'DATE_START' => new DateTime,
            'DATE_STOP' => null,
        ]);
    }

   echo "Ты начал раб день";
}

/**
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws Exception
 */
function pauseStartWork($dataWorkPauseDayRes, $workDateData)
{

    if ($dataWorkPauseDayRes->getSelectedRowsCount() == 0)
    {
        WorkDayPauseTable::add([
            'WORKDAY_ID' => $workDateData['ID'],
            'DATE_START' => new DateTime,
            'DATE_STOP' => null,
        ]);

    }

    WorkDayPauseTable::add([
        'WORKDAY_ID' => $workDateData['ID'],
        'DATE_START' => new DateTime,
        'DATE_STOP' => null,
    ]);

    if ($dataWorkPauseDayRes->getSelectedRowsCount() > 1) {
        ShowError(GetMessage("CHECK_IF_WORK_2"));
        return;
    }

    echo "Ты поставил время на паузу";
}

/**
 * @throws Exception
 */
function pauseStopWork($dataWorkPauseDay)
{

    WorkDayPauseTable::update($dataWorkPauseDay['ID'],[
        'DATE_STOP' => new DateTime,
    ]);

    echo "Ты продолжил раб день";
}

/**
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws Exception
 */
function stopWork($dataWorkDayRes)
{

    //проверка на то что чел начал работать
    // если нету старта работы выход
    // если тебя нету в системе выход
    //

    if ($dataWorkDayRes->getSelectedRowsCount() == 0) {
        ShowError(GetMessage("CHECK_IF_DATA_HERE_W"));
        return;
    }
    if ($dataWorkDayRes->getSelectedRowsCount() > 1) {
        ShowError(GetMessage("CHECK_IF_WORK_2"));
        return;
    }

    $dataObj = $dataWorkDayRes->fetch();

    WorkDayTable::update($dataObj['ID'],[
        'DATE_STOP' => new DateTime,
    ]);

    echo "Ты прекратил раб день";
}

if (array_key_exists('start',$_POST)) {
    startWork($dataUserRes);
}
if (array_key_exists('pause_start', $_POST)) {
    pauseStartWork($dataWorkPauseDayRes, $workDateData);
}
if (array_key_exists('pause_stop', $_POST)) {
    pauseStopWork($workDateDataPause);
}
if (array_key_exists('stop', $_POST)) {
    stopWork($dataWorkDayRes);
}
