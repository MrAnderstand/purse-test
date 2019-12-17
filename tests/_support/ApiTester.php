<?php

use Flow\JSONPath\JSONPath;
use PHPUnit\Framework\Assert;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    /**
     * Добавляет в url параметры из массива
     * @param  string $url    Исходный url без параметров
     * @param  array  $params Массив параметров
     * @return string
     */
    public function makeUrl(string $url, array $params = []): string
    {
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }
   
    /**
    * Проверяет наличие значения в отфильтрованном подмассиве
    * @param  mixed        $needle    Ожидаемое значение
    * @param  string       $jsonPath  Путь к подмассиву
    * @param  bool|boolean $isVerbose Подробный вывод при ошибке
    */
    public function seeResponseContainsJsonPath($needle, $jsonPath, bool $isVerbose = false)
    {
        $textResponse = $this->grabResponse();
        $jsonResponse = json_decode($textResponse, true);
        $result = (new JSONPath($jsonResponse))->find($jsonPath);
        $data = $result->data();
        
        $description = "\n" . json_encode($result) . "\n";
        if ($isVerbose) {
            $description .= "\n" . $textResponse . "\n";
        }
        
        Assert::assertContains($needle, $data, $description);
    }
   
    /**
    * Проверяет отсутствие значения в отфильтрованном подмассиве
    * @param  mixed        $needle    Ожидаемое значение
    * @param  string       $jsonPath  Путь к подмассиву
    * @param  bool|boolean $isVerbose Подробный вывод при ошибке
    */
    public function dontSeeResponseContainsJsonPath($needle, $jsonPath, bool $isVerbose = false)
    {
        $textResponse = $this->grabResponse();
        $jsonResponse = json_decode($textResponse, true);
        $result = (new JSONPath($jsonResponse))->find($jsonPath);
        $data = $result->data();
        
        $description = "\n" . json_encode($result) . "\n";
        if ($isVerbose) {
            $description .= "\n" . $textResponse . "\n";
        }
        
        Assert::assertNotContains($needle, $data, $description);
    }
}
