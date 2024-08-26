<?php

namespace Orm\Local;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use \Bitrix\Main\Type\DateTime;

class WorkDayPauseTable extends DataManager
{
    /**
     * Имя таблицы
     */
    public static function getTableName(): string
    {
        return 'my_workday_pause_table';
    }

    /**
     * Описание таблицы
     * @throws ArgumentException|SystemException
     */
    public static function getMap(): array
    {
        return [
            new ORM\Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new ORM\Fields\IntegerField('WORKDAY_ID', [
                'required' => true,
            ]),
            (new Reference(
                'WORKDAY',
                WorkDayTable::class,
                Join::on('this.WORKDAY_ID', 'ref.ID')
            )),
            new ORM\Fields\DatetimeField('DATE_START', [
                'nullable' => true,
                'default_value' => static function() {
                    return new DateTime();
                }
            ]),
            new ORM\Fields\DatetimeField('DATE_STOP', [
                'nullable' => true,
                'default_value' => static function() {
                    return new DateTime();
                }
            ]),
        ];
    }
}
