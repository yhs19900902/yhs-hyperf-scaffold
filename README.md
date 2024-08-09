# 使用规范：
## 1、常量

常量在app\Constants目录下，常量文件命名方式以Constant为后缀，如UriConstant、CommonConstant等等

比如使用UriConstant用作路由uri的常量，代码如下
```
<?php

declare(strict_types=1);

namespace App\Constants;

class UriConstant
{
    /**
     * 版本
     */
    public const VERSION = 'v1';
    /**
     * 首页前缀
     */
    public const INDEX_PREFIX = 'index';

    private function __construct()
    {
    }
}
```

## 2、控制器

> 定义路由我们使用注解来进行定义，尽可能不用配置文件就不用配置文件能使用注解就使用注解

```
<?php

declare(strict_types=1);


namespace App\Controller;

use App\Constants\CommonConstant;
use App\Constants\UriConstant;
use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\POJO\VO\Response\UserLoginResponseVO;
use App\Service\UserService;
use App\Utils\LogUtil;
use App\Utils\Stopwatch;
use Hyperf\ApiDocs\Annotation\Api;
use Hyperf\ApiDocs\Annotation\ApiHeader;
use Hyperf\ApiDocs\Annotation\ApiOperation;
use Hyperf\ApiDocs\Annotation\ApiResponse;
use Hyperf\Di\Annotation\Inject;
use Hyperf\DTO\Annotation\Contracts\RequestBody;
use Hyperf\DTO\Annotation\Contracts\Valid;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

#[Api(tags: '用户中心接口', description: '用户中心接口包含：用户中心收获地址、获取用户信息、登录、退出、注册')]
#[ApiHeader(name: 'Authorization', required: false, description: 'Bearer token')]
#[Controller(prefix: UriConstant::USER_PREFIX . CommonConstant::SLASH . UriConstant::VERSION)]
class UserController extends AbstractController
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(UserController::class);
    }

    /**
     * @var UserService 用户服务
     */
    #[Inject]
    protected UserService $userService;

    /**
     * 用户登录
     *
     * @param UserLoginRequestVO $userLoginRequestVO
     * @return ResponseInterface
     */
    #[PostMapping(path: UriConstant::LOGIN)]
    #[ApiOperation(summary: '用户登录')]
    #[ApiResponse(returnType: new BusinessResponse(UserLoginResponseVO::class))]
    public function login(#[RequestBody] #[Valid] UserLoginRequestVO $userLoginRequestVO): ResponseInterface
    {
        $this->logger->info("request user login parameter: {$userLoginRequestVO}");

        $stopwatch = new Stopwatch();
        $businessResponse = $this->userService->login($userLoginRequestVO);
        $this->logger->info("response user login api result: {$businessResponse}");
        $this->logger->info("response user login api cost: {$stopwatch->elapsed(CommonConstant::THREE)}ms");

        return $this->response->json($businessResponse->toArray());
    }
}
```
### 注解介绍
``` #[Api] ```注解是swagger的注解用于Api文档接口说明，详细可以到https://github.com/tw2066/api-docs 查看用法

``` #[ApiHeader()] ```注解就是在请求体中添加请求头的参数

``` #[Controller()] ```注解就是定义接口路由，详细可以到官网查看https://hyperf.wiki/3.1/#/zh-cn/router

Controller继承AbstractController抽象类，主要是返回使用``` $this->response->json() ```返回json结果。

``` LogUtil::get(UserController::class) ```这是脚手架中编写好的日志工具类

```
#[Inject]
protected UserService $userService;
 ```

这是调用服务的写法，可以参考官网https://hyperf.wiki/3.1/#/zh-cn/quick-start/overview?id=通过-inject-注解注入 类似springboot的@Resource和@Autowired那样

``` #[PostMapping()] ```post请求，对于路由Uri路径还有``` #[GetMapping()] ``` ``` #[PutMapping()] ``` ``` #[DeleteMapping()] ``` ``` #[RequestMapping()] ```

``` #[ApiOperation(summary: '用户登录')] ```注解是swapper中uri的标题

``` #[ApiResponse(returnType: new BusinessResponse(UserLoginResponseVO::class))] ``` 注解是swagger响应的返回对象参数，如返回的json是``` {"code":0, "message":"success", "data": [{"name":"小一", "age":12}, {"name":"小二", "age":10}]} ``` 那么写法可以``` #[ApiResponse(returnType: new BusinessResponse([UserResponse::class]))] ```

**重点1：** *``` #[RequestBody] ```注解是获取json的请求体，然后序列化转对象的注解，所有的请求参数都与对接接收，不要用数组接收，这是规范。该注解参考与springboot中的``` @RequestBody ```的注解*

