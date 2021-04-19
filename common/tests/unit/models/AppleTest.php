<?php

namespace common\tests\unit\models;

use common\fixtures\AppleFixture;
use yii\base\UserException;
use yii\base\InvalidArgumentException;
use common\models\Apple;

class AppleTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'apple' => [
                'class' => AppleFixture::className(),
                'dataFile' => codecept_data_dir() . 'apple.php'
            ],
        ]);
    }

    public function testEatOnTree()
    {
        $this->tester->expectThrowable(UserException::class, function() {
            $apple = $this->tester->grabFixture('apple', 0);
            $apple->eat(10);
        });
    }

    public function testEatRotten()
    {
        $this->tester->expectThrowable(UserException::class, function() {
            $apple = $this->tester->grabFixture('apple', 2);
            $apple->eat(10);
        });
    }

    public function testEatMoreThenLeft()
    {
        $this->tester->expectThrowable(InvalidArgumentException::class, function() {
            $apple = $this->tester->grabFixture('apple', 1);
            $apple->eat(75);
        });
    }

    public function testFallFalled()
    {
        $this->tester->expectThrowable(UserException::class, function() {
            $apple = $this->tester->grabFixture('apple', 1);
            $apple->fallToGround();
        });
    }

    public function testNotExistedColor()
    {
        $this->tester->expectThrowable(InvalidArgumentException::class, function() {
            $apple = new Apple('white');
        });
    }

    public function testFall()
    {
        $apple = $this->tester->grabFixture('apple', 0);
        $apple->fallToGround();
        $this->tester->assertEquals(Apple::STATUS_FELL, $apple->status);
    }

    public function testEat()
    {
        $apple = $this->tester->grabFixture('apple', 1);
        $apple->eat(20);
        $this->tester->assertEquals(0.3, $apple->size);
    }
}
