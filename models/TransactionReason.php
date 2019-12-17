<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "public.transaction_reason".
 *
 * @property int $id
 * @property string $name Название
 * @property string $created_at Дата создания
 * @property string $updated_at Дата изменения
 */
class TransactionReason extends \yii\db\ActiveRecord
{
    /** ID оплаты */
    public const REASON_STOCK = 1;
    /** ID возврата */
    public const REASON_REFUND = 2;
    /** Name оплаты */
    public const REASON_NAME_STOCK = 'stock';
    /** Name возврата */
    public const REASON_NAME_REFUND = 'refund';
    /** Список причин транзакции */
    public const REASONS = [
        self::REASON_STOCK => self::REASON_NAME_STOCK,
        self::REASON_REFUND => self::REASON_NAME_REFUND,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'public.transaction_reason';
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
            [['name'], 'required'],
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
            'name'       => 'Название',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }
}
