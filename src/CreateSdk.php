<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/29
 * Time: 11:00
 */

namespace Mrstock\Helper;

use App\Logic\Sdk;
use Mrstock\Mjc\Control;
use Mrstock\Mjc\Http\Request;

class CreateSdk
{
    const USER = 'kevin';
    const PASSWD = '12345678';
    const EMAIL = 'chenfei@guxiansheng.cn';
    private $env = '';         // 当前环境(dev/qa/pre/master)
    private $project_group = '';      // 项目组(im/message...)
    private $sdk_group = '';      // sdk组(gxs-app/dx-app/php-service)
    private $service = [];    // 更改的服务
    private $commitname = ''; // 提交者信息
    private $project = '';
    private $srcpath = '';
    private $sdkpath = '';
    private $fail = 0;
    private $failmsg = [];


    public function index($data)
    {
        if (empty($data)) {
            return false;
        }
        $this->runpath = $data['runpath'] ?? 'E:/GXS/zhongtai/';
        $this->project = $data['projectpath'] ?? 'E:/GXS/zhongtai-sdk/';
        $this->commitname = $data['commituser'];
        $this->sdk_group = $data['sdk_group'];
        $this->project_group = $data['project_group'];
        $this->changeService = explode(',', $data['service_name']);
        $this->service = $data['service_name'];
        $this->sdkpath = $this->project . $this->service;
        $this->srcpath = $this->sdkpath . '/src/';
        $result = $this->make();
        return $result;
    }

    /**
     * 参数校验
     * @param Request $request
     * @return array|bool
     */

    public function make()
    {
        $modules = [
            'Inneruse',
            'Institution',
            'Company',
            'User',
            'Cloud',
            'Manager',
            'App'
        ];

        $versions = [
            'application',
            'v2'
        ];

        // 删除临时目录
        // $this->rmdir();

        // clone sdk
        //$this->clonesdk();
        $this->copyComposer();

        // 生成SDK
        foreach ($versions as $version) {
            $this->start($this->changeService, $this->env, $modules, $version);
        }

        // 推送到仓库
        // $this->push();

        if ($this->fail == 0) return true;

        return $this->failmsg;
    }

    private function rmdir()
    {
        file_put_contents("rm.sh", "rm -rf {$this->runpath}*");
        // system('sh ./rm.sh >/dev/null 2>&1');
    }

    private function clonesdk()
    {
        file_put_contents('clonesdk.sh', "mkdir -p {$this->runpath}\n");
        file_put_contents('clonesdk.sh', "cd {$this->runpath}\n", FILE_APPEND);
        $clone = sprintf(
            "git clone http://%s:%d@192.168.10.21/{$this->sdk_group}/{$this->service}.git",
            self::USER,
            self::PASSWD
        );

        file_put_contents('clonesdk.sh', "{$clone}\n", FILE_APPEND);
        file_put_contents('clonesdk.sh', "cd {$this->runpath}{$this->service}/ \n", FILE_APPEND);
        file_put_contents('clonesdk.sh', "git checkout {$this->env} \n", FILE_APPEND);
        file_put_contents('clonesdk.sh', "rm -rf {$this->runpath}{$this->service}/src/*", FILE_APPEND);
        // system("sh ./clonesdk.sh >/dev/null 2>&1");
    }

    private function copyComposer()
    {
        $content = '{
  "name": "{SDKGROUP}sdk\/{SERVICE}",
  "description": "Package description here.",
  "license": "MIT",
  "authors": [
    {
      "name": "kevin",
      "email": "chenfei@guxiansheng.cn"
    }
  ],
  "autoload": {
    "psr-4": {
      "{SDKGROUP_AUTOLOAD}\\\{SERVICE_AUTOLOAD}\\\": "src"
    }
  }
}';

