<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "public.transaction".
 *
 * @property int $id
 * @property int $purse_id Кошелек
 * @property int $type_id Тип транзакции
 * @property int $reason_id Причина изменения счета
 * @property int $rate_id Валюта
 * @property float $amount Сумма
 * @property string $created_at Дата создания
 * @property string $updated_at Дата изменения
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'public.transaction';
    }

    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purse_id', 'type_id', 'reason_id', 'amount'], 'required'],
            [['purse_id', 'type_id', 'reason_id'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['purse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Purse::class, 'targetAttribute' => [
                'purse_id' => 'id'
            ]],
            [['reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionReason::class, 'targetAttribute' => [
                'reason_id' => 'id'
            ]],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionType::class, 'targetAttribute' => [
                'type_id' => 'id'
            ]],
            [['rate_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionRate::class, 'targetAttribute' => [
                'rate_id' => 'id'
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'purse_id'   => 'Кошелек',
            'type_id'    => 'Тип транзакции',
            'reason_id'  => 'Причина изменения счета',
            'rate_id'    => 'Валюта',
            'amount'     => 'Сумма',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }
}
