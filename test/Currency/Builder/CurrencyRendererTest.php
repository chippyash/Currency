<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Test\Currency\Builder;

use Chippyash\Currency\Builder\CurrencyRenderer;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class CurrencyRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyRenderer
     */
    protected $sut;

    /**
     * @var vfsStreamFile
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $saveLocale;

    protected function setUp()
    {
        $this->rootDir = vfsStream::setup('foo');
        $this->sut = new CurrencyRenderer('foo\bar', $this->rootDir->url());
    }


    public function testRenderWillCreateClassFileAndReturnClassText()
    {
        $mockBuilder = $this->getMockBuilder('Chippyash\Currency\Builder\CurrencyBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $mockBuilder->expects($this->once())
            ->method('getResult')
            ->willReturn(array(
                'code' => 'GBP',
                'name' => 'Pound Sterling',
                'symbol' => '£',
                'displayFormat' => '%s',
                'locale' => 'en_GB',
                'precision' => 2,
                'value' => 0
            ));

        $text = $this->sut->render($mockBuilder);
        $this->assertFileExists($this->rootDir->url() . '/GBP.php');
        $this->assertEquals($text, file_get_contents($this->rootDir->url() . '/GBP.php'));
    }
}
