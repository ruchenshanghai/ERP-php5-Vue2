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

class PurchaseOrder extends CI_Controller
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
        $data['rowsCount'] = $this->data_model->get_order($where, 3);
        //总条数
        $data['pageCount'] = ceil($data['totalSize'] / $pageSize);
        //总分页数

        // fetch data from db
        $list = $this->data_model->get_order($where . ' order by ' . $order . ' limit ' . $offset . ',' . $pageSize . '');
        //exit(var_dump($list));
        foreach ($list as $arr => $row) {
            $list[$arr]['id'] = intval($row['id']);
            $list[$arr]['checkName'] = $row['checkName'];
            $list[$arr]['checked'] = intval($row['checked']);
            $list[$arr]['billDate'] = $row['billDate'];
            $list[$arr]['hxStateCode'] = intval($row['hxStateCode']);
            $list[$arr]['amount'] = (float)abs($row['amount']);
            $list[$arr]['transType'] = intval($row['transType']);
            $list[$arr]['rpAmount'] = (float)abs($row['rpAmount']);
            $list[$arr]['currency'] = $row['currency'];
            $list[$arr]['contactName'] = $row['contactName'];//$row['contactNo'].' '.$row['contactName'];
            $list[$arr]['description'] = $row['description'];
            $list[$arr]['billNo'] = $row['billNo'];
            $list[$arr]['totalAmount'] = (float)abs($row['totalAmount']);
            $list[$arr]['userName'] = $row['userName'];
            $list[$arr]['transTypeName'] = $row['transTypeName'];
            $list[$arr]['disEditable'] = 0;
            $list[$arr]['totalQty'] = $row['totalQty'];
            $list[$arr]['deliveryDate'] = $row['deliveryDate'];
            $list[$arr]['checkName'] = $row['checkName'];
            if ($row['billStatus'] == 2) {
                $list[$arr]['billStatusName'] = "全部入库";
            } elseif ($row['billStatus'] == 1) {
                $list[$arr]['billStatusName'] = "部分入库";
            } else {
                $list[$arr]['billStatusName'] = "未入库";
            }
        }
        $data['rows'] = $list;

        return $data;
    }

    //获取详细信息
    public function detail()
    {
//        $this->common_model->checkpurview(1);
        //$id   = intval($this->input->get_post('id',TRUE));

        $postData = json_decode(file_get_contents('php://input'));
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
        $condition = "";
        if (!empty($id)) {
            $condition .= " and (a.id=$id.)";
        }
        if (!empty($fetchConfig->billNo)) {
            $condition .= " and (a.billNo='" . $fetchConfig->billNo . "')";
        }
        $data = $this->data_model->get_order($condition . ' and billType="PUR"', 1);

        if (count($data) > 0) {
            $s = $v = array();

            $result['id'] = intval($data['id']);
            $result['buId'] = intval($data['buId']);
            $result['contactName'] = $data['contactName'];
            $result['date'] = $data['billDate'];
            $result['billNo'] = $data['billNo'];
            $result['billType'] = $data['billType'];
            $result['modifyTime'] = $data['modifyTime'];
            $result['checkName'] = $data['checkName'];
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
            $result['status'] = intval($data['checked']) == 1 ? 'view' : 'edit';    //edit
            $result['totalDiscount'] = (float)$data['totalDiscount'];
            $result['totalTax'] = (float)$data['totalTax'];
            $result['totalAmount'] = (float)abs($data['totalAmount']);
            $result['description'] = $data['description'];
            $result['orderType'] = intval($data['orderType']);
            $result['paymentMethod'] = intval($data['paymentMethod']);
            $result['shippingMethod'] = intval($data['shippingMethod']);
            $result['deliveryDate'] = $data['deliveryDate'];
            $result['currency'] = intval($data['currency']);
            $result['currencyCode'] = $data['currencyCode'];
            $result['currencyText'] = $data['currencyText'];
            $result['accId'] = $data['accId'];
            $result['locationName'] = $data['locationName'];
            $result['locationNo'] = $data['locationNo'];
            $result['locationId'] = intval($data['locationId']);

            $list = $this->data_model->get_order_info('and (iid=' . $id . ') order by id');
            foreach ($list as $arr => $row) {
                $v[$arr]['spec'] = $row['invSpec'];
                $v[$arr]['srcEntryId'] = $row['srcEntryId'];
                $v[$arr]['srcBillNo'] = $row['srcBillNo'];
                $v[$arr]['srcId'] = $row['srcId'];
                $v[$arr]['goods'] = $row['invName'];
                $v[$arr]['invName'] = $row['invNumber'];
                $v[$arr]['qty'] = (float)abs($row['qty']);
                $v[$arr]['stockQty'] = abs($row['stockQty']);
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
                $v[$arr]['description'] = $row['description'];
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
                if ($result['accId'] == $row['accId']) {
                    $result['account'] = $row['accountName'];
                }
            }
            $result['accounts'] = $s;
        }
        return $result;
    }


    public function update()
    {
        $postData = json_decode(file_get_contents("php://input"));
        $userName = $postData->userName;
        $password = $postData->password;
        $updateConfig = $postData->updateConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $response->info = $this->updateOrder($updateConfig);
        }
        echo json_encode($response);
    }

    private function updateOrder($updateConfig)
    {
//        $updateConfig = (array)$updateConfig;
        $updateConfig = (array)json_decode('{
  "id": 148,
  "buId": 11,
  "contactName": "Full Home Limited",
  "date": "2017-12-13",
  "deliveryDate": "2017-12-13",
  "locationId": 5,
  "billNo": "95ZYPO201712131924383",
  "transType": 150501,
  "entries": [
    {
      "invId": 10,
      "invNumber": "95ZYGD0010",
      "invName": "95ZYGD0010",
      "invSpec": "NT5TU64M16HG-AC",
      "skuId": -1,
      "skuName": "",
      "unitId": -1,
      "mainUnit": "PCS",
      "qty": "4000",
      "price": "1.250",
      "discountRate": "1",
      "deduction": "50.00",
      "amount": "4950.00",
      "description": "",
      "locationId": 5,
      "locationName": "香港仓库/U-Freight Logis"
    },
    {
      "invId": 64,
      "invNumber": "95ZYGD0064",
      "invName": "95ZYGD0064",
      "invSpec": "C84ME",
      "skuId": -1,
      "skuName": "",
      "unitId": -1,
      "mainUnit": "台",
      "qty": "1",
      "price": "32000.000",
      "discountRate": "2",
      "deduction": "640.00",
      "amount": "31360.00",
      "description": "",
      "locationId": 1,
      "locationName": "九五尊易上海仓库"
    },
    {
      "invId": "63",
      "invNumber": "95ZYGD0063",
      "invName": "演示背包",
      "invSpec": "小鱼",
      "skuId": -1,
      "skuName": "",
      "unitId": "0",
      "mainUnit": "个",
      "qty": "2",
      "price": "279.300",
      "discountRate": "0",
      "deduction": "0.00",
      "amount": "558.60",
      "description": "",
      "locationId": "3",
      "locationName": "北京仓库"
    }
  ],
  "totalQty": "4003",
  "totalAmount": "36868.60",
  "description": "",
  "disRate": "1",
  "disAmount": "123.75",
  "amount": "36744.85",
  "paymentMethod": 85,
  "shippingMethod": 86,
  "currency": 2,
  "accId": 3
}
');
        $updateConfig = $this->validform($updateConfig);
        return $updateConfig;
        $info = elements(array(
            'billType',
            'transType',
            'transTypeName',
            'buId',
            'billDate',
            'description',
            'totalQty',
            'amount',
            'arrears',
            'rpAmount',
            //'currency',
            'totalAmount',
            'hxStateCode',
            'totalArrears',
            'disRate',
            'disAmount',
            'uid',
            'userName',
            'accId',
            'modifyTime',
            'orderType',
            'paymentMethod',
            'shippingMethod',
            'currency',
            'locationId'
        ), $updateConfig);
        return $info;
        $this->db->trans_begin();
        $this->mysql_model->update(ORDER, $info, '(id=' . $updateConfig['id'] . ')');
        $this->order_info($updateConfig['id'], $updateConfig);
        $this->account_info($updateConfig['id'], $updateConfig);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            $this->common_model->logs('修改采购单 单据编号：' . $updateConfig['billNo']);
            return true;
        }
    }

    //公共验证
    private function validform($data)
    {
        //(float)$data['arrears'] < 0 || !is_numeric($data['arrears']) && str_alert(-1,'本次欠款要为数字，请输入有效数字！');
        //(float)$data['disRate'] < 0 || !is_numeric($data['disRate']) && str_alert(-1,'折扣率要为数字，请输入有效数字！');
        //(float)$data['rpAmount'] < 0 || !is_numeric($data['rpAmount']) && str_alert(-1,'本次收款要为数字，请输入有效数字！');
        //(float)$data['amount'] < (float)$data['rpAmount']  && str_alert(-1,'本次付款不能大于折后金额！');
        //(float)$data['amount'] < (float)$data['disAmount'] && str_alert(-1,'折扣额不能大于合计金额！');

        if (isset($data['id']) && intval($data['id']) > 0) {
            $data['id'] = intval($data['id']);
            $order = $this->mysql_model->get_row(ORDER, '(id=' . $data['id'] . ') and billType="PUR" and isDelete=0');  //修改的时候判断
            if (count($order) < 1) {
                return '单据不存在、或者已删除';
            }
            //jason.xie 暂时删除
            //$invoice['checked']>0 && str_alert(-1,'审核后不可修改');
            $data['billNo'] = $order['billNo'];
        } else {
            $data['billNo'] = str_no('PO');    //修改的时候屏蔽
        }

        $data['billType'] = 'PUR';
        $data['transType'] = intval($data['transType']);
        $data['transTypeName'] = $data['transType'] == 150501 ? '采购' : '退货';
        $data['buId'] = intval($data['buId']);
        $data['billDate'] = $data['date'];
        $data['description'] = $data['description'];
        $data['totalQty'] = (float)$data['totalQty'];
        if ($data['transType'] == 150501) {
            $data['amount'] = abs($data['amount']);
            $data['arrears'] = abs($data['arrears']);
            $data['rpAmount'] = abs($data['rpAmount']);
            $data['totalAmount'] = abs($data['totalAmount']);
        } else {
            $data['amount'] = -abs($data['amount']);
            $data['arrears'] = -abs($data['arrears']);
            $data['rpAmount'] = -abs($data['rpAmount']);
            $data['totalAmount'] = -abs($data['totalAmount']);
        }
        //exit(print_r($this->jxcsys));
        $data['hxStateCode'] = $data['rpAmount'] == $data['amount'] ? 2 : ($data['rpAmount'] > 0 ? 1 : 0);
        $data['totalArrears'] = (float)$data['totalArrears'];
        $data['disRate'] = (float)$data['disRate'];
        $data['disAmount'] = (float)$data['disAmount'];
//        $data['uid'] = $this->jxcsys['uid'];
//        $data['userName'] = $this->jxcsys['name'];
        $data['accId'] = (float)$data['accId'];
        return $data;

        $data['modifyTime'] = date('Y-m-d H:i:s');

        //选择了结算账户 需要验证
        if (isset($data['accounts']) && count($data['accounts']) > 0) {
            foreach ($data['accounts'] as $arr => $row) {
                if ((float)$row['payment'] < 0 || !is_numeric($row['payment'])) {
                    return '结算金额要为数字，请输入有效数字！';
                }
            }
        }

        //供应商验证
        if ($this->mysql_model->get_count(CONTACT, '(id=' . intval($data['buId']) . ')') < 1) {
            return '采购单位不存在';
        }

        //商品录入验证
        if (is_array($data['entries'])) {
            $system = $this->common_model->get_option('system');
            if ($system['requiredCheckStore'] == 1) {  //开启检查时判断
                $item = array();
                //exit(print_r($data['entries']));
                foreach ($data['entries'] as $k => $v) {
                    if (!isset($v['invId'])) {
                        return '参数错误';
                    }
                    if (!isset($v['locationId'])) {
                        return '参数错误';
                    }
                    if (!isset($item[$v['invId'] . '-' . $v['locationId']])) {
                        $item[$v['invId'] . '-' . $v['locationId']] = $v;
                    } else {
                        $item[$v['invId'] . '-' . $v['locationId']]['qty'] += $v['qty'];        //同一仓库 同一商品 数量累加
                    }
                }
                $inventory = $this->data_model->get_invoice_info_inventory();
            } else {
                $item = $data['entries'];
            }
            $storage = array_column($this->mysql_model->get_results(STORAGE, '(disable=0)'), 'id');
            //exit(print_r($item));


            foreach ($item as $arr => $row) {
                $row = (array) $row;
                if (!isset($row['invId'])) {
                    return '参数错误';
                }
                if (!isset($row['locationId'])) {
                    return '参数错误';
                }
                if ((float)$row['qty'] < 0 || !is_numeric($row['qty'])) {
                    return '商品数量要为数字，请输入有效数字！';
                }
                if ((float)$row['price'] < 0 || !is_numeric($row['price'])) {
                    return '商品销售单价要为数字，请输入有效数字！';
                }
                if ((float)$row['discountRate'] < 0 || !is_numeric($row['discountRate'])) {
                    return '折扣率要为数字，请输入有效数字！';
                }
                if (intval($row['locationId']) < 1) {
                    return '请选择相应的仓库！';
                }
                if (!in_array(intval($row['locationId']), $storage)) {
                    return $row['locationName'] . '不存在或不可用！';
                }
                //库存判断
                if ($system['requiredCheckStore'] == 1) {
                    if (intval($data['transType']) == 150502) {                        //退货才验证
                        if (isset($inventory[$row['invId']][$row['locationId']])) {
                            if ($inventory[$row['invId']][$row['locationId']] < (float)$row['qty']) {
                                return $row['locationName'] . $row['invName'] . '商品库存不足！';
                            }
                        } else {
                            return $row['invName'] . '库存不足！';
                        }
                    }
                }
            }
        } else {
            return '提交的是空数据';
        }
        return $data;
    }
}