        $sdk_group = substr($this->sdk_group, 0, -4);
        $content = str_replace("{SDKGROUP}", $sdk_group, $content);
        $content = str_replace("{SERVICE}", $this->service, $content);
        $content = str_replace("{SDKGROUP_AUTOLOAD}", ucfirst($sdk_group), $content);
        $content = str_replace("{SERVICE_AUTOLOAD}", ucfirst($this->service), $content);
        $fileName = $this->sdkpath . '/' . "composer.json";
        $this->handleService($fileName);
        file_put_contents($fileName, $content);
    }

    /**
     * cd
     * git clone lib
     * git checkout env
     * git add .
     * git push origin dev
     */
    private function push()
    {
        file_put_contents('push.sh', "cd {$this->sdkpath}\n");
        file_put_contents('push.sh', "git add {$this->sdkpath} \n", FILE_APPEND);
        $email = sprintf(
            'git config user.email %s',
            self::EMAIL
        );
        file_put_contents('push.sh', "{$email} \n", FILE_APPEND);
        file_put_contents('push.sh', "git commit -m'生成{$this->sdk_group}/{$this->service}@{$this->commitname}' \n", FILE_APPEND);
        // file_put_contents('push.sh', "git push origin {$this->env} \n", FILE_APPEND);
        file_put_contents('push.sh', "git checkout master \n", FILE_APPEND);
        file_put_contents('push.sh', "git merge dev \n", FILE_APPEND);
        file_put_contents('push.sh', "git push origin master \n", FILE_APPEND);
        // system('sh ./push.sh');
        //  system('sh ./push.sh >/dev/null 2>&1');
    }

    /**
     * @param $changeService array  需要生成SDK的service
     * @param $env           string 当前环境
     * @param $result        array  全部服务
     * @param $modules       array  默认 Inneruse
     * @param $version       string  版本
     */
    private function start($changeService, $env, $modules, $version)
    {

        foreach ($changeService as $item) {

            if ($this->sdk_group == 'zhongtai-sdk') {

                $site = trim($item) . ".service";

                $project = substr($this->sdk_group, 0, -4);

            } else {

                $site = trim($item) . ".app";
                $project = substr($this->sdk_group, 0, -4) . "-app";
            }
            $servicePath = $this->runpath . $site . "/";
            $devingPath = $servicePath . $version;
            //  $format = "git clone http://%s:%d@192.168.10.21/{$project}/%s.git %s";

            //  $cmd = sprintf(
            //   $format,
            //   self::USER,
            //   self::PASSWD,
            //  $site,
            //   $servicePath
            //   );

            //   file_put_contents('service.sh', "mkdir -p {$servicePath}\n");
            //    file_put_contents('service.sh', $cmd . "\n", FILE_APPEND);
            //  file_put_contents('service.sh', "cd {$servicePath}\n", FILE_APPEND);
            //  file_put_contents('service.sh', "git checkout {$env} \n", FILE_APPEND);
            //  file_put_contents('service.sh', "git pull\n", FILE_APPEND);
            // system("sh ./service.sh >/dev/null 2>&1");

            $rpcapipaths[] = $devingPath;
        }

        $this->startOneService($rpcapipaths, $modules);
    }

    private function startOneService($rpcapipaths, $modules)
    {
        $services = [];
        if (is_array($rpcapipaths)) {
            foreach ($rpcapipaths as $item) {
                $dirArray = explode('/', $item);

                if (preg_match('/\/?(\w*?).service/i', $item, $siteMatches)) {
                    $service_pre = $siteMatches[0];
                }
                $service_pre = explode('/', $service_pre);

                if (PHP_OS == 'Linux') {
                    $serviceArray = explode('.', $service_pre[1] ? $service_pre[1] : $dirArray[3]);
                } else {
                    $serviceArray = explode('.', $service_pre[1] ? $service_pre[1] : $dirArray[3]);
                }


                $path = str_replace('/', "\\", $item);

                $services[$serviceArray[0]] = $path;
            }
        }

        $this->parseService($services, $modules);
    }

    // 处理服务
    private function parseService($services, $modules)
    {
        $serviceMethods = '';
        foreach ($services as $serviceName => $service) {


            $serviceName = \ucfirst($serviceName);
            $this->handleService($serviceName);

            $this->parseServiceModule($service, $serviceName, $modules);

            $serviceMethods .= " * @method static  " . $serviceName
                . "\ModuleResolver " . strtolower($serviceName)
                . "() $serviceName 服务 \n";
        }
    }

    // 创建服务目录
    private function handleService($serviceName)
    {
        $serviceName = $this->sdkpath;

        $str = "create service dir $serviceName";
        if (is_dir($serviceName)) {
            return;
        }
        if (mkdir($serviceName, 0777, true)) {

            return;
        } else {
            $this->fail++;
            $this->failmsg[] = $str . " fail";
        }
    }

    // 处理模块
    private function parseServiceModule($service, $serviceName, $modules)
    {
        $moduleMethod = '';
        foreach ($modules as $module) {
            $result = $this->parseControlVersion($service, $module, $serviceName);

            if ($result) {
                $moduleMethod .= " * @method " . $module . "\VersionResolver "
                    . strtolower($module) . "() $module 模块 \n";
            }
        }

        if ($moduleMethod) {
            $namespace = $serviceName;
            $this->adapteModuleResolver($namespace, $moduleMethod);
            return true;
        }
        $this->fail++;
        $this->failmsg[] = "创建 $serviceName parseServiceModule 失败";
    }

    // 处理控制器版本
    private function parseControlVersion($service, $moduleName, $serviceName)
    {
        $service = str_replace("\"", "\\", $service);
        $modulePath = $service . "\\" . $moduleName;

        $version = $this->getVersion($modulePath);

        $dirs = [$version];

        if ($dirs && count($dirs) > 0) {
            $this->handleModule($moduleName);
            $methods = '';

            $versionName = $version;
            $versionDir = "Control\\";

            $this->handleVersion($moduleName, $versionName);
            $versionPath = $modulePath . "\\" . $versionDir;

            $result = $this->parseControl($versionPath, $moduleName, $serviceName, $versionName);

            if ($result) {
                $methods .= " * @method " . $versionName
                    . "\ControlResolver "
                    . strtolower($versionName)
                    . "() 控制器 $versionName 版本 \n";
            }

            $namespace = $moduleName;
            $this->adapteVersionResolver($namespace, $methods);
            return true;
        }
        $this->fail++;
        $this->failmsg[] = "创建 $serviceName parseControlVersion 失败";
    }

    private function getVersion($modulePath)
    {
        $pathArr = explode("\\", $modulePath);
        $version = $pathArr[count($pathArr) - 2];
        if ($version == "application") {
            $version = "V";
        }
        return $version;
    }

    // 创建模块目录
    private function handleModule($moduleName)
    {
        $serviceName = $this->srcpath . ucfirst($moduleName);
        $str = "create version dir $serviceName";
        if (is_dir($serviceName)) {
            return;
        }
        if (mkdir($serviceName, 0777, true)) {
            return;
        } else {
            $this->fail++;
            $this->failmsg[] = $str . " fail";
        }
    }

    // 创建控制器版本目录
    private function handleVersion($moduleName, $versionName)
    {
        $serviceName = $this->srcpath . ucfirst($moduleName) . '/' . ucfirst($versionName);
        $str = "create version dir $serviceName";
        if (is_dir($serviceName)) {
            return;
        }
        if (mkdir($serviceName, 0777, true)) {
            return;
        } else {
            $this->fail++;
            $this->failmsg[] = $str . " fail";
        }
    }

    // 创建控制器目录
    private function handleControl($moduleName, $versionName, $controlName)
    {
        $serviceName = $this->srcpath . ucfirst($moduleName) . '/' . ucfirst($versionName)
            . '/' . $controlName;
        $str = "create control dir $serviceName";
        if (is_dir($serviceName)) {
            return;
        }
        if (mkdir($serviceName, 0777, true)) {
            return;
        } else {
            $this->fail++;
            $this->failmsg[] = $str . " fail";
        }
    }

    // 处理控制器
    private function parseControl($path, $moduleName, $serviceName, $versionName)
    {
        $path = str_replace('\\', '/', $path);

        $scanFiles = scandir($path);

        $methods = "";
        if ($scanFiles && count($scanFiles) > 0) {
            foreach ($scanFiles as $file) {

                $isFile = stripos($file, '.php');
                if ($isFile > 0) {
                    $filePath = $path . DIRECTORY_SEPARATOR . $file;
                    $fileArray = explode('.', $file);
                    $className = $fileArray[0];
                    $contrlName = str_replace('Control', '', $className);
                    $this->handleControl($moduleName, $versionName, $contrlName);

                    $this->parseAction($filePath, $serviceName, $moduleName, $versionName, $contrlName);

                    $methods .= " * @method " . $contrlName . "\ApiResolver "
                        . lcfirst($contrlName) . "() $contrlName 控制器 \n";
                }
            }
            $namespace = $moduleName . "\\" . ucfirst($versionName);

            $this->adapteControlResolver($namespace, $methods);
            return true;
        }
    }

    // 处理操作
    private function parseAction($filePath, $serviceName, $moduleName, $versionName, $contrlName)
    {
        $content = file_get_contents($filePath);
        $tokens = token_get_all($content);
        $functions = $this->getOpsFromToken($tokens);
        $versionName = ucfirst($versionName);
        $namespace = $moduleName . "\\" . $versionName . "\\" . $contrlName;
        $methods = '';
        if ($functions && is_array($functions) && count($functions) > 0) {
            foreach ($functions as $function) {
                $this->adapteApi($namespace, $function['name'], '');

                $comment = $this->parseComment($function['comment']);

                $methods .= " * @method " . $function['name'] . " "
                    . strtolower($function['name']) . "("
                    . 'array $options = []' . ") $comment \n";
            }
            $this->adapteApiResolver($namespace, $methods);
        }
    }

    // 解析php文件
    private function getOpsFromToken($tokens)
    {
        $functions = [];
        $getting_function = false;
        $comment = [];

        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] == T_DOC_COMMENT) {
                $comment = $token[1];
            }

            if (is_array($token) && $token[0] == T_FUNCTION) {
                $getting_function = true;
            }

            if ($getting_function === true) {
                if (is_array($token) && $token[0] == T_STRING) {
                    $function['comment'] = $comment;
                    $function['name'] = $token[1];
                    if (stripos($function['name'], 'Op') > 0) {
                        $function['name'] = \ucfirst(str_replace('Op', '', $function['name']));
                        $functions[] = $function;
                    }
                    $getting_function = false;
                    $comment = [];
                }
            }
        }
        return $functions;
    }

    private function adapteModuleResolver($namespace, $methods)
    {
        // $templatePath = 'Template/ModuleResolver.php.Templage';
        $content = '<?php

namespace {SDKGROUP}\{NAMESPACE};

use MrstockCloud\Client\Traits\ModuleResolverTrait;

/**
 * Find the specified version of the {NAMESPACE} based on the method name as the version name.
 *
 * @package   {SDKGROUP}\{NAMESPACE}
 *
{METHODS}
 */
class ModuleResolver
{
    use ModuleResolverTrait;
}
';
        $sdk_group = substr($this->sdk_group, 0, -4);
        $content = str_replace("{SDKGROUP}", ucfirst($sdk_group), $content);
        $content = str_replace("{NAMESPACE}", $namespace, $content);
        $content = str_replace("{METHODS}", $methods, $content);
        $fileName = $this->srcpath . "ModuleResolver.php";
        file_put_contents($fileName, $content);
    }

    private function adapteVersionResolver($namespace, $methods)
    {
        if (PHP_OS == 'Linux') {
            $namespace_di = str_replace('\\', '/', $namespace);
        } else {
            $namespace_di = $namespace;
        }
        $fileName = $this->srcpath . $namespace_di . DIRECTORY_SEPARATOR . "VersionResolver.php";
        if (file_exists($fileName)) {
            $this->appendVersionResolver($namespace, $methods);
        } else {
            // $templatePath = 'Template/VersionResolver.php.Templage';
            $content = '<?php

namespace {SDKGROUP}\{NAMESPACE};

use MrstockCloud\Client\Traits\VersionResolverTrait;

/**
 * Find the specified version of the {NAMESPACE} based on the method name as the version name.
 *
 * @package   {SDKGROUP}\{NAMESPACE}
 *
{METHODS}
 */
class VersionResolver
{
    use VersionResolverTrait;
}
';
            $sdk_group = substr($this->sdk_group, 0, -4);
            $content = str_replace("{SDKGROUP}", ucfirst($sdk_group), $content);
            $content = str_replace("{NAMESPACE}", ucfirst($this->service) . "\\" . $namespace, $content);
            $content = str_replace("{METHODS}", $methods, $content);
            $fileName = $this->srcpath . $namespace_di . DIRECTORY_SEPARATOR . "VersionResolver.php";
            file_put_contents($fileName, $content);
        }
    }

    private function appendVersionResolver($namespace, $methods)
    {
        if (PHP_OS == 'Linux') {
            $namespace_di = str_replace('\\', '/', $namespace);
        } else {
            $namespace_di = $namespace;
        }
        $fileName = $this->sdkpath . $namespace_di . DIRECTORY_SEPARATOR . "VersionResolver.php";
        $content = file_get_contents($fileName);
        if (stripos($content, $methods) == 0) {
            $content = str_replace("*/", "*\n" . $methods . "\n" . "*/", $content);
        }
        $fileName = $this->sdkpath . $namespace_di . DIRECTORY_SEPARATOR . "VersionResolver.php";
        file_put_contents($fileName, $content);
    }

    private function adapteControlResolver($namespace, $methods)
    {
        // $templatePath = 'Template/ControlResolver.php.Templage';
        $content = '<?php

namespace {SDKGROUP}\{NAMESPACE};

use MrstockCloud\Client\Traits\ControlResolverTrait;

/**
 * Resolve Api based on the method name.
 *
{METHODS}
 */
class ControlResolver
{
    use ControlResolverTrait;
}
';

        $content = str_replace("{SDKGROUP}", $this->sdk_group == "zhongtai" ? ucfirst($this->sdk_group) :
            ucfirst(substr($this->sdk_group, 0, -4)), $content);
        $content = str_replace("{NAMESPACE}", ucfirst($this->service) . "\\" . $namespace, $content);
        $content = str_replace("{METHODS}", $methods, $content);

        if (PHP_OS == 'Linux') {
            $namespace = str_replace('\\', '/', $namespace);
        }
        $fileName = $this->srcpath . $namespace . DIRECTORY_SEPARATOR . "ControlResolver.php";
        file_put_contents($fileName, $content);
    }

    private function adapteApiResolver($namespace, $methods)
    {
        //$templatePath = 'Template/ApiResolver.php.Templage';
        $content = '<?php

namespace {SDKGROUP}\{NAMESPACE};

use MrstockCloud\Client\Traits\ApiResolverTrait;

/**
 * Resolve Api based on the method name.
 *
{METHODS}
 */
class ApiResolver
{
    use ApiResolverTrait;
}
';
        $content = str_replace("{SDKGROUP}", $this->sdk_group == "zhongtai" ? ucfirst($this->sdk_group) :
            ucfirst(substr($this->sdk_group, 0, -4)), $content);
        $content = str_replace("{NAMESPACE}", ucfirst($this->service) . "\\" . $namespace, $content);
        $content = str_replace("{METHODS}", $methods, $content);
        if (PHP_OS == 'Linux') {
            $namespace = str_replace('\\', '/', $namespace);
        }
        $fileName = $this->srcpath . $namespace . DIRECTORY_SEPARATOR . "ApiResolver.php";
        file_put_contents($fileName, $content);
    }

    private function adapteApi($namespace, $className, $ops)
    {
        $_GET["i"] = intval($_GET["i"]) + 1;
        // $templatePath = 'Template/Api.php.Templage';
        $content = '<?php

namespace {SDKGROUP}\{NAMESPACE};

use MrstockCloud\Client\Request\RpcRequest;


class {CLASSNAME} extends RpcRequest
{

	{OPS}
}';
        $content = str_replace("{SDKGROUP}", $this->sdk_group == "zhongtai" ? ucfirst($this->sdk_group) :
            ucfirst(substr($this->sdk_group, 0, -4)), $content);
        $content = str_replace("{NAMESPACE}", ucfirst($this->service) . "\\" . $namespace, $content);
        $content = str_replace("{CLASSNAME}", $className, $content);
        $content = str_replace("{OPS}", $ops, $content);
        if (PHP_OS == 'Linux') {
            $namespace = str_replace('\\', '/', $namespace);
        }
        $fileName = $this->srcpath . $namespace . DIRECTORY_SEPARATOR . $className . ".php";
        file_put_contents($fileName, $content);
    }


    // 解析注释
    private function parseComment($detail)
    {
        $actionPreg = '/@OpDescription\((.*?)\)/';
        $str = '';
        if (preg_match_all($actionPreg, $detail, $matches)) {
            $str = $matches[1][0];
            return $str;
        }

        return $str;
    }
}