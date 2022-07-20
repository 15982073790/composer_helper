<?php

namespace Mrstock\Helper;

class Ftp
{

    // FTP 连接资源
    protected $link;

    // FTP连接时间
    public $linkTime;

    // 错误代码
    protected $errCode = 0;

    // 传送模式{文本模式:FTP_ASCII, 二进制模式:FTP_BINARY}
    public $mode = FTP_BINARY;

    protected $config;

    protected $data;

    public function __construct($host = 'default')
    {
        $this->config();

        $this->data = $this->config[$host];
    }

    protected function config()
    {
        $this->config = Config::get("ftp");
    }

    public function start()
    {
        $data = $this->data;

        if (empty($data['port']))
            $data['port'] = '21';
        if (empty($data['pasv']))
            $data['pasv'] = false;
        if (empty($data['ssl']))
            $data['ssl'] = false;
        if (empty($data['timeout']))
            $data['timeout'] = 30;

        return $this->connect($data['server'], $data['username'], $data['password'], $data['port'], $data['pasv'], $data['ssl'], $data['timeout']);
    }

    /**
     * 连接FTP服务器
     *
     * @param string $host
     *            服务器地址
     * @param string $username
     *            用户名
     * @param string $password
     *            密码
     * @param integer $port
     *            服务器端口，默认值为21
     * @param boolean $pasv
     *            是否开启被动模式
     * @param boolean $ssl
     *            是否使用SSL连接
     * @param integer $timeout
     *            超时时间
     */
    protected function connect($host, $username = '', $password = '', $port = '21', $pasv = true, $ssl = false, $timeout = 30)
    {
        $start = time();
        if ($ssl) {
            if (!$this->link = @ftp_ssl_connect($host, $port, $timeout)) {
                $this->errCode = 1;
                return false;
            }
        } else {
            if (!$this->link = @ftp_connect($host, $port, $timeout)) {
                $this->errCode = 1;
                return false;
            }
        }

        if (@ftp_login($this->link, $username, $password)) {
            if ($pasv)
                ftp_pasv($this->link, true);
            $this->linkTime = time() - $start;
            return true;
        } else {
            $this->errCode = 1;
            return false;
        }
    }

    /**
     * 创建文件夹
     *
     * @param string $dirname
     *            目录名，
     */
    protected function mkdir($dirname)
    {
        if (!$this->link) {
            $this->errCode = 2;
            return false;
        }
        $dirname = $this->checkDirname($dirname);
        $nowdir = '/';
        foreach ($dirname as $v) {
            if ($v && !$this->chdir($nowdir . $v)) {
                if ($nowdir)
                    $this->chdir($nowdir);
                @ftp_mkdir($this->link, $v);
            }
            if ($v)
                $nowdir .= $v . '/';
        }
        return true;
    }

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
        if (!$this->link) {
            $this->errCode = 2;
            return false;
        }
        $dirname = pathinfo($remote, PATHINFO_DIRNAME);
        //当目录不存在时,更改工作目录,会漏一层目录.所以需要标记一个是否存在,然后补一次更改工作目录.
        // 如果不做这个操作会导致第一次上传不存在的目录时文件和最后一个目录同级而不是下级.
        $workDirFlag = true;
        if (!$this->chdir($dirname)) {
            $workDirFlag = false;
            $this->mkdir($dirname);
            $this->chdir($dirname);
        }
        if (!$workDirFlag) {
            $chdir = @ftp_chdir($this->link, '/' . $dirname . '/');
        }

        $remoteFileName = pathinfo($remote, PATHINFO_FILENAME) . '.' . pathinfo($remote, PATHINFO_EXTENSION);

        if (ftp_put($this->link, $remoteFileName, $local, $this->mode)) {
            $fileUrl = $this->data['url'] . DIRECTORY_SEPARATOR . $remote;
            return $this->parseUrl($fileUrl);
        } else {
            $this->errCode = 7;
            return false;
        }
    }

    protected function parseUrl($fileUrl)
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', $fileUrl);
    }

    /**
     * 在 FTP 服务器上改变当前目录
     *
     * @param string $dirname
     *            修改服务器上当前目录
     */
    protected function chdir($dirname)
    {
        if (!$this->link) {
            $this->errCode = 2;
            return false;
        }
        if (@ftp_chdir($this->link, $dirname)) {
            return true;
        } else {
            $this->errCode = 6;
            return false;
        }
    }

    /**
     * 获取错误信息
     */
    public function getError()
    {
        if (!$this->errCode)
            return false;
        $err_msg = array('1' => 'Server can not connect', '2' => 'Not connect to server', '3' => 'Can not delete non-empty folder', '4' => 'Can not delete file', '5' => 'Can not get file list', '6' => 'Can not change the current directory on the server', '7' => 'Can not upload files'
        );
        return $err_msg[$this->errCode];
    }

    /**
     * 检测目录名
     *
     * @param string $url
     *            目录
     * @return 由 / 分开的返回数组
     */
    protected function checkDirname($url)
    {
        $url = str_replace('', '/', $url);
        $urls = explode('/', $url);
        return $urls;
    }

    /**
     * 关闭FTP连接
     */
    public function close()
    {
        return @ftp_close($this->link);
    }
}
