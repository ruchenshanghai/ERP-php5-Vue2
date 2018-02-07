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

    public function Contact() {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchContactList($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }
    private function fetchContactList($fetchConfig) {
        $v = array();
        //客户为type=-10，供应商为type=10
        $type   = intval($fetchConfig->type) == 10 ? 10 : -10;
//        $skey   = str_enhtml($this->input->get_post('skey',TRUE));
//        $page   = max(intval($this->input->get_post('page',TRUE)),1);
//        $categoryid   = intval($this->input->get_post('categoryId',TRUE));
//        $rows   = max(intval($this->input->get_post('rows',TRUE)),100);
        $where  = ' and type='.$type;
//        $where .= $skey ? ' and (contact like "%'.$skey.'%" or linkMans like "%'.$skey.'%")' : '';
//        $where .= $categoryid>0 ? ' and cCategory = '.$categoryid.'' : '';
//        $offset = $rows * ($page-1);
//        $data['data']['page']      = $page;
        $data['records']   = $this->mysql_model->get_count(CONTACT,'(isDelete=0) '.$where.'');
//        $data['data']['total']     = ceil($data['data']['records']/$rows);
//        $list = $this->mysql_model->get_results(CONTACT,'(isDelete=0) '.$where.' order by id desc limit '.$offset.','.$rows.'');
        $list = $this->mysql_model->get_results(CONTACT,'(isDelete=0) '.$where.' order by id desc');
        foreach ($list as $arr=>$row) {
            $v[$arr]['id']           = intval($row['id']);
            $v[$arr]['RID']          = intval($row['id']);
            $v[$arr]['number']       = $row['number'];
            $v[$arr]['cCategory']    = intval($row['cCategory']);
            $v[$arr]['customerType'] = $row['cCategoryName'];
            $v[$arr]['pinYin']       = $row['pinYin'];
            $v[$arr]['name']         = $row['name'];
            $v[$arr]['type']         = $row['type'];
            $v[$arr]['delete']       = intval($row['disable'])==1 ? true : false;
            $v[$arr]['cLevel']       = intval($row['cLevel']);
            $v[$arr]['amount']       = (float)$row['amount'];
            $v[$arr]['periodMoney']  = (float)$row['periodMoney'];
            $v[$arr]['difMoney']     = (float)$row['difMoney'];
            $v[$arr]['remark']       = $row['remark'];
            $v[$arr]['taxRate']      = (float)$row['taxRate'];
            $v[$arr]['links']        = '';

            //开户行
            $v[$arr]['bank']           = isset($row['bank']) ? $row['bank'] : '';
            //银行账号
            $v[$arr]['account']           = isset($row['account']) ? $row['account'] : '';
            //税号
            $v[$arr]['taxnumber']           = isset($row['taxnumber']) ? $row['taxnumber'] : '';
            if (strlen($row['linkMans'])>0) {
                $list = (array)json_decode($row['linkMans'],true);
                foreach ($list as $arr1=>$row1) {
                    if ($row1['linkFirst']==1) {
                        //首要联系人的手机、座机、地址为该公司联系方式
                        $v[$arr]['contacter']        = isset($row1['linkName']) ? $row1['linkName'] : '';
                        $v[$arr]['linkTitle']           = isset($row1['linkTitle']) ? $row1['linkTitle'] : '';
                        $v[$arr]['mobile']           = isset($row1['linkMobile']) ? $row1['linkMobile'] : '';
                        $v[$arr]['telephone']        = isset($row1['linkPhone']) ? $row1['linkPhone'] : '';
                        $v[$arr]['linkIm']           = isset($row1['linkIm']) ? $row1['linkIm'] : '';
                        $v[$arr]['firstLink']['first']   = isset($row1['linkFirst']) ? $row1['linkFirst'] : '';
                        $v[$arr]['province']   = $row1['province'];
                        $v[$arr]['city']   = $row1['city'];
                        $v[$arr]['county']   = $row1['county'];
                        $v[$arr]['deliveryAddress']   = $row1['address'];
                    }
                }
            }
        }
        $data['rows']       = array_values($v);
        return $data;
    }

}
