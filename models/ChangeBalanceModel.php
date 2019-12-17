<?php

namespace app\models;

use yii\base\Model;

/**
 * Модель изменения баланса
 */
class ChangeBalanceModel extends Model
{
    /** @var float Минимальная сумма */
    private const MIN_AMOUNT = 0.01;
    /** @var int Id кошелька */
    public $purseId;
    /** @var string Тип транзакции */
    public $transactionType;
    /** @var string Причина транзакции */
    public $transactionReason;
    /** @var string Валюта транзакции */
    public $transactionRate;
    /** @var float Сумма */
    public $amount;

    /** @var bool Результат выполнения */
    private $result;

    public function rules()
    {
        return [
            [['purseId', 'transactionType', 'transactionReason', 'transactionRate', 'amount'], 'required'],

            ['purseId', 'integer'],
            ['purseId', 'exist', 'skipOnError' => true, 'targetClass' => Purse::class, 'targetAttribute' => [
                'purseId' => 'id'
            ]],
            ['transactionType', 'string', 'max' => 255],
            ['transactionType', 'exist', 'skipOnError' => true, 'targetClass' => TransactionType::class, 'targetAttribute' => [
                'transactionType' => 'name'
            ]],
            ['transactionReason', 'string', 'max' => 255],
            ['transactionReason', 'exist', 'skipOnError' => true, 'targetClass' => TransactionReason::class, 'targetAttribute' => [
                'transactionReason' => 'name'
            ]],
            ['transactionRate', 'string', 'max' => 255],
            [['transactionRate'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionRate::class, 'targetAttribute' => [
                'transactionRate' => 'name'
            ]],
            ['amount', 'number', 'min' => self::MIN_AMOUNT],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'purseId'           => 'Id кошелька',
            'transactionType'   => 'Тип транзакции',
            'transactionReason' => 'Причина транзакции',
            'transactionRate'   => 'Валюта транзакции',
            'amount'            => 'Сумма',
        ];
    }

    public function save(): bool
    {
        $this->result = false;

        if ($this->validate(null, false)) {
            $typeRow = TransactionType::findOne(['name' => $this->transactionType]);
            $typeId = $typeRow['id'];
            $reasonRow = TransactionReason::findOne(['name' => $this->transactionReason]);
            $reasonId = $reasonRow['id'];
            $rateRow = TransactionRate::findOne(['name' => $this->transactionRate]);
            $rateId = $rateRow['id'];
            $actualRate = $rateRow['amount'];

            $db = \Yii::$app->db;
            $transactionDB = $db->beginTransaction();

            try {
                // Сохраним данные транзакции
                $transaction = new Transaction();
                $transaction->purse_id = $this->purseId;
                $transaction->type_id = $typeId;
                $transaction->reason_id = $reasonId;
                $transaction->rate_id = $rateId;
                $transaction->amount = $this->amount;
                $transaction->actual_rate = $actualRate;

                if ($this->result = $transaction->save()) {
                    $purse = Purse::findOne(['id' => $this->purseId]);

                    if ($this->transactionType == TransactionType::TYPE_NAME_DEBIT) {
                        $purse->amount += $this->amount * $actualRate;
                    } else {
                        $purse->amount -= $this->amount * $actualRate;
                    }

                    // Изменим сумму кошелька
                    if (($this->result = $purse->save())) {
                        $transactionDB->commit();
                    } else {
                        $this->addError('purse', 'Ошибка изменения суммы кошелька');
                        $transactionDB->rollBack();
                    }
                } else {
                    $this->addError('transaction', 'Ошибка сохранения транзакции');
                    $transactionDB->rollBack();
                }
            } catch (\Throwable $e) {
                $this->addError('transaction', 'Ошибка сохранения транзакции');
                $transactionDB->rollBack();
            }
        }

        return $this->result;
    }
    
    public function getResult(): array
    {
        if ($this->validate(null, false)) {
            $data = [
                'isSuccess' => $this->result,
            ];
        } else {
            $data = ['errors' => $this->errors];
        }
        
        return $data;
    }
}
