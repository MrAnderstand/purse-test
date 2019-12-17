<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "public.transaction_type".
 *
 * @property int $id
 * @property string $name Название
 * @property string $created_at Дата создания
 * @property string $updated_at Дата изменения
 */
class TransactionType extends \yii\db\ActiveRecord
{
    /** ID оплаты */
    public const TYPE_DEBIT = 1;
    /** ID возврата */
    public const TYPE_CREDIT = 2;
    /** Name оплаты */
    public const TYPE_NAME_DEBIT = 'debit';
    /** Name возврата */
    public const TYPE_NAME_CREDIT = 'credit';
    /** Список причин транзакции */
    public const TYPES = [
        self::TYPE_DEBIT => self::TYPE_NAME_DEBIT,
        self::TYPE_CREDIT => self::TYPE_NAME_CREDIT,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'public.transaction_type';
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
