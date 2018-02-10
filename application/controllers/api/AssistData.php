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

    public function Warehouse() {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchWarehouseList();
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }
    private function fetchWarehouseList() {
        $v = array();
        $list = $this->mysql_model->get_results(STORAGE,'(isDelete=0) order by id desc');
        foreach ($list as $arr=>$row) {
            $v[$arr]['address']     = $row['address'];;
            $v[$arr]['delete']      = $row['disable'] > 0 ? true : false;
            $v[$arr]['allowNeg']    = false;
            $v[$arr]['deptId']      = intval($row['deptId']);;
            $v[$arr]['empId']       = intval($row['empId']);;
            $v[$arr]['groupx']      = $row['groupx'];
            $v[$arr]['id']          = intval($row['id']);
            $v[$arr]['number']  = $row['number'];
            $v[$arr]['name']        = $row['name'];
            $v[$arr]['email']       = $row['email'];
            $v[$arr]['phone']       = $row['phone'];
            $v[$arr]['manager']       = $row['manager'];
            $v[$arr]['type']        = intval($row['type']);
        }
        $data['rows']       = $v;
        $data['total']      = 1;
        $data['records']    = $this->mysql_model->get_count(STORAGE,'(isDelete=0)');
        $data['page']       = 1;
        return $data;
    }

    public function Currency() {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchCurrencyList();
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }
    private function fetchCurrencyList() {
        $v = '';
        $list = $this->mysql_model->get_results(CURRENCY,'(isDelete=0) order by id');
        foreach ($list as $arr=>$row) {
            $v[$arr]['id']         = intval($row['id']);
            $v[$arr]['code']       = $row['code'];
            $v[$arr]['name']       = $row['name'];
            $v[$arr]['symbol']       = $row['symbol'];
            $v[$arr]['rate']       = $row['rate'];
            $v[$arr]['note']       = $row['note'];
            $v[$arr]['isDelete']   = intval($row['isDelete']);
        }
        $data['items']     = is_array($v) ? $v : '';
        $data['totalsize'] = $this->mysql_model->get_count(CURRENCY,'(isDelete=0)');
        return $data;
    }

    public function Inventory() {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchInventoryList();
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }
    private function fetchInventoryList() {
        $v = array();
        $where = '';
        $data['records']   = $this->data_model->get_goods($where,3);   //总条数
        $list = $this->data_model->get_goods($where.' order by id desc');
        //exit(print_r($list));
        foreach ($list as $arr=>$row) {
            $v[$arr]['amount']        = (float)$row['iniamount'];
            $v[$arr]['barCode']       = $row['barCode'];
            $v[$arr]['categoryName']  = $row['categoryName'];
            $v[$arr]['currentQty']    = $row['totalqty'];                            //当前库存
            $v[$arr]['delete']        = intval($row['disable'])==1 ? true : false;   //是否禁用
            $v[$arr]['discountRate']  = 0;
            $v[$arr]['id']            = intval($row['id']);
            $v[$arr]['isSerNum']      = intval($row['isSerNum']);
            $v[$arr]['josl']     = $row['josl'];
            $v[$arr]['name']     = $row['name'];
            $v[$arr]['number']   = $row['number'];
            $v[$arr]['pinYin']   = $row['pinYin'];
            $v[$arr]['locationId']   = intval($row['locationId']);
            $v[$arr]['locationName'] = $row['locationName'];
            $v[$arr]['locationNo'] = '';
            $v[$arr]['purPrice']   = $row['purPrice'];
            $v[$arr]['currency']   = $row['currency'];
            $v[$arr]['quantity']   = $row['iniqty'];
            $v[$arr]['salePrice']  = $row['salePrice'];
            $v[$arr]['skuClassId'] = $row['skuClassId'];
            $v[$arr]['spec']       = $row['spec'];
            $v[$arr]['remark']       = $row['remark'];
            $v[$arr]['unitCost']   = $row['iniunitCost'];
            $v[$arr]['unitId']     = intval($row['unitId']);
            $v[$arr]['unitName']   = $row['unitName'];


            // 库存数量
            // 获取该商品在库数量
            $res = $this->mysql_model->query ( INVOICE_INFO, "SELECT SUM(qty) as stockQty from " . INVOICE_INFO . " WHERE invId=$row[id]" );
            $v [$arr] ['stockQty'] = $res ['stockQty'];

        }
        $data['rows']   = $v;
        return $data;
    }

    public function Unit() {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchUnitList();
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }
    private function fetchUnitList() {
        $where = '';
        $v = '';
        $list = $this->mysql_model->get_results(UNIT,'(isDelete=0) '.$where.' order by id desc');
        foreach ($list as $arr=>$row) {
            $v[$arr]['default']    = $row['default']==1 ? true : false;
            $v[$arr]['guid']       = $row['guid'];
            $v[$arr]['id']         = intval($row['id']);
            $v[$arr]['name']       = $row['name'];
            $v[$arr]['rate']       = intval($row['rate']);
            $v[$arr]['isDelete']   = intval($row['isDelete']);
            $v[$arr]['unitTypeId'] = intval($row['unitTypeId']);
        }
        $data['items']     = is_array($v) ? $v : '';
        $data['totalsize'] = $this->mysql_model->get_count(UNIT,'(isDelete=0) '.$where.'');
        return $data;
    }
}
