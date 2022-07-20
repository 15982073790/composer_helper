<?php

namespace Mrstock\Helper;

use Mrstock\Mjc\Facade\Log;
use Mrstock\Mjc\Log\LogLevel;
use Mrstock\Mjc\Facade\Hook;

/**
 *
 * 变量缓存类
 *
 * @author Administrator
 *
 */
class File
{

    // 静态文件内容缓存数组
    protected $expire = 300;

    // 变量文件缓存有效期秒
    protected $fileDir = '/data/file';

    protected $bufferSize = 1024 * 1024 * 100;

    protected $config;

    protected $request;

    protected $debug;


    /**
     * 写入缓存
     *
     * @param unknown $key
     * @param unknown $data
     * @param number $exp
     *            <=0 永不过期 >0 过期时间为期值
     * @return boolean
     */
    public function set($key, $data, $exp = 300)
    {
        $startTime = microtime(true);
        $this->getConfig();

        $key = $this->adaptedKey($key);

        $value = $this->assembleData($data, $exp);

        $fileName = $this->setFilePath($key);


        // 判断目录是否存在(自定义目录存储)
        if (!is_dir(dirname($fileName))) {
            //$mkdir_rs=@mkdir(iconv("UTF-8", "GBK", dirname($fileName)), 0777, true);
            $mkdir_rs = @mkdir(dirname($fileName), 0755, true);
            if (empty($mkdir_rs)) {
                Log::record('mkdir fail ' . dirname($fileName), LogLevel::FILERECORD);
            }
        }

        $tryNum = 1; // 重试次数
        $re = $this->writeFileLock($fileName, $key, $value);
        while (!$re && $tryNum < 3) {
            usleep(10);
            $re = $this->writeFileLock($fileName, $key, $value);
            $tryNum++;
        }
        Hook::listen('debug_record', ['type' => 'File', 'link' => $fileName, 'command' => "set exp:" . $exp, 'starttime' => $startTime, 'result' => $re, 'state' => true]);

        return $re;
    }

    /**
     * 删除缓存
     *
     * @param unknown $key
     * @return number|boolean
     */
    public function delete($key)
    {
        $this->getConfig();

        $key = $this->adaptedKey($key);

        $fileName = $this->setFilePath($key);

        if (is_file($fileName)) @unlink($fileName);

        return true;
    }

    /**
     * 获取缓存
     *
     * @param unknown $key
     * @return number|boolean
     */
    public function get($key)
    {
        $startTime = microtime(true);
        $this->getConfig();

        $key = $this->adaptedKey($key);

        $fileName = $this->setFilePath($key);

        $debug_result = true;

        if (is_file($fileName)) {
            $nowTime = time();
            $creatTime = filemtime($fileName);
            $value = $this->readFileLock($fileName);
            $data = $this->parseData($value);
            if ($data) {
                $exp = intval($data['exp']);
                $result = $data['data'];
                if ($exp <= 0 || ($creatTime + $exp) > $nowTime) {
                    Hook::listen('debug_record', ['type' => 'File', 'link' => $fileName, 'command' => "get", 'starttime' => $startTime, 'result' => $debug_result, 'state' => true]);
                    return $result;
                } else {
                    $debug_result = 'exp_file';
                }
            } else {
                $debug_result = 'empty_file';
            }
            @unlink($fileName);
        } else {
            $debug_result = 'not_file';
        }
        Hook::listen('debug_record', ['type' => 'File', 'link' => $fileName, 'command' => "get", 'starttime' => $startTime, 'result' => $debug_result, 'state' => false]);

        return false;
    }

    /**
     * 组装数据
     *
     * @param unknown $value
     * @param unknown $exp
     * @return string
     */
    protected function assembleData($data, $exp)
    {
        $cacheData = [];
        $cacheData['exp'] = intval($exp);
        $cacheData['data'] = $data;
        return serialize($cacheData);
    }

    /**
     * 解析数据
     *
     * @param unknown $value
     * @return NULL|unknown[]|mixed[]
     */
    protected function parseData($value)
    {
        $cacheData = unserialize($value);
        if (empty($cacheData) || !isset($cacheData['exp']) || !isset($cacheData['data'])) {
            return false;
        }
        return $cacheData;
    }

    // 获取文件名-用于判断文件存不存在
    protected function getFileName($key, $dir = null)
    {
        $this->config();
        if (!is_null($dir)) {
            $this->fileDir = $dir;
        }

        $key = $this->adaptedKey($key);
        $fileName = $this->setFilePath($key);
        if (is_file($fileName)) {
            return true;
        } else {
            Log::record('file_is_not ' . $fileName, LogLevel::FILERECORD);
            return false;
        }
    }

    protected function getConfig()
    {
        $fileConfig = Config::get("filesystem");

        $this->config = $fileConfig;

        if (isset($fileConfig['dir'])) {
            $this->fileDir = $fileConfig['dir'];
        }
    }

    protected function adaptedKey($key)
    {
        $tmpKey = KeyValue::dynamicPrefix($this->config);
        $key = $tmpKey . $key;
        $key = str_replace('-', '_', $key);
        $key = str_replace(':', '_', $key);
        $key = str_replace('.', '_', $key);
        return $key;
    }

    protected function setFilePath($key)
    {
        $filePath = strtolower($key);
        $filePath = $this->distributionFile($filePath);

        $filePath = $this->fileDir . '/' . $filePath;
        $filePath = $filePath . '.php';

        return $filePath;
    }

    protected function distributionFile($key)
    {
        $keyDir = '';
        if (strlen($key) < 2) {
            return $key;
        }

        $keyDir = str_replace('_', '/', $key);

        return $keyDir;
    }

    public function readFileLock($fileName)
    {
        $result = false;
        if (isset($_GET["apidebug"]) && $_GET["apidebug"]) {
            header('apidebug-file' . uniqid() . ':' . $fileName);
        }
        $fo = @fopen($fileName, 'r');
        if (flock($fo, LOCK_SH)) {
            $result = fread($fo, $this->bufferSize);
            flock($fo, LOCK_UN);
            fclose($fo);
        }
        return $result;
    }

    protected function writeFileLock($fileName, $key, $data)
    {
        $result = false;
        $content = $data;
        $fo = fopen($fileName, 'cb');
        if (empty($fo)) {
            Log::record('writeFileLock fopen_fail ' . print_r(func_get_args(), true), LogLevel::FILERECORD);
        }
        if ($fo) {
            if (flock($fo, LOCK_EX)) {
                rewind($fo);
                fwrite($fo, $content);
                fflush($fo);
                ftruncate($fo, ftell($fo));
                flock($fo, LOCK_UN);
                fclose($fo);
                $result = true;
            }
            if (empty($result)) {
                Log::record('writeFileLock flock_fail ' . print_r(func_get_args(), true), LogLevel::FILERECORD);
            }
        }

        return $result;
    }
}