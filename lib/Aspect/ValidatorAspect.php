<?php

namespace Fengdangxing\ValidatorQuote\Aspect;

use Fengdangxing\ValidatorQuote\Annotation\VQ;
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
        VQ::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $list = AnnotationCollector::getMethodsByAnnotation($this->annotations[0]);
        foreach ($list as $key => $anno) {
            if ($proceedingJoinPoint->className == $anno['class'] && $proceedingJoinPoint->methodName == $anno['method']) {
                [$class, $className] = explode("::", $anno['annotation']->class);
                $validator = new $class();
                $validator->scenes($anno['annotation']->scene)->filter($this->getValidationData())->validator();
            }
        }
        return $proceedingJoinPoint->process();
    }

    protected function getValidationData(): array
    {
        $request = ApplicationContext::getContainer()->get(\Hyperf\HttpServer\Contract\RequestInterface::class);

        return array_merge_recursive($request->all(), $request->getUploadedFiles());
    }
}
