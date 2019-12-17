<?php

namespace app\models;

class GetBalanceQuery extends \yii\db\Query
{
    /**
     * Возвращает состояние баланса
     * @param integer $purseId
     * @return GetBalanceQuery
     */
    public function balance(int $purseId): GetBalanceQuery
    {
        $this->from(['p' => Purse::tableName()])->select([
            'purseId'     => 'p.id',
            'purseAmount' => 'p.amount',
        ])->where([
            'p.id' => $purseId,
        ]);

        return $this;
    }
}
