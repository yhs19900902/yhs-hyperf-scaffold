<?php

declare(strict_types=1);

namespace App\POJO;

use App\POJO\VO\BaseObject;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\ApiDocs\Annotation\ApiVariable;

#[ApiModel(value: "响应参数")]
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
     * @return array
     */
    public function ok(mixed $data): array
    {
        $this->code = $this->RESPONSE_OK;
        $this->message = 'success';
        $this->data = $data;
        return $this->toArray($this);
    }

    /**
     * 响应失败返回
     *
     * @param int $code 响应编码
     * @param string $message 响应信息
     * @param mixed|null $data 响应数据
     * @return array
     */
    public function fail(int $code = 50000, string $message = 'service exception', mixed $data = null): array
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        return $this->toArray($this);
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
}