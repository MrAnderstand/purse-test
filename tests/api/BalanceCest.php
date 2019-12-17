<?php

use app\models\{
    TransactionRate,
    TransactionReason,
    TransactionType,
};
use Codeception\Util\HttpCode;
use PHPUnit\Framework\Assert;

class BalanceCest
{
    /** @var string Url */
    private const URL = '/api/balance';
    /** @var integer Id кошелька */
    private const PURSE_ID = 1;

    public function _before(\ApiTester $I)
    {
    }

    public function _after(\ApiTester $I)
    {
    }
    
    /**
     * Получение баланса
     * @param \ApiTester $I
     * @return void
     */
    private function _get(\ApiTester $I, int $purseId)
    {
        $I->amGoingTo('_get balance');

        $I->sendGET(self::URL, ['purseId' => $purseId]);
        $I->seeResponseIsJson();
        $I->dontSeeResponseJsonMatchesJsonPath('$.errors');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseJsonMatchesJsonPath('$.purseAmount');

        return $I->grabDataFromResponseByJsonPath('purseAmount')[0];
    }
    
    /**
     * Получение баланса
     * @param \ApiTester $I
     * @return void
     */
    public function get(\ApiTester $I)
    {
        $this->_get($I, self::PURSE_ID);
    }

    /**
     * Расход в долларах
     * @param \ApiTester $I
     * @return void
     */
    public function changeStockCreditUSD(\ApiTester $I)
    {
        $value = 51.35;
        $oldAmount = $this->_get($I, self::PURSE_ID);

        $I->amGoingTo('change balance stock credit usd');

        $transactionRate = TransactionRate::findOne(['id' => TransactionRate::RATE_USD]);
        
        $I->sendPOST(self::URL, [
            'purseId'           => self::PURSE_ID,
            'transactionType'   => TransactionType::TYPE_NAME_CREDIT,
            'transactionReason' => TransactionReason::REASON_NAME_STOCK,
            'transactionRate'   => $transactionRate->name,
            'amount'            => $value,
        ]);
        $I->seeResponseIsJson();
        $I->dontSeeResponseJsonMatchesJsonPath('$.errors');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseContainsJsonPath(true, '$.isSuccess');

        $newAmount = $this->_get($I, self::PURSE_ID);
        
        Assert::assertEquals($oldAmount - round($value * $transactionRate->amount * 100) / 100, $newAmount);
    }

    /**
     * Приход в рублях
     * @param \ApiTester $I
     * @return void
     */
    public function changeRefundDebitRUB(\ApiTester $I)
    {
        $value = 51.35;
        $oldAmount = $this->_get($I, self::PURSE_ID);

        $I->amGoingTo('change balance refund debit rub');

        $transactionRate = TransactionRate::findOne(['id' => TransactionRate::RATE_RUB]);
        
        $I->sendPOST(self::URL, [
            'purseId'           => self::PURSE_ID,
            'transactionType'   => TransactionType::TYPE_NAME_DEBIT,
            'transactionReason' => TransactionReason::REASON_NAME_REFUND,
            'transactionRate'   => $transactionRate->name,
            'amount'            => $value,
        ]);
        $I->seeResponseIsJson();
        $I->dontSeeResponseJsonMatchesJsonPath('$.errors');
        $I->seeResponseCodeIs(HttpCode::OK); // 200
        $I->seeResponseContainsJsonPath(true, '$.isSuccess');

        $newAmount = $this->_get($I, self::PURSE_ID);
        
        Assert::assertEquals($oldAmount + round($value * $transactionRate->amount * 100) / 100, $newAmount);
    }

    /**
     * Попытаемся зачислить ноль
     * @param \ApiTester $I
     * @return void
     */
    public function tryChangeZero(\ApiTester $I)
    {
        $value = 0;
        $I->amGoingTo('try change balance zero');

        $transactionRate = TransactionRate::findOne(['id' => TransactionRate::RATE_RUB]);
        
        $I->sendPOST(self::URL, [
            'purseId'           => self::PURSE_ID,
            'transactionType'   => TransactionType::TYPE_NAME_DEBIT,
            'transactionReason' => TransactionReason::REASON_NAME_REFUND,
            'transactionRate'   => $transactionRate->name,
            'amount'            => $value,
        ]);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContainsJsonPath(true, '$.isSuccess');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST); // 400
        $I->seeResponseJsonMatchesJsonPath('$.errors.amount');
    }

    /**
     * Попытаемся изменить баланс с неверным типом транзакции
     * @param \ApiTester $I
     * @return void
     */
    public function tryChangeWrongType(\ApiTester $I)
    {
        $value = 51.35;
        $I->amGoingTo('try change balance wrong type');

        $transactionRate = TransactionRate::findOne(['id' => TransactionRate::RATE_RUB]);
        
        $I->sendPOST(self::URL, [
            'purseId'           => self::PURSE_ID,
            'transactionType'   => 'oopps',
            'transactionReason' => TransactionReason::REASON_NAME_REFUND,
            'transactionRate'   => $transactionRate->name,
            'amount'            => $value,
        ]);
        $I->seeResponseIsJson();
        $I->dontSeeResponseContainsJsonPath(true, '$.isSuccess');
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST); // 400
        $I->seeResponseJsonMatchesJsonPath('$.errors.transactionType');
    }
}
