<?php

declare(strict_types=1);

/**
 * Currency

 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency\Builder;

use Chippyash\BuilderPattern\BuilderInterface;
use Chippyash\BuilderPattern\RendererInterface;

class CurrencyRenderer implements RendererInterface
{
    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $outDir;

    /**
     * @param string $namespace Namespace for new class
     * @param string $outDir Directory in which to save the class definition PHP file
     */
    public function __construct(string $namespace, string $outDir)
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
    public function render(BuilderInterface $builder): string
    {
        [$code, $name, $symbol, $displayFormat, $locale, $precision, $value] = array_values($builder->getResult());
        $tpl = file_get_contents(__DIR__ . '/CurrencyClass.tpl');
        $out = str_replace(
            [
                '<namespace>',
                '<name>',
                '<code>',
                '<value>',
                '<symbol>',
                '<precision>',
                '<displayFormat>',
                '<locale>'
            ],
            [
                $this->namespace,
                $name,
                $code,
                $value,
                $symbol,
                $precision,
                $displayFormat,
                $locale
            ],
            $tpl
        );
        file_put_contents($this->outDir . "/{$code}.php", $out);

        return $out;
    }
}
