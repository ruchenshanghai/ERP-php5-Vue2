<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by IntelliJ IDEA.
 * User: wenjin
 * Date: 2018/1/2
 * Time: 14:43
 */

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

require 'ResponseMessage.php';

class AssistData extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function PayMethod()
    {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchMethodList($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }

    public function ShippingMethod()
    {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchMethodList($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }

    private function fetchMethodList($fetchConfig) {
        $v = array();
        $type = $fetchConfig->typeNumber;
        $skey = $fetchConfig->skey;
        $where = ' and typenumber="' . $type . '"';
        $where .= $skey ? ' and name like "%' . $skey . '%"' : '';
        $pid = array_column($this->mysql_model->get_results(CATEGORY, '(isDelete=0) ' . $where . ' order by id'), 'parentId');
        $list = $this->mysql_model->get_results(CATEGORY, '(isDelete=0) ' . $where . ' order by path');
        foreach ($list as $arr => $row) {
            $v[$arr]['detail'] = in_array($row['id'], $pid) ? false : true;
            $v[$arr]['id'] = intval($row['id']);
            $v[$arr]['level'] = $row['level'];
            $v[$arr]['name'] = $row['name'];
            $v[$arr]['parentId'] = intval($row['parentId']);
            $v[$arr]['remark'] = $row['remark'];
            $v[$arr]['sortIndex'] = intval($row['sortIndex']);
            $v[$arr]['status'] = intval($row['isDelete']);
            $v[$arr]['typeNumber'] = $row['typeNumber'];
        }
        $data['items'] = $v;
        $data['totalsize'] = $this->mysql_model->get_count(CATEGORY, '(isDelete=0) ' . $where . '');
        return $data;
    }

    public function Account() {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchAccountList();
            $response->info = $fetchData;
        }
        echo json_encode($response);

    }

    private function fetchAccountList() {
        $v = array();
        $list = $this->mysql_model->get_results(ACCOUNT,'(isDelete=0) order by id');
        foreach ($list as $arr=>$row) {
            $v[$arr]['date']        = $row['date'];
            $v[$arr]['amount']      = (float)$row['amount'];
            $v[$arr]['del']         = false;
            $v[$arr]['id']          = intval($row['id']);
            $v[$arr]['name']        = $row['name'];
            $v[$arr]['account']     = $row['account'];
            $v[$arr]['bank']     = $row['bank'];
            $v[$arr]['currency']     = intval($row['currency']);
            $v[$arr]['number']      = $row['number'];
            $v[$arr]['type']        = intval($row['type']);
        }
        $data['items']      = $v;
        $data['totalsize']  = $this->mysql_model->get_count(ACCOUNT,'(isDelete=0)');
        return $data;
    }

}
