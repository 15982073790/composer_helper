<?php

namespace Mrstock\Helper;

use Mrstock\Mjc\Facade\Log;
use Mrstock\Mjc\Log\LogLevel;

/**
 *
 * 变量缓存类
 *
 * @author Administrator
 *
 */
class FileCache
{

    protected static $files;

    // 静态文件内容缓存数组
    protected static $expire = 300;

    // 变量文件缓存有效期秒
    protected static $fileDir = '/data/cache';

    protected static $bufferSize = 1024 * 1024 * 100;

    /**
     *
     * 变量写入文件缓存
     *
     * for ($i=0;$i<1000;$i++)
     * {
     * $list[$i] = $i.'试文件锁测试文件锁测试文件锁';
     * }
     * $list[count($list)-1] = microtime(true);
     * var_dump(Gfile::set('testfile',$list));
     *
     * @param unknown_type $key
     * @param unknown_type $data
     *            可以为数组或字符串 int 等等
     */
    public static function set($key, $data, $dir = null)
    {
        self::config();
        if (!is_null($dir)) {
            self::$fileDir = $dir;
        }

        $key = self::adaptedKey($key);

        $value = serialize($data);

        $fileName = self::setFilePath($key);

        // 判断目录是否存在(自定义目录存储)
        if (!is_dir(dirname($fileName))) {
            @mkdir(iconv("UTF-8", "GBK", dirname($fileName)), 0777, true);
        }

        $tryNum = 1; // 重试次数
        $re = self::writeFileLock($fileName, $key, $value);
        while (!$re && $tryNum < 3) {
            usleep(10);
            $re = self::writeFileLock($fileName, $key, $value);
            $tryNum++;
        }
        return $re;
    }

    //获取文件名-用于判断文件存不存在
    protected static function getFileName($key, $dir = null)
    {
        self::config();
        if (!is_null($dir)) {
            self::$fileDir = $dir;
        }

        $key = self::adaptedKey($key);
        $fileName = self::setFilePath($key);
        if (is_file($fileName)) {
            return true;
        } else {
            Log::record('file_is_not ' . $fileName, LogLevel::FILERECORD);
            return false;
        }
    }

    /**
     * 获取缓存变量内容
     * @param string $key
     * @param int $exp (小于0 永不过期 0实时获取 大于0 有效时间 单位秒)
     * @param string $dir
     * @param string $isDaemon
     * @return unknown|boolean
     */
    public static function get($key, $exp = 300, $dir = null, $isDaemon = FALSE)
    {
        self::config();
        if (!is_null($dir)) {
            self::$fileDir = $dir;
        }

        $key = self::adaptedKey($key);

        // 如果-1 则为过期时间为10年 永不过期
        if ($exp <= -1) {
            $exp = 315360000;
        }
        $exp = intval($exp);
        if ($exp > 0) {
            $exp += rand(-10, 100) / 100;
        }

        $nowTime = time();
        // 连接创建时间
        $createTime = 0;
        if (isset(self::$files['createtime'][$key])) {
            $createTime = intval(self::$files['createtime'][$key]);
        }

        // 静态变量过期时间
        if (($nowTime - $createTime) >= $exp) {
            if (isset(self::$files[$key]) && $exp > 0) {
                unset(self::$files[$key]);
            }
        }

        if (!isset(self::$files[$key]) || $exp === 0) {
            $fileName = self::setFilePath($key);
            if (is_file($fileName)) {

                $mTime = filemtime($fileName);

                if (($mTime + $exp) > $nowTime || $exp === 0) {
                    $content = self::readFileLock($fileName);
                    $tryNum = 1;
                    while (empty($content) && $tryNum < 3) {
                        $content = self::readFileLock($fileName);
                        $tryNum++;
                    }
                    if ($content === false) {
                        Log::write('file_read_false ' . $fileName, LogLevel::FILERECORD);
                    }
                    if (empty($content)) {
                        Log::write('file_read_empty ' . $fileName, LogLevel::FILERECORD);
                    } else {
                        $unSerializeContent = unserialize($content);
                        if (empty($unSerializeContent)) {
                            Log::write('file_unserialize_empty ' . $fileName . PHP_EOL . $content, LogLevel::FILERECORD);
                        } else {
                            self::$files[$key] = $unSerializeContent;

                            // 创建时间
                            self::$files['createtime'][$key] = $nowTime;
                        }
                    }
                } else {
                    @unlink($fileName);
                }
            } else {
                Log::write('file_is_not ' . $fileName, LogLevel::FILERECORD);
            }
        }

        if (isset(self::$files[$key])) {
            return self::$files[$key];
        } else {
            return false;
        }
    }

    protected static function config()
    {
        $fileConfig = Config::get("filesystem");
        if (isset($fileConfig['dir'])) {
            self::$fileDir = $fileConfig['dir'];
        }
    }

    protected static function adaptedKey($key)
    {
        $key = str_replace('-', '_', $key);
        $key = str_replace(':', '_', $key);
        $key = str_replace('.', '_', $key);
        return $key;
    }

    protected static function setFilePath($key)
    {
        $filePath = strtolower($key);
        $filePath = self::distributionFile($filePath);

        $filePath = self::$fileDir . '/' . $filePath;
        $filePath = $filePath . '/' . $key . '.php';

        return $filePath;
    }

    protected static function distributionFile($key)
    {
        $keyDir = '';
        if (strlen($key) < 2) {
            return $key;
        }

        $keyDir = str_replace('_', '/', $key);

        return $keyDir;
    }

    protected static function readFileLock($fileName)
    {
        $result = false;
        if (isset($_GET["apidebug"]) && $_GET["apidebug"]) {
            header('apidebug-file' . uniqid() . ':' . $fileName);
        }
        $fo = @fopen($fileName, 'r');
        if (flock($fo, LOCK_SH)) {
            $result = fread($fo, self::$bufferSize);
            flock($fo, LOCK_UN);
            fclose($fo);
        }
        return $result;
    }

    protected static function writeFileLock($fileName, $key, $data)
    {
        $content = $data;
        $fo = fopen($fileName, 'cb');
        if ($fo) {
            if (flock($fo, LOCK_EX)) {
                rewind($fo);
                fwrite($fo, $content);
                fflush($fo);
                ftruncate($fo, ftell($fo));
                flock($fo, LOCK_UN);
                fclose($fo);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}