<?php

declare(strict_types=1);


namespace App\POJO\VO;

use App\POJO\JsonSerializableTrait;
use Hyperf\ApiDocs\Annotation\ApiModel;
use Hyperf\DTO\Annotation\Validation\In;
use Hyperf\DTO\Annotation\Validation\Min;
use Hyperf\DTO\Annotation\Validation\Numeric;
use Hyperf\DTO\Annotation\Validation\Str;
use JsonSerializable;
use PhpAccessor\Attribute\Data;

#[Data]
#[ApiModel(value: '分页请求基础实体')]
class PageRequestVO implements JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * @var int 页码
     */
    #[Min(value: 0, message: '页码最小值 1 ！')]
    #[Numeric]
    private int $pageNumber = 1;

    /**
     * @var int 每页的记录数
     */
    #[Min(value: 1, message: '每页的记录数最小值 1 ！')]
    #[Numeric]
    private int $pageSize = 10;

    /**
     * @var String|null 排序的字段
     */
    #[Str]
    private ?string $orderByField;

    /**
     * @var string|null 排序类型
     */
    #[Str]
    #[In(value: ['ASC', 'DESC', 'asc', 'desc'], message: '排序类型错误 ！')]
    private ?string $orderByType;
}