``` #[Valid] ```是校验参数的注解

**重点2：** *``` businessResponse = $this->userService->login($userLoginRequestVO); ```返回的类型是BusinessResponse对象，必须所有的响应体返回该对象，该对象字段有code响应码、message响应信息、data响应体。*

*BusinessResponse对象主要方法：isSuccess()判断响应是否成功，ok(mixed $data)返回成功的响应，fail(int $code = 50000, string $message = 'service exception', mixed $data = null)返回错误的响应，toArray()转数组*

*另外有个公共类app\Common\BusinessResult.php，返回的也是BusinessResponse对象，在BusinessResponse的基础多了个failForEnum(BaseObject $baseObject)的方法，该返回传递参数是枚举，根据枚举返回错误信息，枚举案例代码如下：*
```
<?php

declare(strict_types=1);


namespace App\Enum;

use App\Common\BusinessResult;
use App\POJO\BusinessResponse;

enum UserResponseEnum
{
    case ERROR_LOGIN_TYPE;
    case AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY;
    case ACCOUNT_IS_EMPTY;
    case ERROR_LOGIN_ACCOUNT_TYPE;
    case PASSWORD_IS_EMPTY;
    case CAPTCHA_IS_EMPTY;

    public function response(): BusinessResponse
    {
        return match ($this) {
            self::ERROR_LOGIN_TYPE => BusinessResult::fail(50001, '登录类型错误！'),
            self::AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY => BusinessResult::fail(50002, '自动登录密文不能为空！'),
            self::ACCOUNT_IS_EMPTY => BusinessResult::fail(50003, '登录账号不能为空！'),
            self::ERROR_LOGIN_ACCOUNT_TYPE => BusinessResult::fail(50004, '错误账号登录类型！'),
            self::PASSWORD_IS_EMPTY => BusinessResult::fail(50005, '登录密码不能为空！'),
            self::CAPTCHA_IS_EMPTY => BusinessResult::fail(50006, '验证码不能为空！'),
        };
    }
}

```

使用案例``` return BusinessResult::failForEnum(UserResponseEnum::ACCOUNT_IS_EMPTY->response()); ```

## 3、VO，PO，DTO定义

### VO请求体/响应体定义

目录规范：app\POHO\VO\Request是请求目录，app\POJO\VO\Response是响应体目录，所有文件命名以VO为后缀

如：请求的VO，命名为：``` UserLoginRequestVO.php ```代码如下
```
<?php

declare(strict_types=1);


namespace App\POJO\VO\Request;

use App\Constants\LoginSourceConstant;
use App\Constants\LoginTypeConstant;
use App\POJO\JsonSerializableTrait;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\DTO\Annotation\Validation\In;
use Hyperf\DTO\Annotation\Validation\Str;
use PhpAccessor\Attribute\Data;

#[Data]
#[ApiModel(value: '登录请求参数')]
class UserLoginRequestVO implements \JsonSerializable
{
    use JsonSerializableTrait;

    #[ApiModelProperty(value: '登录来源 pc、app、wap、applet、wechat', example: 'pc', required: true)]
    #[Str]
    #[In(value: [LoginSourceConstant::PC, LoginSourceConstant::APP, LoginSourceConstant::WECHAT, LoginSourceConstant::APPLET, LoginSourceConstant::WAP], messages: '登录来源方式错误！')]
    private string $loginSource;

    #[ApiModelProperty(value: '登录类型 account账号密码登录 captcha验证码登录 autoLogin自动登录', example: 'account', required: true)]
    #[In(value: [LoginTypeConstant::ACCOUNT, LoginTypeConstant::CAPTCHA, LoginTypeConstant::AUTO_LOGIN], messages: '登录类型错误！')]
    #[Str]
    private string $loginType;

    #[ApiModelProperty(value: '登录账号 当loginType是account或captcha时必填', example: '13111111111')]
    #[Str]
    private string $account;

    #[ApiModelProperty(value: '登录密码 当loginType是account时必填', example: '123456')]
    #[Str]
    private string $password;

    #[ApiModelProperty(value: '登录验证码 当loginType是captcha时必填', example: '1234')]
    #[Str]
    private string $captcha;

    #[ApiModelProperty(value: '自动登录token 当loginType是autoLogin时必填', example: 'sadwadwanuahdnkwjandkjawugcsabnfeasb=')]
    #[Str]
    private string $automaticLoginToken;
}
```

``` #[Data] ```自动生成get和set的方法。

``` #[ApiModel] ```请求体的说明

``` #[ApiModelProperty] ``` 是字段说明

``` #[Str] #[In] ``` 是校验说明

