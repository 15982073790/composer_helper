<?php

namespace Mrstock\Helper;

class Upload
{

    /**
     * 允许上传的文件类型
     */
    protected $allowType = ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf', 'tbi', 'mp3', 'wma', 'WAV', 'CD', 'OGG', 'ASF', 'MP3PRO', 'RM', 'REAL', 'APE', 'MODULE', 'MIDI', 'VQF', 'm4a', 'xls', 'amr'
    ];

    /**
     * 允许的最大文件大小，单位为KB
     */
    protected $maxSize = '5120';

    /**
     * 上传文件名
     */
    protected $fileName;

    /**
     * 上传文件后缀名
     */
    protected $ext;

    /**
     * 上传文件新后缀名
     */
    protected $newExt;

    /**
     * 默认文件存放文件夹
     */
    protected $defaultDir = "upload";

    /**
     * 临时文件存储目录
     * @var string
     */
    protected $tmpDir = "/tmp";

    /**
     * 调用时指定目录+文件名
     * @var unknown
     */
    protected $filePath;


    /**
     * 使用 $_FILES 控件上传
     * @param unknown $field $_FILES 控件所有
     * @param string $filePath 指定目录+文件名 (上次头像使用)
     * @return boolean|boolean|mixed
     */
    public function file($field, $filePath = '')
    {
        $this->filePath = '';
        if (!isset($_FILES[$field])) {
            $this->setError("找不到临时文件，请确认临时文件夹是否存在可写");
            return false;
        }

        $this->filePath = $filePath;

        // 上传文件
        $this->uploadFile = $_FILES[$field];

        // 验证是否是合法的上传文件
        if (!is_uploaded_file($this->uploadFile['tmp_name'])) {
            $this->setError("上传文件不合法");
            return false;
        }

        return $this->up();
    }

    /**
     * base64 上传图片 (文件后缀名为.jpg)
     * @param unknown $data 图片内容base64 编码
     * @param string $filePath 指定目录+文件名 (上次头像使用)
     * @return boolean|boolean|mixed
     */
    public function base64($data, $filePath = '')
    {
        $this->filePath = '';
        if (!$data) {
            $this->setError("base64 为空");
            return false;
        }

        $this->filePath = $filePath;

        $tmpFileName = md5(microtime(true)) . '.' . 'jpg';
        $tmpFilePath = $this->tmpDir . '/' . $tmpFileName;
        $data = str_replace(' ', '+', $data);
        $data = str_replace('*', '/', $data);
        $data = str_replace('-', '=', $data);
        $data = base64_decode($data);

        if (file_put_contents($tmpFilePath, $data)) {
            $size = filesize($tmpFilePath);

            $tmpFile = [];
            $tmpFile['name'] = $tmpFileName;
            $tmpFile['type'] = 'image/jpeg';
            $tmpFile['tmp_name'] = $tmpFilePath;
            $tmpFile['error'] = '0';
            $tmpFile['size'] = $size;
            $this->uploadFile = $tmpFile;

            $result = $this->up();
            unlink($tmpFilePath);
            return $result;
        }
        $this->setError("写入临时文件失败");
        return false;
    }

    protected function up()
    {
        if ($this->checkFile()) {
            $ftp = new Ftp();
            if ($ftp->start()) {
                $result = $ftp->put($this->fileName, $this->uploadFile['tmp_name']);
                if ($result) {
                    return $result;
                }
            }
            $error = $ftp->getError();
            $this->setError($error);
        }
        return false;
    }

    protected function checkFile()
    {
        if ($this->uploadFile['tmp_name'] == "") {
            $this->setError("找不到临时文件，请确认临时文件夹是否存在可写");
            return false;
        }

        // 验证文件大小
        if ($this->uploadFile['size'] == 0) {
            $this->setError("临时文件大小为空");
            return false;
        }

        if ($this->uploadFile['size'] > $this->maxSize * 1024) {
            $this->setError("文件大小超过" . $this->maxSize . "KB");
            return false;
        }

        // 文件后缀名
        $tmpExt = explode(".", $this->uploadFile['name']);
        $tmpExt = $tmpExt[count($tmpExt) - 1];
        $this->ext = strtolower($tmpExt);

        // 验证文件格式是否为系统允许
        if (!in_array($this->ext, $this->allowType)) {
            $this->setError("不是允许的文件类型");
            return false;
        }

        $this->setFileName();

        return true;
    }

    protected function setError($error)
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置文件名称
     *
     * 生成(从2000-01-01 00:00:00 到现在的秒数+微秒+四位随机)
     */
    protected function setFileName()
    {
        if ($this->filePath) {
            $this->fileName = $this->filePath;
        } else {
            $tmpName = sprintf('%010d', time() - 946656000) . sprintf('%03d', microtime() * 1000) . sprintf('%04d', mt_rand(0, 9999));
            $this->fileName = $this->defaultDir . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . $tmpName . '.' . ($this->newExt == '' ? $this->ext : $this->newExt);
        }
    }
}

