<?php

/* @var $this yii\web\View */

$this->title = 'Purse Test';
?>
<div class="site-index">
    <h4>Получение баланса:</h4>
    <div class="code-javascript">
$.ajax({
    url: '/api/balance',
    data: {
        purseId: 1,
    },
    method: 'GET',
    dataType: 'JSON'
});
    </div>
    
    <h4>Изменение баланса:</h4>
    <div class="code-javascript">
$.ajax({
    url: '/api/balance',
    data: {
        purseId: 1,
        transactionType: 'debit',
        transactionReason: 'stock',
        transactionRate: 'RUB',
        amount: 1,
    },
    method: 'POST',
    dataType: 'JSON'
});
    </div>
</div>
