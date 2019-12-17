<?php

use app\models\TransactionRate;
use app\models\TransactionReason;
use app\models\TransactionType;
use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m191213_071613_seed_test_data
 */
class m191213_071613_seed_test_data extends Migration
{
    public $purseTable = "public.purse";
    public $transactionTable = "public.transaction";
    public $userTable = "public.user";
    public $transactionRateTable = "public.transaction_rate";

    public $purseData = [
        [1, 0, 'NOW()', 'NOW()'],
        [2, 0, 'NOW()', 'NOW()'],
        [3, 0, 'NOW()', 'NOW()'],
        [4, 0, 'NOW()', 'NOW()'],
        [5, 0, 'NOW()', 'NOW()'],
    ];
    public $transactionData = [];
    public $userData = [
        [1, 1, 'First', 'NOW()', 'NOW()'],
        [2, 2, 'Second', 'NOW()', 'NOW()'],
        [3, 3, 'Third', 'NOW()', 'NOW()'],
        [4, 4, 'Fourth', 'NOW()', 'NOW()'],
        [5, 5, 'Fifth', 'NOW()', 'NOW()'],
    ];
    public const USD_RATE = 65.25;
    public $transactionRateData = [
        [1, 'RUB', 1, 'NOW()', 'NOW()'],
        [2, 'USD', self::USD_RATE, 'NOW()', 'NOW()'],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert($this->purseTable, [
            'id', 'amount', 'created_at', 'updated_at',
        ], $this->purseData)->execute();
        $this->execute("SELECT setval('purse_id_seq', " . (count($this->purseData) + 1) . ", true);");

        Yii::$app->db->createCommand()->batchInsert($this->userTable, [
            'id', 'purse_id', 'username', 'created_at', 'updated_at',
        ], $this->userData)->execute();
        $this->execute("SELECT setval('user_id_seq', " . (count($this->userData) + 1) . ", true);");

        Yii::$app->db->createCommand()->batchInsert($this->transactionRateTable, [
            'id', 'name', 'amount', 'created_at', 'updated_at',
        ], $this->transactionRateData)->execute();
        $this->execute("SELECT setval('transaction_rate_id_seq', " . (count($this->transactionRateData) + 1) . ", true);");

        // Сгенерируем транзакции
        $purses = array_column($this->purseData, 0);
        for ($i = 0; $i < 10; $i++) {
            $daysAgo = rand(0, 10);
            $timestamp = new Expression("NOW() - '${daysAgo}d'::interval");

            $rateId = array_rand(TransactionRate::RATES);
            $rateActual = 1;

            if ($rateId != TransactionRate::RATE_RUB) {
                $rateActual = rand(self::USD_RATE * 100 - 300, self::USD_RATE * 100 + 300) / 100;
            }

            $this->transactionData[] = [
                $purses[array_rand($purses)],
                array_rand(TransactionType::TYPES),
                array_rand(TransactionReason::REASONS),
                $rateId,
                $rateActual,
                rand(0, 10000) / 100,
                $timestamp,
                $timestamp,
            ];
        }
        Yii::$app->db->createCommand()->batchInsert($this->transactionTable, [
            'purse_id', 'type_id', 'reason_id', 'rate_id', 'actual_rate', 'amount', 'created_at', 'updated_at',
        ], $this->transactionData)->execute();

        // Посчитаем и заполним суммы кошельков
        $sql = <<<SQL
        UPDATE {$this->purseTable}
        SET amount = data_table.amount
        FROM (
            SELECT t.purse_id, SUM(
                CASE tt.name = 'debit' 
                    WHEN true THEN
                        round(t.amount*t.actual_rate, 2)
                    ELSE
                        -round(t.amount*t.actual_rate, 2)
                END
            ) amount
            FROM public.transaction t
            INNER JOIN public.transaction_type tt ON t.type_id = tt.id
            GROUP BY t.purse_id
        ) data_table
        WHERE {$this->purseTable}.id = data_table.purse_id
SQL;
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()->delete($this->purseTable, ['id' => array_column($this->purseData, 0)]);
        Yii::$app->db->createCommand()->delete($this->userTable, ['id' => array_column($this->userData, 0)]);
        Yii::$app->db->createCommand()->delete($this->transactionRateTable, ['id' => array_column($this->transactionRateData, 0)]);
    }
}
