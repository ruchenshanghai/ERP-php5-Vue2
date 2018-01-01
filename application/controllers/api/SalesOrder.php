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

class SalesOrder extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function fetch()
    {
        $postData = json_decode(file_get_contents("php://input"));
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
        $auditState = intval($fetchConfig->auditState);

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;

        $sortKey = 'billDate';
        $sortType = 'desc';
        $order = $sortKey ? $sortKey . ' ' . $sortType : ' a.id desc';
        $transType = '150601';
        $where = "";
        $where .= ' and a.transType=' . $transType;
        $where .= $auditState > 0 ? ' and a.hxStateCode=' . $auditState : '';
        $where .= $keyword ? ' and (a.userName like "%' . $keyword . '%" or a.description like "%' . $keyword . '%" or a.billNo like "%' . $keyword . '%")' : '';
        $where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
        $where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';


        $offset = $pageSize * ($pageIndex - 1);
        $data['rowsCount'] = $this->data_model->get_sale($where, 3);                             //总条数
        $data['pageCount'] = ceil($data['totalSize'] / $pageSize);                                 //总分页数

        // fetch data from db
        $list = $this->data_model->get_sale($where . ' order by ' . $order . ' limit ' . $offset . ',' . $pageSize . '');
        //exit(var_dump($list));
        foreach ($list as $arr => $row) {
            $list[$arr]['hxStateCode'] = intval($row['hxStateCode']);
            $list[$arr]['checkName'] = $row['checkName'];
            $list[$arr]['checked'] = intval($row['checked']);
            $list[$arr]['salesId'] = intval($row['salesId']);
            $list[$arr]['staffName'] = $row['staffName'];
            $list[$arr]['billDate'] = $row['billDate'];
            $list[$arr]['billStatus'] = $row['billStatus'];
            $list[$arr]['totalQty'] = (float)$row['totalQty'];
            $list[$arr]['id'] = intval($row['id']);
            $list[$arr]['amount'] = (float)abs($row['amount']);
            $list[$arr]['transType'] = intval($row['transType']);
            $list[$arr]['rpAmount'] = (float)abs($row['rpAmount']);
            $list[$arr]['contactName'] = $row['contactName'];
            $list[$arr]['description'] = $row['description'];
            $list[$arr]['billNo'] = $row['billNo'];
            $list[$arr]['totalAmount'] = (float)abs($row['totalAmount']);
            $list[$arr]['userName'] = $row['userName'];
            $list[$arr]['transTypeName'] = $row['transTypeName'];
            if ($row['billStatus'] == 2) {
                $list[$arr]['billStatusName'] = "全部出库";
            } elseif ($row['billStatus'] == 1) {
                $list[$arr]['billStatusName'] = "部分出库";
            } else {
                $list[$arr]['billStatusName'] = "未出库";
            }
            if (!empty($row['deliveryDate'])) {
                try {
                    $datetime1 = date_create($row['deliveryDate']);
                    $datetime2 = date_create($row['billDate']);
                    $interval = date_diff($datetime1, $datetime2);
                    $list[$arr]['deliveryDate'] = $interval->days;
                } catch (Exception $e) {
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
            $fetchData = $this->detailOrder($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }

    private function detailOrder($fetchConfig)
    {
        $data = null;
        $result = null;

        $id = $fetchConfig->id;
        $data = $this->data_model->get_sale('and (a.id=' . $id . ")", 1);
        if (count($data) > 0) {
            $s = $v = array();
            $result['id'] = intval($data['id']);
            $result['buId'] = intval($data['buId']);
            $result['cLevel'] = 0;
            $result['contactName'] = $data['contactName'];
            $result['salesId'] = intval($data['salesId']);
            $result['date'] = $data['billDate'];
            $result['billNo'] = $data['billNo'];
            $result['billType'] = $data['billType'];
            $result['transType'] = intval($data['transType']);
            $result['totalQty'] = (float)$data['totalQty'];
            $result['modifyTime'] = $data['modifyTime'];
            $result['checked'] = intval($data['checked']);
            $result['checkName'] = $data['checkName'];
            $result['disRate'] = (float)$data['disRate'];
            $result['disAmount'] = (float)$data['disAmount'];
            $result['amount'] = (float)abs($data['amount']);
            $result['rpAmount'] = (float)abs($data['rpAmount']);
            $result['customerFree'] = (float)$data['customerFree'];
            $result['arrears'] = (float)abs($data['arrears']);
            $result['userName'] = $data['userName'];
            $result['status'] = intval($data['checked']) == 1 ? 'view' : 'edit'; //edit
            $result['totalDiscount'] = (float)$data['totalDiscount'];
            $result['totalAmount'] = (float)abs($data['totalAmount']);
            $result['description'] = $data['description'];
            $result['currency'] = intval($data['currency']);
            $result['currencyCode'] = $data['currencyCode'];
            $result['currencyText'] = $data['currencyText'];
            $result['paymentMethod'] = intval($data['paymentMethod']);
            $result['shippingMethod'] = intval($data['shippingMethod']);

            $result['accountName'] = intval($data['accountName']);
            if (!empty($data['deliveryDate'])) {
                try {
                    if ($data['deliveryDate'] != '0000-00-00') {
                        $datetime1 = date_create($data['deliveryDate']);
                        $datetime2 = date_create($data['billDate']);
                        $interval = date_diff($datetime1, $datetime2);
                        $result['deliveryDate'] = $interval->days;
                    } else {
                        $data['deliveryDate'] = "";
                    }
                } catch (Exception $e) {
                }
            }
            $list = $this->data_model->get_sale_info('and (iid=' . $id . ') order by id');
            foreach ($list as $arr => $row) {
                $v[$arr]['invSpec'] = $row['invSpec'];
                $v[$arr]['taxRate'] = (float)$row['taxRate'];
                $v[$arr]['srcEntryId'] = intval($row['srcEntryId']);
                $v[$arr]['srcBillNo'] = $row['srcBillNo'];
                $v[$arr]['srcId'] = intval($row['srcId']);
                $v[$arr]['goods'] = $row['invName'];
                $v[$arr]['spec'] = $row['invSpec'];
                $v[$arr]['invName'] = $row['invName'];
                $v[$arr]['qty'] = (float)abs($row['qty']);
                $v[$arr]['locationName'] = $row['locationName'];
                $v[$arr]['amount'] = (float)abs($row['amount']);
                $v[$arr]['taxAmount'] = (float)$row['taxAmount'];
                $v[$arr]['price'] = (float)$row['price'];
                $v[$arr]['tax'] = (float)$row['tax'];
                $v[$arr]['mainUnit'] = $row['mainUnit'];
                $v[$arr]['deduction'] = (float)$row['deduction'];
                $v[$arr]['invId'] = intval($row['invId']);
                $v[$arr]['invNumber'] = $row['invNumber'];
                $v[$arr]['locationId'] = intval($row['locationId']);
                $v[$arr]['locationName'] = $row['locationName'];
                $v[$arr]['discountRate'] = (float)$row['discountRate'];
                $v[$arr]['description'] = $row['description'];
                $v[$arr]['unitId'] = intval($row['unitId']);
                $v[$arr]['mainUnit'] = $row['mainUnit'];

                // 库存数量
                // 获取该商品在库数量
                $res = $this->mysql_model->query(INVOICE_INFO, "SELECT SUM(qty) as stockQty from " . INVOICE_INFO . " WHERE invId=$row[invId]");
                $v [$arr] ['stockQty'] = ($res ['stockQty'] == null) ? 0 : $res ['stockQty'];

            }

            $result['entries'] = $v;
            $result['accId'] = (float)$data['accId'];
            $accounts = $this->data_model->get_account_info('and (iid=' . $id . ') order by id');
            foreach ($accounts as $arr => $row) {
                $s[$arr]['invoiceId'] = intval($id);
                $s[$arr]['billNo'] = $row['billNo'];
                $s[$arr]['buId'] = intval($row['buId']);
                $s[$arr]['billType'] = $row['billType'];
                $s[$arr]['transType'] = $row['transType'];
                $s[$arr]['transTypeName'] = $row['transTypeName'];
                $s[$arr]['billDate'] = $row['billDate'];
                $s[$arr]['accId'] = intval($row['accId']);
                $s[$arr]['account'] = $row['accountNumber'] . ' ' . $row['accountName'];
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
