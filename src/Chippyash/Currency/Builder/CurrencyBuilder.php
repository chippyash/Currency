<?php
/**
 * Currency
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace Chippyash\Currency\Builder;

use chippyash\BuilderPattern\AbstractBuilder;
use Chippyash\Currency\Factory;
use Chippyash\Type\String\StringType;

/**
 * Hard currency builder
 */
class CurrencyBuilder extends AbstractBuilder
{
    /**
     * Currency code
     * @var StringType
     */
    protected $code;

    public function __construct(StringType $crcyCode)
    {
        $this->code = $crcyCode;
        parent::__construct();
    }

    /**
     * Set up the build items that this builder will manage
     * We don't need to do any complicated build routine so can set values here
     */
    protected function setBuildItems()
    {
        $crcy = Factory::create($this->code);

        $refl = new \ReflectionClass($crcy);
        $fmt = $refl->getProperty('displayFormat'); $fmt->setAccessible(true);
        $locale = $refl->getProperty('locale'); $locale->setAccessible(true);
        $precision = $refl->getProperty('precision'); $precision->setAccessible(true);

        $this->buildItems = [
            'code' => $this->code,
            'name' => $crcy->getName()->get(),
            'symbol' => $crcy->getSymbol()->get(),
            'displayFormat' => $fmt->getValue($crcy)->get(),
            'locale' => $locale->getValue($crcy)->get(),
            'precision' => $precision->getValue($crcy)->get(),
            'value' => $crcy()
        ];
    }
}