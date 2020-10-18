<?php

declare(strict_types=1);

/**
 * Currency
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace Chippyash\Currency\Builder;

use Chippyash\BuilderPattern\AbstractBuilder;
use Chippyash\Currency\Factory;

/**
 * Hard currency builder
 */
class CurrencyBuilder extends AbstractBuilder
{
    /**
     * Currency code
     * @var string
     */
    protected $code;

    public function __construct(string $crcyCode)
    {
        $this->code = $crcyCode;
        parent::__construct();
    }

    /**
     * Set up the build items that this builder will manage
     * We don't need to do any complicated build routine so can set values here
     */
    protected function setBuildItems(): void
    {
        $crcy = Factory::create($this->code);

        $refl = new \ReflectionClass($crcy);
        $fmt = $refl->getProperty('displayFormat');
        $fmt->setAccessible(true);
        $locale = $refl->getProperty('locale');
        $locale->setAccessible(true);
        $precision = $refl->getProperty('precision');
        $precision->setAccessible(true);

        $this->buildItems = [
            'code' => $this->code,
            'name' => $crcy->getName(),
            'symbol' => $crcy->getSymbol(),
            'displayFormat' => $fmt->getValue($crcy),
            'locale' => $locale->getValue($crcy),
            'precision' => $precision->getValue($crcy),
            'value' => $crcy->getValue()
        ];
    }
}
