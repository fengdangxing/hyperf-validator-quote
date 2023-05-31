<?php

namespace Fengdangxing\ValidatorQuote;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class ValidatorQuote
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject()
     * @var Filter
     */
    protected $filterClass;

    protected $scenes = []; // 场景设置
    protected $extendList = []; // 自定义方法验证
    protected $rules = [];//规则
    protected $messages = [];//提示信息
    protected $filter = [];//过滤

    private $keyScenes; //当前场景
    const ERROR_SEPARATOR = '|';
    public $parmas = [];//参数

    public function validator()
    {
        $this->checkScenes();
        $this->setExtendList();
        $validator = $this->validationFactory->make($this->parmas, $this->rules, $this->messages);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            [$code, $message] = explode(self::ERROR_SEPARATOR, $error);
            throw new \Exception($message ?: $error, (int)$code);
        }
    }

    public static function validatorStatic(array $parmas, array $rules, array $messages)
    {
        $class = new self();
        $class->parmas = $parmas;
        $class->rules = $rules;
        $class->messages = $messages;
        $class->validator();
    }

    public function filter($params)
    {
        $this->parmas = $params;
        if (!$this->filter) {
            return $this;
        }
        $rulesKey = $this->scenes[$this->keyScenes] ?? [];
        foreach ($this->filter as $key => $value) {
            if ($rulesKey && !in_array($key, $rulesKey)) {
                continue;
            }
            $more = explode(self::ERROR_SEPARATOR, $value);
            foreach ($more as $v) {
                if (!$v) {
                    continue;
                }
                if (isset($params[$key])) {
                    $params[$key] = $this->filterClass->$v($params[$key]);
                }
                $this->parmas = $params;
            }
        }

        return $this;
    }

    public function scenes(string $key)
    {
        if ($key) {
            $this->keyScenes = $key;
        }
        return $this;
    }

    private function checkScenes()
    {
        if (!$this->keyScenes) {
            return false;
        }
        $rulesKey = $this->scenes[$this->keyScenes] ?? [];
        if (!count($rulesKey)) {
            return false;
        }
        foreach ($this->rules as $key => $val) {
            if (!in_array($key, $rulesKey)) {
                unset($this->rules[$key]);
            }
        }
    }

    private function setExtendList()
    {
        if (empty($this->extendList)) {
            return false;
        }
        foreach ($this->extendList as $val) {
            $this->validationFactory->extend($val, function ($attribute, $value, $parameters, $validator) use ($val) {
                return call_user_func([$this, $val], $attribute, $value, $parameters, $validator);
            });
        }
    }
}
