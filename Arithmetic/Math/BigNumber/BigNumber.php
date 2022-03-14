<?php

namespace Arithmetic\Math\BigNumber;

use Exception;

class BigNumber
{
    private $d;

    private $len;

    /**
     * @return array
     */
    public function getD(): array
    {
        return $this->d;
    }

    /**
     * @return int
     */
    public function getLen(): int
    {
        return $this->len;
    }

    public function __construct($input)
    {
        if (is_array($input)) {
            $this->d = $input;
            $this->len = count($input);
            return $this;
        }

        if (is_string($input)) {
            $d = [];
            $this->len = strlen($input);
            for ($i = $this->len - 1; $i >= 0; $i--) {
                $d[] = $input[$i];
            }
            $this->d = $d;
            return $this;
        }

        throw new Exception('Invalid params!');
    }

    /**
     * 转换为字符串。
     */
    public function toString() :string
    {
        $result = '';
        for ($i = 0; $i < $this->getLen(); $i++) {
            $result = $this->getD()[$i] . $result;
        }

        return $result;
    }
}