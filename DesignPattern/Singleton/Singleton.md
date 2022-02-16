#### 前言
PHP面向对象编程中常见的设计模式-单例模式.

#### 简介
单例模式是创建对象模式中的一种，它确保某一个类只有一个实例，自行实例化并向整个系统全局地提供这个实例（类似与 global）。当有任务需要该实例时，它不会创建实例副本，而是会返回单例类内部存储实例的一个引用。

#### 应用场景
- 数据库连接
单例模式主要在于数据库连接中。一个项目中会存在大量的数据库操作，比如过数据库句柄来连接数据库这一行为。使用单例模式可以避免大量的 new 操作，因为每一次 new 操作都会消耗内存资源和系统资源，还可以减少数据库连接这样就不容易出现 too many connections 情况；

- 配置类
项目中有一个类来全局控制某些配置信息。如果这个类能被实例化多次，可能在运行中对配置进行了修改，我们无法知道是在哪个实例中进行了修改。使用单例模式，所有对于配置文件的操作都是基于这个实例的；

#### 单例模式的基本实现.
* 注意项:
    1. 需要一个静态的成员变量保存类的唯一实例.
    2. 构造函数和克隆函数必须声明为private,防止外部程序new 类从而失去单例模式的意义.
    3. 必须提供一个访问这个实例的公共静态方法(通常命名为getInstance), 从而返回唯一实例的一个引用.

代码实现:
```
<?php
class singleton {
    // 私有静态成员变量
    private static $_instance = null;

    // 私有构造函数，只在第一次实例化时执行
    private function __construct() {}

    // 私有克隆函数
    private function __clone() {}

    // 获取实例函数
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
```

通过以下代码验证是否为同一实例:
```
$a = singleton::getInstance();
$b = singleton::getInstance();
var_dump($a === $b);
```
输出结果:
```
cdyf@jumei:~/tutorial/DesignMode$ php Singleton.php
bool(true)
```
可见$a和$b是同一实例.

#### 应用单例模式的数据库连接类
单例模式的一个常见的应用是在数据库连接中.

* 未使用单例模式
```
<?php
......
// 初始化一个数据库句柄
$db = mysql_connect(...);

// 添加用户信息
$db->addUserInfo(...);

......

// 在函数中访问数据库，查找用户信息
function getUserInfo()
{
    $db = mysql_connect(...); // 再次new 数据库类，和数据库建立连接
    $db = query(....); // 根据查询语句访问数据库
}
?>
```

多次连接数据库,浪费资源.

* 使用单例模式
```
<?php

class DB {
    // 增加一个成员变量存放数据库连接句柄
    private $_db;
    private static $_instance;

    // 在构造函数中执行数据库连接，给$_db赋值
    private function __construct(...) {
        $this->_db = mysql_connect(...);
    }

    private function __clone() {};  //覆盖__clone()方法，禁止克隆

    public static function getInstance()  {
        if(! (self::$_instance instanceof self) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function doSometing($sql) {
          $res = mysql_query($sql,$this->_db);
        return $res;
    }
}

$db = DB::getInstance();

$db->doSomething("SELECT * FROM user");
?>
```
通过单例模式保证了只有一次连接数据库的操作.

#### 实际项目中的例子

```
<?php

class db
{
    public $connect;
    public static $lastSql;
    private static $instance;

    private function __construct()
    {
        try {
            $this->connect = new PDO('mysql:host=127.0.0.1;dbname=seven', 'root', '123456');
            $this->connect->prepare('set names utf8')->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    private function __clone(){}

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 查询数据库.
    public function query($table, $conds = array(), $fileds = array())
    {
        $where = '';
        if (!empty($conds)) {
            foreach ($conds as $k => $v) {
                $where .= $k . "='" . $v . "' and 1=1";
            }
            $where = 'where ' . $where;
        }

        $filedStr = '';
        if (!empty($fileds)) {
            foreach ($fileds as $k => $v) {
                $filedStr .= $v . ',';
            }
            $filedStr = rtrim($filedStr, ',');
        } else {
            $filedStr = '*';
        }
        self::$lastSql = "select {$filedStr} from {$table} {$where}";
        $stmt = $this->connect->prepare(self::$lastSql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 添加一条记录.
    public function insert($table, $data)
    {
        $fileds = $values = '';
        foreach ($data as $k => $v) {
            $fileds .= $k . ',';
            $values .= "'$v',";
        }
        $values = rtrim($values, ',');
        $fileds = rtrim($fileds, ',');

        self::$lastSql = "INSERT INTO {$table} ({$fileds}) VALUES ({$values})";
        $stmt = $this->connect->prepare(self::$lastSql);
        return $stmt->execute();
    }

    // 修改一条记录.
    public function modify($table, $data, $conds = array())
    {
        $where='';
        if(!empty($conds)){

            foreach($conds as $k=>$v){
                $where .= $k."='".$v."' and ";
            }
            $where = 'where '.$where .'1=1';
        }
        $updatastr = '';
        if(!empty($data)){
            foreach($data as $k=>$v){
                $updatastr .= $k."='".$v."',";
            }
            $updatastr = 'set '.rtrim($updatastr,',');
        }
        self::$lastSql = "update {$table} {$updatastr} {$where}";
        return $this->connect->prepare(self::$lastSql)->execute();
    }

    // 删除一条记录.
    public function delete($table, $conds)
    {
        $where='';
        if(!empty($conds)){
            foreach($conds as $k=>$v){
                $where .= $k."='".$v."' and ";
            }
            $where='where '.$where .'1=1';
        }
        self::$lastSql = "delete from {$table} {$where}";
        return $this->connect->prepare(self::$lastSql)->execute();
    }

    // 获取最后一条SQL.
    public function getLastSql()
    {
        return self::$lastSql;
    }
}

$db = db::getInstance();


// 增.
$result = $db->insert('category', array('name' => '黑土豆', 'parent_id' => 7));
var_dump($result);
// 改.
$result = $db->modify('category', array('name' => '黄皮土豆'), array('name' => '黑土豆'));
var_dump($result);
// 删.
$result= $db->delete('category', array('name' => '黑土豆'));
var_dump($result);
// 查.
$result = $db->query('category', array('parent_id' => 0), array('name'));
// 获取最后一次查询的sql.
var_dump($db->getLastSql());
```


##### 问：
还有另外一种实现单例模式的方法，这种方法有什么不好的地方？
```
class Mysql{
    // 通过static来保证实例的唯一性
    public static $_instance;
}
```
答：虽然通过 static 关键字可以将属性设置为静态从而保证了实例的唯一性，但是 public 关键字决定了这个实例的权限是公共的。这样的后果是别人可以对该实例进行修改甚至删除（unset），是不安全的。