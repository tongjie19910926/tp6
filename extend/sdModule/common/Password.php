<?php


namespace sdModule\common;


class Password
{
    const PREFIX = 'sd-cl-love-each-other-';

    /**
     * 加密
     * @param        $password
     * @param string $prefix
     * @return bool|string
     */
    public static function encryption($password, $prefix = self::PREFIX)
    {
        $strlen = strlen($password);
        if ($strlen <= 6) {
            return password_hash($prefix . $password, PASSWORD_DEFAULT);
        } elseif ($strlen > 44) {
            return false;
        }

        $s1 = strrev(substr($password, 0, 3));
        $s2 = strrev(substr($password, -3));

        $password = $s2 . $prefix . $password . $s1;
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 验证
     * @param        $password
     * @param        $verify_string
     * @param string $prefix
     * @return bool
     */
    public static function verify($password, $verify_string, $prefix = self::PREFIX)
    {
        $strlen = strlen($password);
        if ($strlen <= 6) {
            return password_verify($prefix . $password, $verify_string);
        } elseif ($strlen > 44) {
            return false;
        }

        $s1 = strrev(substr($password, 0, 3));
        $s2 = strrev(substr($password, -3));

        $password = $s2 . $prefix . $password . $s1;
        return password_verify($password, $verify_string);
    }

}


