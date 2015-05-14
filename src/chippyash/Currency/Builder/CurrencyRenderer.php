<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace chippyash\Currency\Builder;


use chippyash\BuilderPattern\BuilderInterface;
use chippyash\BuilderPattern\RendererInterface;
use chippyash\Type\String\StringType;

class CurrencyRenderer implements RendererInterface
{
    /**
     * @var StringType
     */
    protected $namespace;

    /**
     * @var StringType
     */
    protected $outDir;

    /**
     * @param StringType $namespace Namespace for new class
     * @param StringType $outDir Directory in which to save the class definition PHP file
     */
    public function __construct(StringType $namespace, StringType $outDir)
    {
        $this->namespace = $namespace;
        $this->outDir = $outDir;
    }

    /**
     * Render Currency class definition and save to target outDir
     *
     * @param BuilderInterface $builder Builder to be used for rendering
     * @return string Text of class that was built
     */
    public function render(BuilderInterface $builder)
    {
        list($code, $name, $symbol, $displayFormat, $locale, $precision, $value) = array_values($builder->getResult());
        $tpl = file_get_contents(__DIR__ . '/CurrencyClass.tpl');
        $out = str_replace(
            array(
                '<namespace>',
                '<name>',
                '<code>',
                '<value>',
                '<symbol>',
                '<precision>',
                '<displayFormat>',
                '<locale>'
            ),
            array(
                $this->namespace->get(),
                $name,
                $code,
                $value,
                $symbol,
                $precision,
                $displayFormat,
                $locale
            ),
            $tpl);
        file_put_contents($this->outDir->get() . "/{$code}.php", $out);

        return $out;
    }
}