<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by IntelliJ IDEA.
 * User: wenja
 * Date: 2017/12/20
 * Time: 14:38
 */
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

require 'ResponseMessage.php';

class PurchaseInbound extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function fetch()
    {
        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->fetchList($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }

    private function fetchList($fetchConfig)
    {
        $data = null;

        $pageIndex = max(intval($fetchConfig->pageIndex), 1);
        // default page size 100
        $pageSize = intval($fetchConfig->pageSize) > 0 ? intval($fetchConfig->pageSize) : 100;
        $keyword = $fetchConfig->keyword;
        $beginDate = $fetchConfig->beginDate;
        $endDate = $fetchConfig->endDate;

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;

        $sortKey = 'billDate';
        $sortType = 'desc';
        $order = $sortKey ? $sortKey . ' ' . $sortType : ' a.id desc';
        $transType = '150501';
        $where = ' and a.billType="PUR"';
        $where .= ' and a.transType=' . $transType;
        $where .= $keyword ? ' and (a.userName like "%' . $keyword . '%" or a.description like "%' . $keyword . '%" or a.billNo like "%' . $keyword . '%")' : '';
        $where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
        $where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';


        $offset = $pageSize * ($pageIndex - 1);
        $data['rowsCount'] = $this->data_model->get_invoice($where, 3);
        //总条数
        $data['pageCount'] = ceil($data['totalSize'] / $pageSize);
        //总分页数

        $list = $this->data_model->get_invoice($where . ' order by ' . $order . ' limit ' . $offset . ',' . $pageSize . '');
        foreach ($list as $arr => $row) {
            $list[$arr]['id'] = intval($row['id']);
            $list[$arr]['checkName'] = $row['checkName'];
            $list[$arr]['checked'] = intval($row['checked']);
            $list[$arr]['billDate'] = $row['billDate'];
            $list[$arr]['contactName'] = $row['contactName'];//$row['contactNo'].' '.$row['contactName'];
            $list[$arr]['description'] = $row['description'];
            $list[$arr]['billNo'] = $row['billNo'];
            $list[$arr]['userName'] = $row['userName'];
            $list[$arr]['locationName'] = $row['locationName'];
            $list[$arr]['disEditable'] = 0;
            $list[$arr]['totalQty'] = $row['totalQty'];
            if (!empty($row['srcId'])) {
                $orderInfo = $this->mysql_model->get_row(ORDER, '(id=' . $row['srcId'] . ')');
                if (count($orderInfo) > 0) {
                    $list[$arr]['puQty'] = $orderInfo['totalQty'];
                    if ($orderInfo['billStatus'] == 2) {
                        $list[$arr]['billStatus'] = "全部入库";
                    } elseif ($orderInfo['billStatus'] == 1) {
                        $list[$arr]['billStatus'] = "部分入库";
                    } else {
                        $list[$arr]['billStatus'] = "未入库";
                    }
                    $list[$arr]['srcBillNo'] = $orderInfo['billNo'];
                    $list[$arr]['srcId'] = $orderInfo['id'];
                }
            }
        }

        $data['rows'] = $list;
        return $data;
    }

    public function detail()
    {
        $postData = json_decode(file_get_contents("php://input"));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->detailInbound($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }

    //获取采购入库订单信息
    private function detailInbound($fetchConfig)
    {
        $data = null;
        $result = null;

        $id = $fetchConfig->id;
        $condition = "";
        if (!empty($id)) {
            $condition .= " and (a.id=$id)";
        }
        $data = $this->data_model->get_invoice($condition . ' and billType="PUR"', 1);
        if (count($data) > 0) {
            $s = $v = array();
            $result['id'] = intval($data['id']);
            $result['buId'] = intval($data['buId']);
            $result['contactName'] = $data['contactName'];
            $result['date'] = $data['billDate'];
            $result['billNo'] = $data['billNo'];
            $result['billType'] = $data['billType'];
            $result['modifyTime'] = $data['modifyTime'];
            $result['transType'] = intval($data['transType']);
            $result['totalQty'] = (float)$data['totalQty'];
            $result['totalTaxAmount'] = (float)$data['totalTaxAmount'];
            $result['billStatus'] = intval($data['billStatus']);
            $result['disRate'] = (float)$data['disRate'];
            $result['disAmount'] = (float)$data['disAmount'];
            $result['amount'] = (float)abs($data['amount']);
            $result['rpAmount'] = (float)abs($data['rpAmount']);
            $result['arrears'] = (float)abs($data['arrears']);
            $result['userName'] = $data['userName'];
            $result['checked'] = intval($data['checked']);
            $result['status'] = intval($data['checked']) == 1 ? 'view' : 'edit';

            $result['totalDiscount'] = (float)$data['totalDiscount'];
            $result['totalTax'] = (float)$data['totalTax'];
            $result['totalAmount'] = (float)abs($data['totalAmount']);
            //$info['data']['description']        = $data['description'];
            $list = $this->data_model->get_invoice_info('and (iid=' . $id . ') order by id');
            //exit(print_r($list));
            foreach ($list as $arr => $row) {
                //order info id
                $v[$arr]['srcId'] = $row['id'];
                $v[$arr]['spec'] = $row['invSpec'];
                $v[$arr]['srcEntryId'] = $row['srcEntryId'];
                $v[$arr]['srcBillNo'] = $row['srcBillNo'];
                $v[$arr]['srcId'] = $row['srcId'];
                $v[$arr]['goods'] = $row['invName'];
                $v[$arr]['invName'] = $row['invNumber'];
                $v[$arr]['stockinQty'] = (float)abs($row['qty']);
                $orderQty = $this->mysql_model->get_row(ORDER_INFO, "(id=$row[srcId])", 'qty');
                //exit("order qty:".$orderQty);
                if (!empty($orderQty)) {
                    $v[$arr]['qty'] = (float)abs($orderQty);
                } else {
                    $v[$arr]['qty'] = (float)abs($row['qty']);
                }
                $v[$arr]['amount'] = (float)abs($row['amount']);
                $v[$arr]['taxAmount'] = (float)abs($row['taxAmount']);
                $v[$arr]['price'] = (float)$row['price'];
                $v[$arr]['tax'] = (float)$row['tax'];
                $v[$arr]['taxRate'] = (float)$row['taxRate'];
                $v[$arr]['currencyCode'] = $row['currencyCode'];
                $v[$arr]['mainUnit'] = $row['mainUnit'];
                $v[$arr]['deduction'] = (float)$row['deduction'];
                $v[$arr]['invId'] = intval($row['invId']);
                $v[$arr]['invNumber'] = $row['invNumber'];
                $v[$arr]['locationId'] = intval($row['locationId']);
                $v[$arr]['locationName'] = $row['locationName'];
                $v[$arr]['discountRate'] = $row['discountRate'];
                $v[$arr]['unitId'] = intval($row['unitId']);
                //$v[$arr]['description']         = $row['description'];
                $v[$arr]['skuId'] = intval($row['skuId']);
                $v[$arr]['skuName'] = '';
            }
            $result['entries'] = $v;
            $result['accId'] = (float)$data['accId'];
            $accounts = $this->data_model->get_account_info('and (iid=' . $id . ') order by id');
            foreach ($accounts as $arr => $row) {
                $s[$arr]['orderId'] = intval($id);
                $s[$arr]['billNo'] = $row['billNo'];
                $s[$arr]['buId'] = intval($row['buId']);
                $s[$arr]['billType'] = $row['billType'];
                $s[$arr]['transType'] = $row['transType'];
                $s[$arr]['transTypeName'] = $row['transTypeName'];
                $s[$arr]['billDate'] = $row['billDate'];
                $s[$arr]['accId'] = intval($row['accId']);
                $s[$arr]['account'] = $row['accountNumber'] . '' . $row['accountName'];
                $s[$arr]['payment'] = (float)abs($row['payment']);
                $s[$arr]['wayId'] = (float)$row['wayId'];
                $s[$arr]['way'] = $row['categoryName'];
                $s[$arr]['settlement'] = $row['settlement'];
            }
            $result['accounts'] = $s;
        }
        return $result;
    }
}
