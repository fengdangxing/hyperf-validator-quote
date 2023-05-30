####简版使用-主要增加自定义方法 放在验证类处理
[validation](https://github.com/hyperf/validation)
___________
#继承基础类
```php
namespace App\Validator;

use App\Constants\ErrorCode;
use Fengdangxing\ValidatorQuote\ValidatorQuote;
use Fengdangxing\ValidatorQuote\ValidatorQuoteInterface;


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
            'id.required' => "merchant_site_id is empty",
            'mark.max' => "merchant_site_id is empty",
            'merchant_page_id.check_type_empty' => "merchant_site_id is empty",
            'merchant_site_id.check_publish' => "merchant_site_id is empty"
        ];
    public function check_publish($attribute, $value, $parameters, ValidatorQuoteInterface $validator)
    {
        return true;
    }
}
```

#控制器
```php
namespace App\Controller;

use App\Model\es\BatchEsModel;
use App\Model\PublishBatchModel;
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
