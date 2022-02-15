<?php

/**
 * 自动加载类.
 */
class Autoloader
{
    protected static $sysRoot = array();
    protected static $instance;

    protected function __construct()
    {
        static::$sysRoot = array(
            // 默认的项目根目录.
            __DIR__ . DIRECTORY_SEPARATOR
        );
    }

    /**
     * 获取单例.
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * 添加根目录, 默认使用当前目录为根目录.
     * @param string $path 目录.
     * @return self
     */
    public function addRoot($path)
    {
        static $called;
        if (!$called) {
            // 取消默认的项目根目录.
            unset(static::$sysRoot[0]);
            $called = true;
        }
        static::$sysRoot[] = $path;
        return $this;
    }

    /**
     * 按命令空间自动加载相应的类.
     *
     * @param string $name 命名空间及类名.
     *
     * @return boolean
     */
    public function loadByNamespace($name)
    {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        foreach (static::$sysRoot as $k => $root) {
            $classFile = $root . $classPath . '.php';
            if (is_file($classFile)) {
                require_once $classFile;
                if (class_exists($name, false)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return self
     */
    public function init()
    {
        spl_autoload_register(array($this, 'loadByNamespace'));
        return $this;
    }
}