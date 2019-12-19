<?php
require_once "autoload.php";

class CreditCardTest extends PHPUnit_Framework_TestCase
{
    public function testValidNumber()
    {
        $credit_card = new CreditCard();
        $this->assertTrue($credit_card->set('4444333322221111'));
    }

    public function testInvalidNumberShouldReturError()
    {
        $credit_card = new CreditCard();
        $this->assertEquals('ERROR_INVALID_LENGTH', $credit_card->set('3333555522221111'));
    }

    public function testValidNumberShouldSetAndGet()
    {
        $credit_card = new CreditCard();
        $credit_card->set('4444333322221111');
        $this->assertEquals('4444333322221111', $credit_card->get());
    }
}