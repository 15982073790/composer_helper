<?php

namespace Mrstock\Helper;

use OSS\OssClient;
use OSS\Core\OssException;

class Oss
{
    protected $errMgs;

    /**
     * 上传文件
     *
     * @param string $remote
     *            远程存放地址
     * @param string $local
     *            本地存放地址
     */
    public function put($remote, $local)
    {
        $config = Config::get("ftptooss");
        $host = $config['default'];
        // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
        $accessKeyId = $host['accessKeyId'];
        $accessKeySecret = $host['accessKeySecret'];
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = $host['endpoint'];
        // 设置存储空间名称。
        $bucket = $host['bucket'];
        // 设置文件名称。
        $object = $remote;
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        $filePath = $local;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            return $ossClient->uploadFile($bucket, $object, $filePath);
        } catch (OssException $e) {
            $this->errMgs = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取错误信息
     */
    public function getError()
    {
        return $this->errMgs;
    }

}