``` #[ArrayType] ``` 是数组的泛型

``` use JsonSerializableTrait; ``` 作用用于序列化使用，输出日志自动转json格式

如：响应体vo，命名为：``` app\POJO\Response\UserLoginResponseVO ```，代码如下

```
<?php

declare(strict_types=1);


namespace App\POJO\VO\Response;

use App\POJO\JsonSerializableTrait;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\DTO\Annotation\Validation\Date;
use Hyperf\DTO\Annotation\Validation\Integer;
use Hyperf\DTO\Annotation\Validation\Str;
use PhpAccessor\Attribute\Data;

#[Data]
#[ApiModel(value: '登录响应参数')]
class UserLoginResponseVO implements \JsonSerializable
{
    use JsonSerializableTrait;

    #[ApiModelProperty(value: 'token', example: '1234567890abcdefg', required: true)]
    #[Str]
    private string $token;

    #[ApiModelProperty(value: '登录时间', example: '2022-01-01 12:00:00', required: true)]
    #[Date]
    private \DateTime $loginTime;

    #[ApiModelProperty(value: '过期时间', example: '2022-01-01 12:00:00', required: true)]
    #[Date]
    private \DateTime $expireTime;

    #[ApiModelProperty(value: '过期时间戳，精确秒', example: 1641056000, required: true)]
    #[Integer]
    private int $expire;
}
```

### PO数据库字段定义对象

目录规范：app\POJO\PO\UserPO.php，代码如下

```
<?php

declare(strict_types=1);


namespace App\POJO\PO;

use App\POJO\JsonSerializableTrait;
use PhpAccessor\Attribute\Data;

#[Data]
class UserPO extends BaseEntity implements \JsonSerializable
{
    use JsonSerializableTrait;
    private string $userName;
    private int $userLevel;
    private string $gender;
    private int $integral;
}
```

BaseEntity是数据库默认字段定义，所以创建数据库时必须要有id、created_date_time、created_by、updated_date_time、updated_by。

**数据库字段命名分隔下划线"_"，那么po的字段必须是驼峰式才能接收，id字段是字符类型使用uuid32位**

### DTO

DTO是应用架构中不同层之间传输数据，目录在app\POJO\DTO下

## 4、服务逻辑编写

服务类在``` app\Service ```创建服务的接口，在``` app\Service\Impl ```创建实现服务接口的服务方法，如：

用户服务：app\Service\UserService.php

```
<?php

declare(strict_types=1);


namespace App\Service;

use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;

interface UserService
{
    /**
     * 登录
     *
     * @param UserLoginRequestVO $userLoginRequestVO 用户登录参数
     * @return BusinessResponse
     */
    public function login(UserLoginRequestVO $userLoginRequestVO): BusinessResponse;
}
```

实现用户服务：app\Service\Impl\UserServiceImpl.php

```
<?php

declare(strict_types=1);


namespace App\Service\Impl;

use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\Service\Impl\Logic\LoginLogic;
use App\Service\Impl\Verification\LoginVerification;
use App\Service\UserService;
use App\Utils\LogUtil;
use Hyperf\Di\Annotation\Inject;
use HyperfHelper\Dependency\Annotation\Dependency;
use Psr\Log\LoggerInterface;

#[Dependency]
class UserServiceImpl implements UserService
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(UserServiceImpl::class);
    }

    /**
     * @var LoginVerification 检查用户登录参数
     */
    #[Inject]
    protected LoginVerification $loginVerification;

    /**
     * @var LoginLogic 登录逻辑
     */
    #[Inject]
    protected LoginLogic $loginLogic;

    /**
     * 登录
     *
     * @param UserLoginRequestVO $userLoginRequestVO
     * @return BusinessResponse
     */
    public function login(UserLoginRequestVO $userLoginRequestVO): BusinessResponse
    {
        $this->logger->info('start user login service.');

        // 校验参数
        $checkParameter = $this->loginVerification->check($userLoginRequestVO);
        if(!$checkParameter->isSuccess())
        {
            return $checkParameter;
        }

        // 登录逻辑
        return $this->loginLogic->exec($userLoginRequestVO);
    }
}
```

``` #[Dependency] ```是将服务注入到框架，类似Java的``` @Service ```的注解，详细可以查看https://github.com/lazychanger/hyperf-helper-dependency 因hyperf-helper-dependency项目长久没有更新，导致不兼容新的版本，所以我在它基础上修复了该BUG，项目代码是https://github.com/yhs19900902/hyperf-helper-dependency

```
    /**
     * @var LoginVerification 检查用户登录参数
     */
    #[Inject]
    protected LoginVerification $loginVerification;

    /**
     * @var LoginLogic 登录逻辑
     */
    #[Inject]
    protected LoginLogic $loginLogic;
```

