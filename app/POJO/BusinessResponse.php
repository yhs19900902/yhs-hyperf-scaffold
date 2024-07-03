<?php

declare(strict_types=1);

namespace App\POJO;

use App\POJO\VO\BaseObject;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\ApiDocs\Annotation\ApiVariable;
use PhpAccessor\Attribute\Data;

#[ApiModel(value: "响应参数")]
#[Data]
class BusinessResponse extends BaseObject
{
    #[ApiModelProperty(value: "响应数据")]
    #[ApiVariable]
    public mixed $data;
    /**
     * @var int 默认响应编码
     */
    private int $RESPONSE_OK = 0;

    public function __construct(mixed $data = null)
    {
        parent::__construct();

        $this->data = $data;
    }

    /**
     * 判断是否响应成功
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->code === $this->RESPONSE_OK;
    }

    /**
     * 响应返回成功
     *
     * @param mixed $data 响应数据
     * @return BusinessResponse
     */
    public function ok(mixed $data): BusinessResponse
    {
        $this->code = $this->RESPONSE_OK;
        $this->message = 'success';
        $this->data = $data;
        return $this;
    }

    /**
     * 响应失败返回
     *
     * @param int $code 响应编码
     * @param string $message 响应信息
     * @param mixed|null $data 响应数据
     * @return BusinessResponse
     */
    public function fail(int $code = 50000, string $message = 'service exception', mixed $data = null): BusinessResponse
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        return $this;
    }

    /**
     * 转数组
     *
     * @return array
     */
    public function toArray(): array
    {
        return parent::turnArray($this);
    }

    public function __toString(): string
    {
        return json_encode(parent::turnArray($this));
    }
}