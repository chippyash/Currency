<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency\Builder;


use Chippyash\BuilderPattern\AbstractDirector;
use Chippyash\Type\String\StringType;

/**
 * Currency class build director
 */
class CurrencyDirector extends AbstractDirector
{
    /**
     * @param StringType $crcyCode Currency code to build
     * @param StringType $namespace Namespace to implement class in
     * @param StringType $outDir Directory to output class code file to
     */
    public function __construct(StringType $crcyCode, StringType $namespace, StringType $outDir)
    {
        $builder = new CurrencyBuilder($crcyCode);
        $renderer = new CurrencyRenderer($namespace, $outDir);
        parent::__construct($builder, $renderer);
    }
}