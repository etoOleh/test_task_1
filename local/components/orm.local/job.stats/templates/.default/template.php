<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Query;
use Orm\Local\OrmTable;
use Orm\Local\ProfileTable;

Loader::includeModule('iblock');

global $USER;

echo 'kot';
?>

<form method="post">
    <input type="submit" name="start" id="start" value="RUN" /><br/>
</form>

<?php

/**
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
function startWork()
{
//    $data = ProfileTable::getlist([
//            'select' => [
//                'ID',
//                'LOGIN',
//                'NAME',
//                'LAST_NAME',
//                'OFFSET'
//            ],
//    ]);
//
//    echo '<pre>' . print_r($data, 1) . '</pre>';
//
//    while($elements = $data->fetchAll()) {
//        echo '<pre>' . print_r($elements, 1) . '</pre>';
//    }

//    var_dump($elements);



//    $add1 = ProfileTable::add([
//    'LOGIN' => 'Название',
//    'NAME' => 'Название',
//    'LAST_NAME' => 'Название',
//    'OFFSET' => 'Название',
//    ]);


    $data = ProfileTable::getEntity();

    $dataRes =
        (new Query($data))
//            ->setFilter([
//
//            ])
            ->setSelect(['ID', 'LOGIN', 'NAME', 'LAST_NAME'])
            ->whereNotNull('LOGIN')
            ->exec();

    if (!$dataRes->fetch()) {
        echo 'pusto' . '<br>';
    }

    while($elements = $dataRes->fetch()) {

        if ($elements) {
            echo '123';
        } else {
            echo '321';
        }

        echo '<pre>' . print_r($elements, 1) . '</pre>';
        echo '<pre>' . var_dump($elements) . '</pre>';
    }


   echo "Ты начал раб день";

   //return $data->fetchAll();
}

function pauseStartWork()
{
    echo "Ты поставил время на паузу";
}

function pauseStopWork()
{
    echo "Ты продолжил раб день";
}

function stopWork()
{
    echo "Ты прекратил раб день";
}

if(array_key_exists('start',$_POST)){
    startWork();
}
if(array_key_exists('pause_start',$_POST)){
    pauseStartWork();
}
if(array_key_exists('pause_stop',$_POST)){
    pauseStopWork();
}
if(array_key_exists('stop',$_POST)){
    stopWork();
}


/**
 *
 * Добавление эл-нта для первого задания
 *
 */
/**
$add1 = OrmTable::add([
'NAME' => 'Название',
'DATE_INSERT' => '2024-08-27 21:55:55',
]);
 **/