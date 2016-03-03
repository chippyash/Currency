<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Test\Currency;


use Chippyash\Currency\Currency;
use Chippyash\Type\Number\FloatType;
use Chippyash\Type\Number\IntType;
use Chippyash\Type\String\StringType;

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
        $this->sut->setLocale(new StringType('en_GB'));
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
        $this->assertEquals(2, $precision->getValue($this->sut)->get());
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
        $this->assertEquals('%s', $df->getValue($this->sut)->get());
    }

    public function testYouCanSetAndGetTheSymbol()
    {
        $symbol = new StringType('Foo');
        $this->assertEquals($symbol, $this->sut->setSymbol($symbol)->getSymbol());
    }

    public function testYouCanSetAndGetTheCode()
    {
        $code = new StringType('XXX');
        $this->assertEquals($code, $this->sut->setCode($code)->getCode());
    }

    public function testYouCanSetAndGetTheName()
    {
        $name = new StringType('FooBar');
        $this->assertEquals($name, $this->sut->setName($name)->getName());
    }

    public function testYouCanSetTheDisplayFormat()
    {
        $this->sut->setDisplayFormat(new StringType('Foo %s'));
        $this->assertEquals('Foo £1,200.26', $this->sut->display());
    }

    public function testYouCanSetTheLocale()
    {
        $this->sut->setLocale(new StringType('fr_FR'));
        $this->assertEquals('1 200,26 £', $this->sut->display());
    }

    public function testYouCanSetThePrecision()
    {
        $this->sut->setPrecision(new IntType(3));
        $this->assertEquals('£120.026', $this->sut->display());
    }

    public function testYouCanSetCurrencyValueUsingAFloatValue()
    {
        $this->assertEquals('£1,020.15', $this->sut->setAsFloat(1020.15)->display());
    }

    public function testYouCanSetCurrencyValueUsingAStrongFloatType()
    {
        $this->assertEquals('£1,020.15', $this->sut->setAsFloat(new FloatType(1020.15))->display());
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
        $this->assertEquals('£10.20', $this->sut->set(1020.15)->display());
    }

    public function testYouCanGetTheCurrencyValueAsAFloatValue()
    {
        $this->assertEquals(1200.26, $this->sut->getAsFloat());
    }

    public function testYouCanGetTheCurrencyValueAsAnIntegerValueRaisedToThePowerOfThePrecision()
    {
        $this->assertEquals(120026, $this->sut->get());
    }

    public function testDisplayObeysLocaleRules()
    {
        $this->assertEquals('£1,200.26', $this->sut->display());
        $this->sut->setLocale(new StringType('fr_FR'));
        $this->assertEquals('1 200,26 £', $this->sut->display());
        $this->sut->setLocale(new StringType('de_DE'));
        $this->assertEquals('1.200,26 £', $this->sut->display());

    }

}
