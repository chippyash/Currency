<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Test\Currency;

use Chippyash\Currency\Factory;

/**
 * @see https://github.com/piece/stagehand-testrunner/issues/51
 */
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', dirname(dirname(__DIR__)) . '/vendor/autoload.php');
}

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $locale;

    /**
     * Tests expect to run in known locale
     */
    protected function SetUp(): void
    {
        $this->locale = locale_get_default();
    }

    /**
     * Reinstate system locale
     */
    protected function tearDown(): void
    {
        locale_set_default($this->locale);
    }

    public function testCreateWillReturnACurrency()
    {
        Factory::setLocale('en_GB');
        $crcy = Factory::create('GBP');
        $this->assertInstanceOf('Chippyash\Currency\Currency', $crcy);
        $this->assertEquals('£0.00', $crcy->display());
    }

    public function testCreateCanReturnACurrencyWithAnInitialValue()
    {
        Factory::setLocale('en_GB');
        $crcy = Factory::create('INR',2000.12);
        $this->assertInstanceOf('Chippyash\Currency\Currency', $crcy);
        $this->assertEquals('₹2,000.12', $crcy->display());
    }

    public function testCreateWillReturnCurrencyWithCodeIfNoSymbolAvailable()
    {
        Factory::setLocale('en_GB');
        $crcy = Factory::create('XUA',2000);
        $this->assertInstanceOf('Chippyash\Currency\Currency', $crcy);
        $this->assertEquals('XUA 2,000', $crcy->display());
    }

    public function testCreateWillReturnCurrencyRespectingExponentsForDisplay()
    {
        Factory::setLocale('en_GB');
        $crcy = Factory::create('OMR',2000);
        $this->assertInstanceOf('Chippyash\Currency\Currency', $crcy);
        $this->assertEquals('﷼2,000.000', $crcy->display());
    }

    public function testCreateWillThrowExceptionForUnknownCurrency()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown currency');
        $crcy = Factory::create('FOO');
    }

    /**
     * @runInSeparateProcess
     */
    public function testYouCanSetALocaleForCreatingCurrencies()
    {
        Factory::setLocale('fr_FR');
        $crcy = Factory::create('EUR',2000);
        $this->assertInstanceOf('Chippyash\Currency\Currency', $crcy);
        $this->assertEquals('2 000,00 €', $crcy->display());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateWillDefaultToCurrentDefaultLocaleIfNotSet()
    {
        $this->assertEquals(locale_get_default(), (string) Factory::getLocale());
    }

    /**
     * @runInSeparateProcess
     * @see http://en.wikipedia.org/wiki/Rombo_language
     * @see http://en.wikipedia.org/wiki/Tanzania
     */
    public function testCreateWillUseEnglishNameIfLanguageSpecificNameCannotBeFound()
    {
        Factory::setLocale('rof_TZ');
        $crcy = Factory::create('AFN',2000);
        $this->assertEquals('؋2,000.00', $crcy->display());
        $this->assertEquals('Afghani', $crcy->getName());
    }

    /**
     * @runInSeparateProcess
     * @see http://en.wikipedia.org/wiki/Rombo_language
     */
    public function testCreateWillUseFullLocaleIfLocaleSpecificNameCanBeFound()
    {
        Factory::setLocale('rof');
        $crcy = Factory::create('EUR',2000);
        $this->assertEquals('€2,000.00', $crcy->display());
        $this->assertEquals('yuro', $crcy->getName());
    }

}
