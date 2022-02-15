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