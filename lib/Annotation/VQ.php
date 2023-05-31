<?php

namespace Fengdangxing\ValidatorQuote\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class VQ extends AbstractAnnotation
{
    public $class;//验证类名称(请输入全名称)
    public $scene;//场景

    public function __construct(...$value)
    {
        parent::__construct(...$value);
    }
}
