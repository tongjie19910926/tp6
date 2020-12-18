<?php


namespace sdModule\common;

/**
 * 反射类调用处理
 * Class ReflexCall
 * @package app\common\custom
 */
class ReflexCall
{
    /**
     * 利用反射类调用指定的操作函数
     * @param object|string $class  所属类的实例或类名
     * @param string        $method 函数名
     * @param $args         array   参数
     * @return mixed
     * @throws \ReflectionException
     */
    public static function invoke($class, $method, $args = [])
    {
        $class = is_object($class) ? $class : self::getInstance($class);

        if (method_exists($class, $method)) {
            $reflex = new \ReflectionMethod($class, $method);
            $param = [];
            foreach ($reflex->getParameters() as $parameter) {
                if ($parameter->getClass()) {
                    $className = $parameter->getClass()->name;
                    $param[$parameter->getName()] = self::getInstance($className);
                }else{
                    $param[$parameter->getName()] = $args[$parameter->getName()] ?? null;
                }
            }
            return $reflex->invokeArgs($class, $param);
        }else if(method_exists($class, '__call')){
            $reflex = new \ReflectionMethod($class, '__call');
            return $reflex->invokeArgs($class, ['method' => $method, 'vars' => $args]);
        }else{
            throw new \ReflectionException('edit 方法不存在');
        }
    }

    /**
     * 利用反射类获取指定类实例
     * @param string $className 类名
     * @param array  $args  构造参数
     * @return object
     * @throws \ReflectionException
     */
    public static function getInstance($className = '', $args = [])
    {
        $ReflectionClass = new \ReflectionClass($className);
        if (!$ReflectionClass->getConstructor()) return $ReflectionClass->newInstance();
        $param = [];
        foreach ($ReflectionClass->getConstructor()->getParameters() as $parameter) {
            if ($parameter->getClass()) {
                $class = $parameter->getClass()->name;
                $param[$parameter->getName()] = new $class();
            }else{
                if ($parameter->getType()->isBuiltin()) {
                    $typeAll = [
                        'array' => [],
                        'object' => new \stdClass(),
                        'string' => '',
                        'int' => 0
                    ];
                    $typeValue = $typeAll[(string)$parameter->getType()] ?? null;
                }else{
                    $typeValue = null;
                }

                $param[$parameter->getName()] = $args[$parameter->getName()] ?? $typeValue;
            }
        }
        return $ReflectionClass->newInstanceArgs($param);
    }
}

