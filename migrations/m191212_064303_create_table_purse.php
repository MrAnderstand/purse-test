<?php

use yii\db\Migration;

/**
 * Class m191212_064303_create_table_purse
 */
class m191212_064303_create_table_purse extends Migration
{
    public $transactionTable = "public.transaction";
    public $transactionTypeTable = "public.transaction_type";
    public $transactionReasonTable = "public.transaction_reason";
    public $purseTable = "public.purse";
    public $userTable = "public.user";
    public $transactionRateTable = "public.transaction_rate";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->transactionTypeTable, [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull()->comment('Название'),
            'created_at' => $this->timestamp()->notNull()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->notNull()->comment('Дата изменения'),
        ]);

        $this->createTable($this->transactionReasonTable, [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull()->comment('Название'),
            'created_at' => $this->timestamp()->notNull()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->notNull()->comment('Дата изменения'),
        ]);

        $this->createTable($this->purseTable, [
            'id'         => $this->primaryKey(),
            'amount'     => $this->decimal(10, 2)->notNull()->comment('Сумма'),
            'created_at' => $this->timestamp()->notNull()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->notNull()->comment('Дата изменения'),
        ]);

        $this->createTable($this->transactionRateTable, [
            'id'         => $this->primaryKey(),
            'name'       => $this->string()->notNull()->comment('Имя'),
            'amount'     => $this->decimal(10, 2)->notNull()->comment('Курс валюты'),
            'created_at' => $this->timestamp()->notNull()->comment('Дата создания'),
            'updated_at' => $this->timestamp()->notNull()->comment('Дата изменения'),
        ]);

        $this->createTable($this->transactionTable, [
            'id'          => $this->primaryKey(),
            'purse_id'    => $this->integer()->notNull()->comment('Кошелек'),
            'type_id'     => $this->smallInteger()->notNull()->comment('Тип транзакции'),
            'reason_id'   => $this->smallInteger()->notNull()->comment('Причина изменения счета'),
            'rate_id'     => $this->smallInteger()->notNull()->comment('Валюта'),
            'actual_rate' => $this->decimal(10, 2)->notNull()->comment('Актуальный курс'),
            'amount'      => $this->decimal(10, 2)->notNull()->comment('Сумма'),
            'created_at'  => $this->timestamp()->notNull()->comment('Дата создания'),
            'updated_at'  => $this->timestamp()->notNull()->comment('Дата изменения'),
        ]);
        $this->createIndex('idx-transaction-purse_id', $this->transactionTable, 'purse_id');
        $this->addForeignKey('fk-transaction-purse_id',
            $this->transactionTable, 'purse_id',
            $this->purseTable, 'id',
            'CASCADE'
        );
        $this->createIndex('idx-transaction-type_id', $this->transactionTable, 'type_id');
        $this->addForeignKey('fk-transaction-type_id',
            $this->transactionTable, 'type_id',
            $this->transactionTypeTable, 'id',
            'RESTRICT'
        );
        $this->createIndex('idx-transaction-reason_id', $this->transactionTable, 'reason_id');
        $this->addForeignKey('fk-transaction-reason_id',
            $this->transactionTable, 'reason_id',
            $this->transactionReasonTable, 'id',
            'RESTRICT'
        );
        $this->createIndex('idx-transaction-rate_id', $this->transactionTable, 'rate_id');
        $this->addForeignKey('fk-transaction-rate_id',
            $this->transactionTable, 'rate_id',
            $this->transactionRateTable, 'id',
            'RESTRICT'
        );

        $this->createTable($this->userTable, [
            'id'           => $this->primaryKey(),
            'purse_id'     => $this->integer()->notNull()->comment('Кошелек'),
            'username'     => $this->string()->notNull()->comment('Имя'),
            'auth_key'     => $this->string(32)->comment('Auth key'),
            'created_at'   => $this->timestamp()->notNull()->comment('Дата создания'),
            'updated_at'   => $this->timestamp()->notNull()->comment('Дата изменения'),
        ]);
        $this->createIndex('idx-user-purse_id', $this->userTable, 'purse_id');
        $this->addForeignKey('fk-user-purse_id',
            $this->userTable, 'purse_id',
            $this->purseTable, 'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->transactionTable);
        $this->dropTable($this->transactionTypeTable);
        $this->dropTable($this->transactionReasonTable);
        $this->dropTable($this->purseTable);
        $this->dropTable($this->userTable);
        $this->dropTable($this->transactionRateTable);
    }
}
