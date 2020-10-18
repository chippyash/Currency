<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Test\Currency\Builder;

use Chippyash\Currency\Builder\CurrencyBuilder;

class CurrencyBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CurrencyBuilder
     */
    protected $sut;

    /**
     * @var string
     */
    protected $saveLocale;

    protected function setUp(): void
    {
        $this->saveLocale = \locale_get_default();
        locale_set_default('en_GB');
        $this->sut = new CurrencyBuilder('GBP');
    }

    protected function tearDown(): void
    {
        \locale_set_default($this->saveLocale);
    }

    public function testBuildWillCreateResultParametersForACurrency()
    {
        $this->assertTrue($this->sut->build());
        $result = $this->sut->getResult();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('symbol', $result);
        $this->assertArrayHasKey('displayFormat', $result);
        $this->assertArrayHasKey('locale', $result);
        $this->assertArrayHasKey('precision', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('GBP', $result['code']);
        $this->assertEquals('Pound Sterling', $result['name']);
        $this->assertEquals('£', $result['symbol']);
        $this->assertEquals('%s', $result['displayFormat']);
        $this->assertEquals('en_GB', $result['locale']);
        $this->assertEquals(2, $result['precision']);
        $this->assertEquals(0, $result['value']);
    }

}
