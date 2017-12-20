<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Config extends CI_Config {
    /**
     * @param $baseUrl
     * @param $uri
     * @return string
     * 相对路径转网络绝对路径
     */
    function dirToHttpUrl($baseUrl,$uri) {
        //文件路径的层次统计
        $tempPath = explode('../', $uri);
        $tempNum = array_count_values($tempPath);
        if (array_key_exists('', $tempNum)) {
            $pathNum = $tempNum[''];
            $pathEnd = end($tempPath);
        } else {
            $pathNum = 0;
            $pathEnd = $uri;
        }
        //域名层次统计
        $tempWeb = explode('/', $baseUrl);
        $webNum = count($tempWeb);
        if ($pathNum >= $webNum-3) {
            return array('baseUrl'=>$baseUrl,"uri"=>"");
        }
        else
        {
            $tempWeb = array_slice($tempWeb, 0,$webNum-$pathNum-1);
            $nowUrl=implode('/',$tempWeb).'/';
            return array('baseUrl'=>$nowUrl,"uri"=>$pathEnd);
        }
    }

    function site_url($uri = '')
    {
        if(defined('BROWSER')&&BROWSER=='mobile') {
            $baseUrl = $this->slash_item('base_url');
            if ($uri == '') {
                return $baseUrl . $this->item('index_page');
            }
            $uri = $this->_uri_string($uri);
            if ($this->item('enable_query_strings') == FALSE) {
                $suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
                $arr = $this->dirToHttpUrl($baseUrl, $uri);
                return $arr['baseUrl'] . $this->slash_item('index_page') . $arr['uri'] . $suffix;
            } else {
                $arr = $this->dirToHttpUrl($baseUrl, $uri);
                return $arr['baseUrl'] . $this->item('index_page') . '?' . $arr['uri'];
            }
        }
        else
        {
            return parent::site_url($uri);
        }
    }
}