LoginVerification是数据校验层，LoginLogic是数据逻辑实现层。我的项目开发设计理想是Controller->Service->Verification->Logic->BussinessResponse，意思是Api控制层接收参数 开始调用服务 在服务中校验参数 参数通过开始执行逻辑 最终输出结果。

因此，在Service目录中也有app\Service\Impl\Verification和app\Service\Impl\Logic的目录。

校验的文件也是以目录为后缀app\Service\Impl\Verification\LoginVerification，代码：

```
<?php

declare(strict_types=1);


namespace App\Service\Impl\Verification;

use App\Common\BusinessResult;
use App\Constants\LoginTypeConstant;
use App\Enum\UserResponseEnum;
use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\Utils\LogUtil;
use HyperfHelper\Dependency\Annotation\Dependency;
use Psr\Log\LoggerInterface;

#[Dependency]
class LoginVerification
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(LoginVerification::class);
    }

    /**
     * 检查用户登录参数
     *
     * @param UserLoginRequestVO $userLoginRequestVO 用户登录的参数
     * @return BusinessResponse
     */
    public function check(UserLoginRequestVO $userLoginRequestVO): BusinessResponse
    {
        $this->logger->info('check user login parameters verification.');

        // 根据登录类型判断参数
        switch ($userLoginRequestVO->getLoginType())
        {
            case LoginTypeConstant::ACCOUNT:
                // 判断账号是否为空
                if(empty($userLoginRequestVO->getAccount())) {
                    return BusinessResult::failForEnum(UserResponseEnum::ACCOUNT_IS_EMPTY->response());
                }
                // 判断密码是否为空
                if (empty($userLoginRequestVO->getPassword())) {
                    return BusinessResult::failForEnum(UserResponseEnum::PASSWORD_IS_EMPTY->response());
                }
                break;
            case LoginTypeConstant::CAPTCHA:
                // 判断账号是否为空
                if(empty($userLoginRequestVO->getAccount())) {
                    return BusinessResult::failForEnum(UserResponseEnum::ACCOUNT_IS_EMPTY->response());
                }
                // 判断密码是否为空
                if (empty($userLoginRequestVO->getCaptcha())) {
                    return BusinessResult::failForEnum(UserResponseEnum::CAPTCHA_IS_EMPTY->response());
                }
                break;
            case LoginTypeConstant::AUTO_LOGIN:
                // 判断自动登录token是否为空
                if(empty($userLoginRequestVO->getAutomaticLoginToken())) {
                    return BusinessResult::failForEnum(UserResponseEnum::AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY->response());
                }
                break;
            default:
                return BusinessResult::failForEnum(UserResponseEnum::ERROR_LOGIN_TYPE->response());
        }

        // 返回
        return BusinessResult::ok(null);
    }
}
```

逻辑层文件app\Service\Impl\Logic\LoginLogic
```
<?php

declare(strict_types=1);


namespace App\Service\Impl\Logic;

use App\Common\BusinessResult;
use App\Constants\CommonConstant;
use App\Enum\CallbackEnum;
use App\Model\UserModel;
use App\POJO\BusinessResponse;
use App\POJO\PO\UserPO;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\POJO\VO\Response\UserLoginResponseVO;
use App\Utils\LocalDateTime;
use App\Utils\LogUtil;
use Hyperf\DbConnection\Db;
use HyperfHelper\Dependency\Annotation\Dependency;
use Psr\Log\LoggerInterface;

#[Dependency]
class LoginLogic
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(LoginLogic::class);
    }

    /**
     * 登录逻辑
     *
     * @param UserLoginRequestVO $userLoginRequestVO 用户登录参数
     * @return BusinessResponse
     */
    public function exec(UserLoginRequestVO $userLoginRequestVO): BusinessResponse
    {
        $this->logger->info('start exec user login logic.');

        // 登录
        $expire = LocalDateTime::plusHour(CommonConstant::FOUR);
        $userLoginResponse = new UserLoginResponseVO();
        $userLoginResponse->setToken('token'); // 模拟返回token
        $userLoginResponse->setLoginTime(LocalDateTime::now()); // 模拟返回登录时间
        $userLoginResponse->setExpireTime(LocalDateTime::plusHour(CommonConstant::FOUR)); // 模拟返回过期时间
        $userLoginResponse->setExpire(LocalDateTime::timestampForSecond($expire)); // 模拟返回过期时间

        // 查数据库，同时将查询的数据返回给UserPO对象
        $userList = UserModel::query()->get()->map(CallbackEnum::MODEL_MAP_CALLBACK->callback(UserPO::class));
        var_dump($userList);

        return BusinessResult::ok($userLoginResponse);
    }
}
```
**重点3：** *代码中``` UserModel::query()->get()->map(CallbackEnum::MODEL_MAP_CALLBACK->callback(UserPO::class)); ```查询数据库后得到的数据反射给UserPO对象，该写法中map查询是使用了枚举的callback来实现，枚举代码是*

