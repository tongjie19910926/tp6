<?php


namespace sdModule\common;


class BinarySystem
{
    const DIGIT_36 = 36;
    const DIGIT_59 = 59;
    const DIGIT_62 = 62;

    private static $aggregate = [
        self::DIGIT_36 => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        self::DIGIT_59 => '023456789ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz',
        self::DIGIT_62 => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
    ];

    /**
     * 10 进制转 其他进制
     * @param int|string $int 10进制数字
     * @param int $digit    要转的进制标识
     * @return string
     */
    public static function binarySystemTo($int, int $digit = self::DIGIT_36)
    {
        $value = '';
        while ($int >= $digit) {
            $value .= self::$aggregate[$digit][intval(fmod(floatval($int), $digit))];
            $int = floor(floatval($int) / $digit);
        }
        return strrev($value . self::$aggregate[$digit][intval($int)]);
    }

    /**
     * 其他进制转10进制
     * @param string|int $data  其他进制代表值
     * @param int $digit        其他进制标识
     * @return float|int
     */
    public static function binarySystemFrom($data, $digit = self::DIGIT_36)
    {
        $value = 0;
        $data = strrev($data);
        for ($i = 0; isset($data[$i]); $i++) {
            $value +=  floatval((int)(strpos(self::$aggregate[$digit], $data[$i])) * pow($digit, $i));
        }
        return $value;
    }
}

