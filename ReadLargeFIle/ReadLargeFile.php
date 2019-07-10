<?php

/**
 * PHP读取一个超大的文件.
 */
class ReadLargeFile
{
    /**
     * 使用file函数读取文件.
     *
     * @param string $file 文件地址.
     *
     * @return float
     */
    public function useFileFunction($file)
    {
        $startTime = microtime(true);
        file($file);
        $endTime = microtime(true);

        return $endTime - $startTime;
    }

    /**
     * 使用file_get_content函数读取文件.
     *
     * @param string $file 文件地址.
     *
     * @return float
     */
    public function useFileGetContentFunction($file)
    {
        $startTime = microtime(true);
        file_get_contents($file);
        $endTime = microtime(true);

        return $endTime - $startTime;
    }

    /**
     * 使用fgetc函数一个字符一个字符地读.
     *
     * @param string $file 文件地址.
     *
     * @return float
     */
    public function useFgetcFunction($file)
    {
        $startTime = microtime(true);
        $fp = fopen($file, 'r');
        while (false !== ($ch = fgetc($fp))) {
            // 打开注释后屏显字符会严重拖慢程序速度！也就是说程序运行速度可能远远超出屏幕显示速度
            // echo $ch . '\n';
        }
        fclose($fp);
        $endTime = microtime(true);

        return $endTime - $startTime;
    }

    /**
     * 使用fgetc函数一行一行地读.
     *
     * @param string $file 文件地址.
     *
     * @return float
     * @throws Exception
     */
    public function useFgetsFunction($file)
    {
        $startTime = microtime(true);
        $fp = fopen($file, 'r');
        while (false !== ($buffer = fgets($fp, 4096))) {
            // 打开注释后屏显字符会严重拖慢程序速度！也就是说程序运行速度可能远远超出屏幕显示速度
            // echo $ch . '\n';
        }
        if (!feof($fp)) {
            throw new Exception('not finished');
        }
        fclose($fp);
        $endTime = microtime(true);

        return $endTime - $startTime;
    }

    /**
     * 使用fread函数每次读系统分配内存大小的数据.
     *
     * @param string $file 文件地址.
     *
     * @return float
     */
    public function useFreadFunction($file)
    {
        $startTime = microtime(true);
        $fp = fopen($file, 'r');
        while (!feof($fp)) {
            // 如果你要使用echo，那么，你会很惨烈...
            fread($fp, 10240);
        }
        fclose($fp);
        $endTime = microtime(true);

        return $endTime - $startTime;
    }

    /**
     * 获取文件最后一行.
     *
     * @param string  $file 文件地址.
     *
     * @return array
     */
    public function getLastLine($file)
    {
        $fp = fopen($file, 'r');
        $pos = -1;
        $t = '';
        // 找到最后一行的开始指针.
        while ($t != "\n") {
            fseek($fp, $pos, SEEK_END);
            $t = fgetc($fp);
            $pos--;
        }

        $result = fgets($fp);
        fclose($fp);
        return $result;
    }

    /**
     * 获取文件最后指定行数.
     *
     * @param string  $file 文件地址.
     * @param integer $num 行数.
     *
     * @return array
     */
    public function getLastNumRowsInfo($file, $num)
    {
        $fp = fopen($file, 'r');
        $pos = -1;
        $ch = '';
        $result = array();
        while ($num > 0) {
            while ($ch != "\n") {
                fseek($fp, $pos, SEEK_END);
                $ch = fgetc($fp);
                $pos--;
            }
            $result[] = fgets($fp);
            $num--;
            $ch = '';
        }
        return $result;
    }
}

$file = './test.log';

$readLargeFile = new ReadLargeFile();
$fileCost = $readLargeFile->useFileFunction($file);
echo "Use file function read large file cost: $fileCost\n\n";

$fileGetContentCost = $readLargeFile->useFileGetContentFunction($file);
echo "Use file_get_content function read large file cost: $fileGetContentCost\n\n";

$fgetcCost = $readLargeFile->useFgetcFunction($file);
echo "Use fgetc function read large file cost: $fgetcCost\n\n";

$fgetsCost = $readLargeFile->useFgetsFunction($file);
echo "Use fgets function read large file cost: $fgetsCost\n\n";

$freadCost = $readLargeFile->useFreadFunction($file);
echo "Use fread function read large file cost: $freadCost\n\n";

var_dump($readLargeFile->getLastNumRowsInfo('./test1.log', 5));