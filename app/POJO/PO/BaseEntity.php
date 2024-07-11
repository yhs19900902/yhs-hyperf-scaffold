<?php

declare(strict_types=1);


namespace App\POJO\PO;

use App\POJO\JsonSerializableTrait;
use JsonSerializable;
use PhpAccessor\Attribute\Data;

#[Data]
class BaseEntity implements JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * @var string id
     */
    private string $id;

    /**
     * @var ?string 创建时间
     */
    private ?string $createdDateTime;

    /**
     * @var ?string 创建人
     */
    private ?string $createdBy;

    /**
     * @var ?string 修改时间
     */
    private ?string $updatedDateTime;

    /**
     * @var ?string 修改人
     */
    private ?string $updatedBy;
}