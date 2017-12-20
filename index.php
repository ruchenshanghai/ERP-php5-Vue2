<?php
///*移动端判断*/
//function isMobile()
//{
//    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
//    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
//    {
//        return true;
//    }
//    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
//    if (isset ($_SERVER['HTTP_VIA']))
//    {
//        // 找不到为flase,否则为true
//        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
//    }
//    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
//    if (isset ($_SERVER['HTTP_USER_AGENT']))
//    {
//        $clientkeywords = array ('nokia',
//            'sony',
//            'ericsson',
//            'mot',
//            'samsung',
//            'htc',
//            'sgh',
//            'lg',
//            'sharp',
//            'sie-',
//            'philips',
//            'panasonic',
//            'alcatel',
//            'lenovo',
//            'iphone',
//            'ipod',
//            'blackberry',
//            'meizu',
//            'android',
//            'netfront',
//            'symbian',
//            'ucweb',
//            'windowsce',
//            'palm',
//            'operamini',
//            'operamobi',
//            'openwave',
//            'nexusone',
//            'cldc',
//            'midp',
//            'wap',
//            'mobile'
//        );
//        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
//        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
//        {
//            return true;
//        }
//    }
//    // 协议法，因为有可能不准确，放到最后判断
//    if (isset ($_SERVER['HTTP_ACCEPT']))
//    {
//        // 如果只支持wml并且不支持html那一定是移动设备
//        // 如果支持wml和html但是wml在html之前则是移动设备
//        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
//        {
//            return true;
//        }
//    }
//    return false;
//}
//
//if(isMobile())
//{
//    $url=$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
//    $index = strpos($url,"index.php");
//    if($index>0)
//    {
//        $url=substr($url,0,$index);
//    }
//    $url="http://".$url."mobile";
//    header("Location: $url");
//    exit();
//}

//$ex=new Exception();
//exit($ex->getTraceAsString());

date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
define('ENVIRONMENT', 'production');  
if (defined('ENVIRONMENT')) {
	switch (ENVIRONMENT) {
		case 'development':
			error_reporting(E_ALL);
		break;
		case 'testing':
		case 'production':
			error_reporting(0);
		break;
		default:
			exit('The application environment is not set correctly.');
	}
}
//exit("this");
$system_path = 'system';
$application_folder = 'application';

if (defined('STDIN')) {
	chdir(dirname(__FILE__));
}
if (realpath($system_path) !== FALSE) {
	$system_path = realpath($system_path).'/';
}

$system_path = rtrim($system_path, '/').'/';

if ( ! is_dir($system_path)) {
	exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

define('EXT', '.php');

define('BASEPATH', str_replace("\\", "/", $system_path));

define('FCPATH', str_replace(SELF, '', __FILE__));
$patharr=explode('\\', FCPATH);
$pathcount=count($patharr);
if($pathcount>2)
{
    if($patharr[$pathcount-1]!='')
    {
        define('SRCROOT', $patharr[$pathcount-1]);
    }
    else if($patharr[$pathcount-2]!='')
    {
        define('SRCROOT', $patharr[$pathcount-2]);
    }
}
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

if (is_dir($application_folder)) {
	define('APPPATH', $application_folder.'/');
} else {
	if ( ! is_dir(BASEPATH.$application_folder.'/')) {
		exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
	}
	define('APPPATH', BASEPATH.$application_folder.'/');
}
define('JXC_VERSION', 'jxc 2.0.1'); 
//exit(BASEPATH.'core/CodeIgniter.php');
require_once BASEPATH.'core/CodeIgniter.php';

/* End of file index.php */
/* Location: ./index.php */