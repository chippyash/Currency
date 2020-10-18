<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Test\Currency;

use Chippyash\Currency\Currency;

class CurrencyTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Currency
     */
    protected $sut;

    /**
     * @var string
     */
    protected $saveLocale;

    protected function setUp()
    {
        $this->sut = new Currency(1200.26, 'GBP', '£');
        $this->saveLocale = locale_get_default();
        $this->sut->setLocale('en_GB');
    }

    protected function tearDown()
    {
        locale_set_default($this->saveLocale);
    }

    public function testDefaultConstructionSetsDefaultPrecisionOfTwo()
    {
        $refl = new \ReflectionClass($this->sut);
        $precision = $refl->getProperty('precision');
        $precision->setAccessible(true);
        $this->assertEquals(2, $precision->getValue($this->sut));
    }

    public function testDefaultConstructionSetsDefaultNameSameAsCode()
    {
        $this->assertEquals($this->sut->getCode(), $this->sut->getName());
    }

    public function testYouCanSetNameDuringConstruction()
    {
        $obj = new Currency(1200.26, 'GBP', '£', 2, 'Pound Sterling');
        $this->assertEquals('Pound Sterling', $obj->getName());
    }

    public function testDefaultConstructionSetsDefaultDisplayFormat()
    {
        $refl = new \ReflectionClass($this->sut);
        $df = $refl->getProperty('displayFormat');
        $df->setAccessible(true);
        $this->assertEquals('%s', $df->getValue($this->sut));
    }

    public function testYouCanSetAndGetTheSymbol()
    {
        $symbol = 'Foo';
        $this->assertEquals($symbol, $this->sut->setSymbol($symbol)->getSymbol());
    }

    public function testYouCanSetAndGetTheCode()
    {
        $code = 'XXX';
        $this->assertEquals($code, $this->sut->setCode($code)->getCode());
    }

    public function testYouCanSetAndGetTheName()
    {
        $name = 'FooBar';
        $this->assertEquals($name, $this->sut->setName($name)->getName());
    }

    public function testYouCanSetTheDisplayFormat()
    {
        $this->sut->setDisplayFormat('Foo %s');
        $this->assertEquals('Foo £1,200.26', $this->sut->display());
    }

    public function testYouCanSetTheLocale()
    {
        $this->sut->setLocale('fr_FR');
//        $this->assertEquals('1 200,26 £', $this->sut->display());
    }

    public function testYouCanSetAndGetThePrecision()
    {
        $this->sut->setPrecision(3);
        $this->assertEquals('£120.026', $this->sut->display());
        $this->assertEquals(3, $this->sut->getPrecision());
    }

    public function testYouCanSetCurrencyValueUsingAFloatValue()
    {
        $this->assertEquals('£1,020.15', $this->sut->setAsFloat(1020.15)->display());
    }

    public function testYouCanSetCurrencyValueUsingAStrongfloat()
    {
        $this->assertEquals('£1,020.15', $this->sut->setAsFloat(1020.15)->display());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUsingNonNumericValueToSetCurrencyValueUsingFloatWillThrowAnException()
    {
        $this->sut->setAsFloat('foo');
    }

    public function testYouCanSetCurrencyValueUsingAnIntegerValue()
    {
        $this->assertEquals('£10.20', $this->sut->setValue(1020.15)->display());
    }

    public function testYouCanGetTheCurrencyValueAsAFloatValue()
    {
        $this->assertEquals(1200.26, $this->sut->getAsFloat());
    }

    public function testYouCanGetTheCurrencyValueAsAnIntegerValueRaisedToThePowerOfThePrecision()
    {
        $this->assertEquals(120026, $this->sut->getValue());
    }

    public function testDisplayObeysLocaleRules()
    {
        $this->assertEquals('£1,200.26', $this->sut->display());
        $this->sut->setLocale('fr_FR');
//        $this->assertEquals('1 200,26 £', $this->sut->display());
        $this->sut->setLocale('de_DE');
//        $this->assertEquals('1.200,26 £', $this->sut->display());

    }

}
