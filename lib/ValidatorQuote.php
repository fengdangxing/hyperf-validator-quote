<?php

namespace Fengdangxing\ValidatorQuote;

use Fengdangxing\ValidatorQuote\Exception\ValidatorQuoteException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class ValidatorQuote
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;
    protected $scenes = []; // 场景设置
    protected $extendList = []; // 自定义方法验证
    protected $rules = [];//规则
    protected $messages = [];//提示信息

    private $KeyScenes; //当前场景

    const ERROR_SEPARATOR = '|';

    public function validator(array $params, array $rules = [], array $messages = [])
    {
        if (!empty($rules)) {
            $this->rules = $rules;
        }
        if (!empty($messages)) {
            $this->messages = $messages;
        }
        $this->checkScenes();
        $this->setExtendList();
        $validator = $this->validationFactory->make($params, $this->rules, $this->messages);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            [$code, $message] = explode(self::ERROR_SEPARATOR, $error);
            throw new \Exception($message, $code);
        }
    }

    public function scenes(string $key)
    {
        if ($key) {
            $this->KeyScenes = $key;
        }
        return $this;
    }

    public static function error(int $code, string $message = null)
    {
        return $message . self::ERROR_SEPARATOR . $code;
    }

    private function checkScenes()
    {
        if (!$this->KeyScenes) {
            return false;
        }
        $rulesKey = $this->scenes[$this->KeyScenes] ?? [];
        if (count($rulesKey)) {
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
            $this->validationFactory->extend($val, function ($attribute, $value, $parameters, ValidatorQuoteInterface $validator) use ($val) {
                return call_user_func([$this, $val], $attribute, $value, $parameters, $validator);
            });
        }
    }
}
