<?php

namespace Orm\Local;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use \Bitrix\Main\Type\DateTime;
use Bitrix\Main\SystemException;

class LatenessTable extends DataManager
{
    /**
     * Имя таблицы
     */
    public static function getTableName(): string
    {
        return 'my_lateness_table';
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
            new ORM\Fields\IntegerField('PROFILE_ID', [
                'required' => true,
            ]),
            (new Reference(
                'PROFILE',
                ProfileTable::class,
                Join::on('this.PROFILE_ID', 'ref.ID')
            ))
            ->configureJoinType('left'),
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
