<?php

namespace Arithmetic\Hash;

use Exception;

class StringHash
{
    /**
     * 根据进制进行转换。大小写字母一共52个，做52进制。
     *
     * @param string $str 字符串（仅包含大小写字母）
     *
     * @return int
     * @throws Exception
     */
    public function charToHash(string $str) :int
    {
        $id = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] >= 'A' && $str[$i] <= 'Z') {
                $id = 52 * $id + (ord($str[$i]) - ord('A'));
                continue;
            }

            if ($str[$i] >= 'a' && $str[$i] <= 'z') {
                $id = 52 * $id + (ord($str[$i]) - ord('a') + 26);
                continue;
            }

            throw new Exception('Invalid char!');
        }

        return $id;
    }
}