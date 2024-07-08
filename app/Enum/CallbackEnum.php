<?php

namespace App\Enum;

use App\Constants\CommonConstant;
use ReflectionClass;

enum CallbackEnum
{
    /**
     * 数据库查询结果反射到对象  查询数据时使用map()操作,案例:Model::query()->get()->map(CallbackEnum::MODEL_NAP_CALLBACK->callback(OrderPO::class));
     */
    case MODEL_NAP_CALLBACK;

    public function callback(?string $className = null): callable
    {
        return match ($this) {
            self::MODEL_NAP_CALLBACK => function ($value) use ($className) {
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