```
<?php

namespace App\Enum;

use App\Constants\CommonConstant;
use ReflectionClass;

enum CallbackEnum
{
    /**
     * 数据库查询结果反射到对象  查询数据时使用map()操作,案例:Model::query()->get()->map(CallbackEnum::MODEL_MAP_CALLBACK->callback(OrderPO::class));
     */
    case MODEL_MAP_CALLBACK;

    public function callback(?string $className = null): callable
    {
        return match ($this) {
            self::MODEL_MAP_CALLBACK => function ($value) use ($className) {
                // 获取赋值的对象名
                $className = $className ?? $value->className;

                // 判断是否存在对象
                if (empty($className) || !class_exists($className)) {
                    return $value;
                }

                // 初始化一个对象
                $objectClass = new $className();
                $reflectionClass = new ReflectionClass($objectClass);

                // 将查询结果转数组进行反射
                foreach ($value->toArray() as $k => $v) {
                    // 过滤空值
                    if (null == $v) {
                        continue;
                    }

                    // 已"_"下划线进行分割
                    $parts = explode(CommonConstant::SYMBOL_UNDERSCORE, $k);
                    // 拼接方法
                    $propertyName = 'set' . implode(CommonConstant::EMPTY, array_map('ucfirst', $parts));
                    // 判断对象中的方法是否存在
                    if ($reflectionClass->hasMethod($propertyName)) {
                        $reflectionProperty = $reflectionClass->getMethod($propertyName);
                        $reflectionProperty->invoke($objectClass, $v);
                    }
                }
                // 返回对象结果
                return $objectClass;
            },
        };
    }
}

```

# 框架的工具类

在app\Utils目录下有部分写好的工具类

``` LocalDateTime.php ```是日期的使用类，可以简化日期的使用

``` LogUtil.php ```是打印日志的工具类

``` Stopwatch.php ```是计算接口的时间，使用案例可以查看Controller层

``` ToolsUtil.php ```是常用的一些工具类，如生成uuid、获取ip地址等等

``` SeataUtil.php ```是用于手动回滚使用，该工具类可以在Controller层结合BusinessResponse对象返回的isSuccess()方法判断用户手动回滚。该工具留给大家自己实现

# 整体demo案例流程

## Controller路由入口

```
<?php

declare(strict_types=1);


namespace App\Controller;

use App\Constants\CommonConstant;
use App\Constants\UriConstant;
use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\POJO\VO\Response\UserLoginResponseVO;
use App\Service\UserService;
use App\Utils\LogUtil;
use App\Utils\Stopwatch;
use Hyperf\ApiDocs\Annotation\Api;
use Hyperf\ApiDocs\Annotation\ApiHeader;
use Hyperf\ApiDocs\Annotation\ApiOperation;
use Hyperf\ApiDocs\Annotation\ApiResponse;
use Hyperf\Di\Annotation\Inject;
use Hyperf\DTO\Annotation\Contracts\RequestBody;
use Hyperf\DTO\Annotation\Contracts\Valid;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

#[Api(tags: '用户中心接口', description: '用户中心接口包含：用户中心收获地址、获取用户信息、登录、退出、注册')]
#[ApiHeader(name: 'Authorization', required: false, description: 'Bearer token')]
#[Controller(prefix: UriConstant::USER_PREFIX . CommonConstant::SLASH . UriConstant::VERSION)]
class UserController extends AbstractController
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(UserController::class);
    }

    /**
     * @var UserService 用户服务
     */
    #[Inject]
    protected UserService $userService;

    /**
     * 用户登录
     *
     * @param UserLoginRequestVO $userLoginRequestVO
     * @return ResponseInterface
     */
    #[PostMapping(path: UriConstant::LOGIN)]
    #[ApiOperation(summary: '用户登录')]
    #[ApiResponse(returnType: new BusinessResponse(UserLoginResponseVO::class))]
    public function login(#[RequestBody] #[Valid] UserLoginRequestVO $userLoginRequestVO): ResponseInterface
    {
        $this->logger->info("request user login parameter: {$userLoginRequestVO}");

        $stopwatch = new Stopwatch();
        $businessResponse = $this->userService->login($userLoginRequestVO);
        $this->logger->info("response user login api result: {$businessResponse}");
        $this->logger->info("response user login api cost: {$stopwatch->elapsed(CommonConstant::THREE)}ms");

        return $this->response->json($businessResponse->toArray());
    }
}
```

