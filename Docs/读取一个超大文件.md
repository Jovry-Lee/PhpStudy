#### 1. 构造一个超大文件的文件
作为演示，生成一个200M左右的文件。
```
$ dd if=/dev/zero of=test.log count=204800 bs=1024
记录了204800+0 的读入
记录了204800+0 的写出
209715200 bytes (210 MB, 200 MiB) copied, 0.81852 s, 256 MB/s
```

#### 2. 方案
##### 2.1 使用file函数读取到一个数组.
```
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
```
当运行时,执行结果为:
```
Use file function read large file cost: 3.2977271080017
```
并没有向参考文档中示例一样提示超过内存.因为我是使用的cli执行,因此查看了cli模式下的php.ini文件,发现该模式下配置的memory_limit = -1,即内存无限制.
为了试验该情况,将memory_limit改成128M后执行脚本抛出了异常,符合预期.

##### 2.2 使用file_get_content读取文件.
```
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
```
结果:
```
Use file_get_content function read large file cost: 0.11869812011719
```
相比file读取文件,速度稍微快一些.

##### 2.3 使用fgetc函数一个字符一个字符读取.
若在内存有限的机器上读取体积几百倍于内存的文件,方法一,二便无法实现.此时可一点一点读,只要每次读取的数量小于内存限定大小即可.

```
/**
     * 使用fgetc函数一点一点读.
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
```
运行结果:
```
Use fgetc function read large file cost: 81.581026077271
```
从运行结果看,一个字符一个字符的读,在内存限定的情况下,可读出所有的数据,但速度较慢.

##### 2.4 使用fgetcs函数一行一行读取.
为了改进方法三速度慢的情况,尝试一行一行读取.
```
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
```
结果:
```
Use fgets function read large file cost: 0.086040019989014
```
从结果看,一行一行读数据,速度快了很多.

##### 2.5 使用fread函数一次读取一定大小的数据.
假设每次读取分配内存的大小,是否能更快读取数据.
```
/**
     * 使用fread函数每次读系统分配内存大小的数据.
     *
     * @param string $file 文件地址.
     *
     * @return float
     * @throws Exception
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
```
结果:
```
Use fread function read large file cost: 0.067295074462891
```
速度减到0.06s左右了.

#####  2.6 获取文件的最后一行
```
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
```

#####  2.7 获取文件最后N行.
```
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
```


---
#### 参考资料：
[php使用file函数、fseek函数读取大文件效率分析](http://www.manongjc.com/article/1578.html)  
[老旧话题：PHP读取超大文件](https://blog.csdn.net/weixin_34235105/article/details/88688631)