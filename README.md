####简版使用-主要增加自定义方法 放在验证类处理
[validation](https://github.com/hyperf/validation)
___________
#继承基础类
```php
<?php

namespace App\Validator;

use Fengdangxing\ValidatorQuote\ValidatorQuote;
use Hyperf\Validation\Validator;


class PublishValidator extends ValidatorQuote
{
    protected $scenes = [
        'publish' => ['id', 'mark'],
        'publish_retry' => ['id'],
        'addBatch' => ['merchant_site_id', 'merchant_page_id', 'mark']
    ];

    protected $extendList = ['check_publish'];

    protected $rules = [
        'merchant_site_id' => 'required|check_publish',
        'mark' => 'max:1000',
        'merchant_page_id' => 'check_type_empty',
        'merchant_component_id' => 'check_type',
        'merchant_color_id' => 'check_type',
        'id' => 'required',
    ];

    protected $messages = [
        'merchant_site_id.required' => "1000|merchant_site_id is empty",//code|message
        'id.required' => "1001|id is empty",
        'mark.max' => "1002|mark is empty",
        'merchant_page_id.check_type_empty' => "1003|merchant_page_id is empty",
        'merchant_site_id.check_publish' => "1004|check_publish"
    ];

    protected $filter = [
        'mark' => 'fliter_sql|fliter_str',
    ];

    public function check_publish($attribute, $value, $parameters, Validator $validator)
    {
        return true;
    }
}

```

#控制器
```php
<?php
namespace App\Controller;

use Fengdangxing\ValidatorQuote\Annotation\ValidatorQuote;

class IndexController extends AbstractController
{
    /**
     * @ValidatorQuote(class="\App\Validator\PublishValidator::class",scene="publish")
     */
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();
        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
}
```