## Constant常量目录

```
<?php

declare(strict_types=1);


namespace App\Constants;

class UriConstant
{
    /**
     * 版本
     */
    public const VERSION = 'v1';
    /**
     * 首页前缀
     */
    public const INDEX_PREFIX = 'index';

    private function __construct()
    {
    }
}
```

```
<?php

declare(strict_types=1);


namespace App\Constants;

class LoginTypeConstant
{
    private function __construct(){}
    public const ACCOUNT = 'account';
    public const CAPTCHA = 'captcha';
    public const AUTO_LOGIN = 'autoLogin';
}
```

```
<?php

declare(strict_types=1);


namespace App\Constants;

class LoginSourceConstant
{
    private function __construct(){}

    public const PC = 'pc';
    public const WAP = 'wap';
    public const APP = 'app';
    public const APPLET = 'applet';
    public const WECHAT = 'wechat';
}
```

## Enum枚举

```
<?php

namespace App\Enum;

use App\Constants\CommonConstant;
use ReflectionClass;

enum CallbackEnum
{
    /**
     * 数据库查询结果反射到对象  查询数据时使用map()操作,案例:Model::query()->get()->map(CallbackEnum::MODEL_MAP_CALLBACK->callback(OrderPO::class));
     */
    case MODEL_MAP_CALLBACK;

    public function callback(?string $className = null): callable
    {
        return match ($this) {
            self::MODEL_MAP_CALLBACK => function ($value) use ($className) {
                // 获取赋值的对象名
                $className = $className ?? $value->className;

                // 判断是否存在对象
                if (empty($className) || !class_exists($className)) {
                    return $value;
                }

                // 初始化一个对象
                $objectClass = new $className();
                $reflectionClass = new ReflectionClass($objectClass);

                // 将查询结果转数组进行反射
                foreach ($value->toArray() as $k => $v) {
                    // 过滤空值
                    if (null == $v) {
                        continue;
                    }

                    // 已"_"下划线进行分割
                    $parts = explode(CommonConstant::SYMBOL_UNDERSCORE, $k);
                    // 拼接方法
                    $propertyName = 'set' . implode(CommonConstant::EMPTY, array_map('ucfirst', $parts));
                    // 判断对象中的方法是否存在
                    if ($reflectionClass->hasMethod($propertyName)) {
                        $reflectionProperty = $reflectionClass->getMethod($propertyName);
                        $reflectionProperty->invoke($objectClass, $v);
                    }
                }
                // 返回对象结果
                return $objectClass;
            },
        };
    }
}

```

```
<?php

declare(strict_types=1);


namespace App\Enum;

use App\Common\BusinessResult;
use App\POJO\BusinessResponse;

enum UserResponseEnum
{
    case ERROR_LOGIN_TYPE;
    case AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY;
    case ACCOUNT_IS_EMPTY;
    case ERROR_LOGIN_ACCOUNT_TYPE;
    case PASSWORD_IS_EMPTY;
    case CAPTCHA_IS_EMPTY;

    public function response(): BusinessResponse
    {
        return match ($this) {
            self::ERROR_LOGIN_TYPE => BusinessResult::fail(50001, '登录类型错误！'),
            self::AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY => BusinessResult::fail(50002, '自动登录密文不能为空！'),
            self::ACCOUNT_IS_EMPTY => BusinessResult::fail(50003, '登录账号不能为空！'),
            self::ERROR_LOGIN_ACCOUNT_TYPE => BusinessResult::fail(50004, '错误账号登录类型！'),
            self::PASSWORD_IS_EMPTY => BusinessResult::fail(50005, '登录密码不能为空！'),
            self::CAPTCHA_IS_EMPTY => BusinessResult::fail(50006, '验证码不能为空！'),
        };
    }
}

```

## Model数据库层

```
<?php

declare(strict_types=1);


namespace App\Model;

class UserModel extends Model
{
    protected ?string $table = 'user';
    protected string $primaryKey = 'id';
    protected string $keyType = 'string';
    protected array $fillable = ['id','user_name','user_level','gender','integral','created_date_time','created_by','updated_date_time','updated_by'];
    protected array $casts = ['user_level' => 'integer', 'integral'=>'integer'];
}
```

## PO层

```
<?php

declare(strict_types=1);


namespace App\POJO\PO;

use App\POJO\JsonSerializableTrait;
use PhpAccessor\Attribute\Data;

#[Data]
class UserPO extends BaseEntity implements \JsonSerializable
{
    use JsonSerializableTrait;
    private string $userName;
    private int $userLevel;
    private string $gender;
    private int $integral;
}
```

