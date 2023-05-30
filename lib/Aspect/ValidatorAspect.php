<?php

namespace Fengdangxing\ValidatorQuote\Aspect;

use Fengdangxing\ValidatorQuote\Annotation\ValidatorQuote;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Codec\Json;

/**
 * @Aspect
 */
class ValidatorAspect extends AbstractAspect
{
    public $classes = [];

    public $annotations = [
        ValidatorQuote::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $list = AnnotationCollector::getMethodsByAnnotation($this->annotations[0]);
        $request = ApplicationContext::getContainer()->get(\Hyperf\HttpServer\Contract\RequestInterface::class);
        foreach ($list as $key => $anno) {
            if ($proceedingJoinPoint->className == $anno['class'] && $proceedingJoinPoint->methodName == $anno['method']) {
                [$class, $className] = explode("::", $anno['annotation']->class);
                $validator = new $class();
                $validator->scenes($anno['annotation']->scene)->validator($request->all());
            }
        }
        $result = $proceedingJoinPoint->process();
        return $result;
    }
}