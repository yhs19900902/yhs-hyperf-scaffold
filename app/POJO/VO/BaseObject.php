<?php

declare(strict_types=1);

namespace App\POJO\VO;

use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Hyperf\DTO\Annotation\Dto;
use Hyperf\DTO\Type\PhpType;

#[Dto]
class BaseObject
{
    #[ApiModelProperty(value: "响应编码", example: 0, required: true, simpleType: PhpType::INT)]
    public int $code;

    #[ApiModelProperty(value: "响应消息", example: "success", required: true, simpleType: PhpType::STRING)]
    public string $message;

    public function __construct(int $code = 0, string $message = '')
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * 对象转数组
     *
     * @param mixed $data
     * @return array
     */
    public function turnArray(mixed $data): array
    {
        // 对象转数组
        $array = json_decode(is_object($data) || is_array($data)
            ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : $data, true);

        // 过滤空参数
        $filterFunc = function ($value) {
            if ($value === null) {
                return false; // 过滤空字符串和 null 值
            }
            if (is_array($value) && empty($value)) {
                return false; // 过滤空数组
            }
            return true; // 保留其他值
        };

        return json_decode(json_encode(array_filter($array, $filterFunc), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE), true);
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}