## vo层

```
<?php

declare(strict_types=1);


namespace App\POJO\VO\Request;

use App\Constants\LoginSourceConstant;
use App\Constants\LoginTypeConstant;
use App\POJO\JsonSerializableTrait;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\DTO\Annotation\Validation\In;
use Hyperf\DTO\Annotation\Validation\Str;
use PhpAccessor\Attribute\Data;

#[Data]
#[ApiModel(value: '登录请求参数')]
class UserLoginRequestVO implements \JsonSerializable
{
    use JsonSerializableTrait;

    #[ApiModelProperty(value: '登录来源 pc、app、wap、applet、wechat', example: 'pc', required: true)]
    #[Str]
    #[In(value: [LoginSourceConstant::PC, LoginSourceConstant::APP, LoginSourceConstant::WECHAT, LoginSourceConstant::APPLET, LoginSourceConstant::WAP], messages: '登录来源方式错误！')]
    private string $loginSource;

    #[ApiModelProperty(value: '登录类型 account账号密码登录 captcha验证码登录 autoLogin自动登录', example: 'account', required: true)]
    #[In(value: [LoginTypeConstant::ACCOUNT, LoginTypeConstant::CAPTCHA, LoginTypeConstant::AUTO_LOGIN], messages: '登录类型错误！')]
    #[Str]
    private string $loginType;

    #[ApiModelProperty(value: '登录账号 当loginType是account或captcha时必填', example: '13111111111')]
    #[Str]
    private string $account;

    #[ApiModelProperty(value: '登录密码 当loginType是account时必填', example: '123456')]
    #[Str]
    private string $password;

    #[ApiModelProperty(value: '登录验证码 当loginType是captcha时必填', example: '1234')]
    #[Str]
    private string $captcha;

    #[ApiModelProperty(value: '自动登录token 当loginType是autoLogin时必填', example: 'sadwadwanuahdnkwjandkjawugcsabnfeasb=')]
    #[Str]
    private string $automaticLoginToken;
}
```

```
<?php

declare(strict_types=1);


namespace App\POJO\VO\Response;

use App\POJO\JsonSerializableTrait;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\DTO\Annotation\Validation\Date;
use Hyperf\DTO\Annotation\Validation\Integer;
use Hyperf\DTO\Annotation\Validation\Str;
use PhpAccessor\Attribute\Data;

#[Data]
#[ApiModel(value: '登录响应参数')]
class UserLoginResponseVO implements \JsonSerializable
{
    use JsonSerializableTrait;

    #[ApiModelProperty(value: 'token', example: '1234567890abcdefg', required: true)]
    #[Str]
    private string $token;

    #[ApiModelProperty(value: '登录时间', example: '2022-01-01 12:00:00', required: true)]
    #[Date]
    private \DateTime $loginTime;

    #[ApiModelProperty(value: '过期时间', example: '2022-01-01 12:00:00', required: true)]
    #[Date]
    private \DateTime $expireTime;

    #[ApiModelProperty(value: '过期时间戳，精确秒', example: 1641056000, required: true)]
    #[Integer]
    private int $expire;
}
```

## Service服务层

```
<?php

declare(strict_types=1);


namespace App\Service;

use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;

interface UserService
{
    /**
     * 登录
     *
     * @param UserLoginRequestVO $userLoginRequestVO 用户登录参数
     * @return BusinessResponse
     */
    public function login(UserLoginRequestVO $userLoginRequestVO): BusinessResponse;
}
```

## Service服务Impl层

```
<?php

declare(strict_types=1);


namespace App\Service\Impl;

use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\Service\Impl\Logic\LoginLogic;
use App\Service\Impl\Verification\LoginVerification;
use App\Service\UserService;
use App\Utils\LogUtil;
use Hyperf\Di\Annotation\Inject;
use HyperfHelper\Dependency\Annotation\Dependency;
use Psr\Log\LoggerInterface;

#[Dependency]
class UserServiceImpl implements UserService
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(UserServiceImpl::class);
    }

    /**
     * @var LoginVerification 检查用户登录参数
     */
    #[Inject]
    protected LoginVerification $loginVerification;

    /**
     * @var LoginLogic 登录逻辑
     */
    #[Inject]
    protected LoginLogic $loginLogic;

    /**
     * 登录
     *
     * @param UserLoginRequestVO $userLoginRequestVO
     * @return BusinessResponse
     */
    public function login(UserLoginRequestVO $userLoginRequestVO): BusinessResponse
    {
        $this->logger->info('start user login service.');

        // 校验参数
        $checkParameter = $this->loginVerification->check($userLoginRequestVO);
        if(!$checkParameter->isSuccess())
        {
            return $checkParameter;
        }

        // 登录逻辑
        return $this->loginLogic->exec($userLoginRequestVO);
    }
}
```

