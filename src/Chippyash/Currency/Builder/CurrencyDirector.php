<?php

declare(strict_types=1);

/**
 * Currency

 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency\Builder;

use Chippyash\BuilderPattern\AbstractDirector;

/**
 * Currency class build director
 */
class CurrencyDirector extends AbstractDirector
{
    /**
     * @param string $crcyCode Currency code to build
     * @param string $namespace Namespace to implement class in
     * @param string $outDir Directory to output class code file to
     */
    public function __construct(string $crcyCode, string $namespace, string $outDir)
    {
        $builder = new CurrencyBuilder($crcyCode);
        $renderer = new CurrencyRenderer($namespace, $outDir);
        parent::__construct($builder, $renderer);
    }
}
