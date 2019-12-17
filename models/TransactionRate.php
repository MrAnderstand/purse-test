<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "public.transaction_rate".
 *
 * @property int $id
 * @property string $name Имя
 * @property float $amount Курс валюты
 * @property string $created_at Дата создания
 * @property string $updated_at Дата изменения
 */
class TransactionRate extends \yii\db\ActiveRecord
{
    /** ID курса рубля к валюте счета (рублю) */
    public const RATE_RUB = 1;
    /** ID курса доллара к валюте счета (рублю) */
    public const RATE_USD = 2;
    /** Name курса рубля */
    public const RATE_NAME_RUB = 'RUB';
    /** Name курса доллара */
    public const RATE_NAME_USD = 'USD';
    /** Список курсов */
    public const RATES = [
        self::RATE_RUB => self::RATE_NAME_RUB,
        self::RATE_USD => self::RATE_NAME_USD,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'public.transaction_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'amount'], 'required'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Имя',
            'amount'     => 'Курс валюты',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }
}
