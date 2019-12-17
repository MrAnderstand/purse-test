<?php

namespace app\models;

use yii\base\Model;

/**
 * Модель получения баланса
 */
class GetBalanceModel extends Model
{
    /** @var int Id кошелька */
    public $purseId;

    public function rules()
    {
        return [
            ['purseId', 'required'],
            ['purseId', 'integer'],
            ['purseId', 'exist', 'skipOnError' => true, 'targetClass' => Purse::class, 'targetAttribute' => [
                'purseId' => 'id'
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purseId' => 'Id кошелька',
        ];
    }
    
    public function getResult(): array
    {
        if ($this->validate(null, false)) {
            $data = (new GetBalanceQuery)->balance($this->purseId)->one();
        } else {
            $data = ['errors' => $this->errors];
        }
        
        return $data;
    }
}
