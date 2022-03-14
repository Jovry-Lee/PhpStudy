<?php

/**
 * HJ4 字符串分隔.
 * @link https://www.nowcoder.com/practice/d9162298cb5a437aad722fccccaae8a7?tpId=37&tqId=21227&rp=1&ru=/ta/huawei&qru=/ta/huawei&difficulty=&judgeStatus=&tags=/question-ranking
 */
namespace Arithmetic\NowCoder;

class HJ4_StrSplit
{
    public function solution1(string $str)
    {
        $result = [];
        $length = strlen($str);
        $rowNum = (int)($length / 8);
        while ($rowNum > 0) {
            $rowData = substr($str, 0, 8);
            $str = substr($str, 8);
            $rowNum--;
            $result[] = $rowData;
        }
        if (empty($str)) {
            return $result;
        }

        $rowData = '00000000';
        for($i = 0; $i < strlen($str); $i++) {
            $rowData[$i] = $str[$i];
        }
        $result[] = $rowData;
        return $result;
    }

    public function solution2(string $str)
    {
        $result = [];
        while ($str) {
            if (($zeroLength = 8 - strlen($str)) > 0) {
                for ($i = 0; $i < $zeroLength; $i++) {
                    $str .= '0';
                }
                $result[] = $str;
                break;
            }

            $result[] = substr($str, 0, 8);
            $str = substr($str, 8);
        }
        return $result;
    }
}