## Verification校验层

```
<?php

declare(strict_types=1);


namespace App\Service\Impl\Verification;

use App\Common\BusinessResult;
use App\Constants\LoginTypeConstant;
use App\Enum\UserResponseEnum;
use App\POJO\BusinessResponse;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\Utils\LogUtil;
use HyperfHelper\Dependency\Annotation\Dependency;
use Psr\Log\LoggerInterface;

#[Dependency]
class LoginVerification
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(LoginVerification::class);
    }

    /**
     * 检查用户登录参数
     *
     * @param UserLoginRequestVO $userLoginRequestVO 用户登录的参数
     * @return BusinessResponse
     */
    public function check(UserLoginRequestVO $userLoginRequestVO): BusinessResponse
    {
        $this->logger->info('check user login parameters verification.');

        // 根据登录类型判断参数
        switch ($userLoginRequestVO->getLoginType())
        {
            case LoginTypeConstant::ACCOUNT:
                // 判断账号是否为空
                if(empty($userLoginRequestVO->getAccount())) {
                    return BusinessResult::failForEnum(UserResponseEnum::ACCOUNT_IS_EMPTY->response());
                }
                // 判断密码是否为空
                if (empty($userLoginRequestVO->getPassword())) {
                    return BusinessResult::failForEnum(UserResponseEnum::PASSWORD_IS_EMPTY->response());
                }
                break;
            case LoginTypeConstant::CAPTCHA:
                // 判断账号是否为空
                if(empty($userLoginRequestVO->getAccount())) {
                    return BusinessResult::failForEnum(UserResponseEnum::ACCOUNT_IS_EMPTY->response());
                }
                // 判断密码是否为空
                if (empty($userLoginRequestVO->getCaptcha())) {
                    return BusinessResult::failForEnum(UserResponseEnum::CAPTCHA_IS_EMPTY->response());
                }
                break;
            case LoginTypeConstant::AUTO_LOGIN:
                // 判断自动登录token是否为空
                if(empty($userLoginRequestVO->getAutomaticLoginToken())) {
                    return BusinessResult::failForEnum(UserResponseEnum::AUTOMATIC_LOGIN_CIPHERTEXT_IS_EMPTY->response());
                }
                break;
            default:
                return BusinessResult::failForEnum(UserResponseEnum::ERROR_LOGIN_TYPE->response());
        }

        // 返回
        return BusinessResult::ok(null);
    }
}
```

## Logic逻辑层

```
<?php

declare(strict_types=1);


namespace App\Service\Impl\Logic;

use App\Common\BusinessResult;
use App\Constants\CommonConstant;
use App\Enum\CallbackEnum;
use App\Model\UserModel;
use App\POJO\BusinessResponse;
use App\POJO\PO\UserPO;
use App\POJO\VO\Request\UserLoginRequestVO;
use App\POJO\VO\Response\UserLoginResponseVO;
use App\Utils\LocalDateTime;
use App\Utils\LogUtil;
use Hyperf\DbConnection\Db;
use HyperfHelper\Dependency\Annotation\Dependency;
use Psr\Log\LoggerInterface;

#[Dependency]
class LoginLogic
{
    /**
     * @var LoggerInterface 日志
     */
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = LogUtil::get(LoginLogic::class);
    }

    /**
     * 登录逻辑
     *
     * @param UserLoginRequestVO $userLoginRequestVO 用户登录参数
     * @return BusinessResponse
     */
    public function exec(UserLoginRequestVO $userLoginRequestVO): BusinessResponse
    {
        $this->logger->info('start exec user login logic.');

        // 登录
        $expire = LocalDateTime::plusHour(CommonConstant::FOUR);
        $userLoginResponse = new UserLoginResponseVO();
        $userLoginResponse->setToken('token'); // 模拟返回token
        $userLoginResponse->setLoginTime(LocalDateTime::now()); // 模拟返回登录时间
        $userLoginResponse->setExpireTime(LocalDateTime::plusHour(CommonConstant::FOUR)); // 模拟返回过期时间
        $userLoginResponse->setExpire(LocalDateTime::timestampForSecond($expire)); // 模拟返回过期时间

        // 查数据库，同时将查询的数据返回给UserPO对象
        $userList = UserModel::query()->get()->map(CallbackEnum::MODEL_MAP_CALLBACK->callback(UserPO::class));
        var_dump($userList);

        return BusinessResult::ok($userLoginResponse);
    }
}
```
