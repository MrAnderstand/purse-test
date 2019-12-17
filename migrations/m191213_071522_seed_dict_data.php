<?php

use yii\db\Migration;

/**
 * Class m191213_071522_seed_dict_data
 */
class m191213_071522_seed_dict_data extends Migration
{
    public $transactionTypeTable = "public.transaction_type";
    public $transactionReasonTable = "public.transaction_reason";
    public $transactionTypeData = [
        [1, 'debit', 'NOW()', 'NOW()'],
        [2, 'credit', 'NOW()', 'NOW()'],
    ];
    public $transactionReasonData = [
        [1, 'stock', 'NOW()', 'NOW()'],
        [2, 'refund', 'NOW()', 'NOW()'],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert($this->transactionTypeTable, [
            'id', 'name', 'created_at', 'updated_at',
        ], $this->transactionTypeData)->execute();
        $this->execute("SELECT setval('transaction_type_id_seq', " . (count($this->transactionTypeData) + 1) . ", true);");

        Yii::$app->db->createCommand()->batchInsert($this->transactionReasonTable, [
            'id', 'name', 'created_at', 'updated_at',
        ], $this->transactionReasonData)->execute();
        $this->execute("SELECT setval('transaction_reason_id_seq', " . (count($this->transactionReasonData) + 1) . ", true);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()->delete($this->transactionTypeTable, [
            'id' => array_column($this->transactionTypeData, 0)
        ]);
        Yii::$app->db->createCommand()->delete($this->transactionReasonTable, [
            'id' => array_column($this->transactionReasonData, 0)
        ]);
    }
}
