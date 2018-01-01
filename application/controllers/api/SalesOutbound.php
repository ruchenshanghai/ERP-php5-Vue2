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

class SalesOutbound extends CI_Controller
{

    private $billType = "'SALE'";
    private $transType = 150601;
    private $transTypeName = "销售出库";

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
        $transType = '150601';

        $where = ' and a.billType="SALE"';
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
                $saleInfo = $this->mysql_model->get_row(SALE, '(id=' . $row ['srcId'] . ')');
                $list [$arr] ['saleQty'] = $saleInfo ['totalQty'];
                if (count($saleInfo) > 0) {
                    if ($saleInfo ['billStatus'] == 2) {
                        $list [$arr] ['billStatus'] = "全部发货";
                    } elseif ($saleInfo ['billStatus'] == 1) {
                        $list [$arr] ['billStatus'] = "部分发货";
                    } else {
                        $list [$arr] ['billStatus'] = "未发货";
                    }
                    $list[$arr]['srcBillNo'] = $saleInfo['billNo'];
                    $list[$arr]['srcId'] = $saleInfo['id'];
                }
            }
        }

        $data['rows'] = $list;
        return $data;
    }


    public function detail()
    {

        $postData = json_decode(file_get_contents('php://input'));
        $userName = $postData->userName;
        $password = $postData->password;
        $fetchConfig = $postData->fetchConfig;
        $response = new ResponseMessage();

        $user = $this->mysql_model->checkUserPwd($userName, $password);
        if ($user != null) {
            $response->status = true;
            $fetchData = $this->detailOutbound($fetchConfig);
            $response->info = $fetchData;
        }
        echo json_encode($response);
    }


    private function detailOutbound($fetchConfig)
    {
        $data = null;
        $result = null;

        $id = $fetchConfig->id;
        $condition = "";
        if (!empty ($id)) {
            $condition .= " and (a.id=$id)";
        }
        $data = $this->data_model->get_invoice($condition . " and billType=$this->billType", 1);
        if (count($data) > 0) {
            $s = $v = array();
            $result['billNo'] = $data ['billNo'];
            $result['billType'] = $data ['billType'];
            $result['modifyTime'] = $data ['modifyTime'];
            $result['userName'] = $data ['userName'];
            $result['date'] = $data ['billDate'];
            $result['description'] = $data ['description'];
            $result['status'] = intval($data['checked']) == 1 ? 'view' : 'edit';
            $result['checked'] = intval($data['checked']);
            $result['totalQty'] = $data['totalQty'];

            $list = $this->data_model->get_invoice_info('and (iid=' . $id . ') order by id');
            foreach ($list as $arr => $row) {
                $v [$arr] ['siid'] = $row ['id'];
                $v [$arr] ['goods'] = $row ['invName'];
                $v [$arr] ['spec'] = $row ['invSpec'];
                $v [$arr] ['mainUnit'] = $row ['mainUnit'];
                $v[$arr]['locationId'] = intval($row['locationId']);
                $v[$arr]['locationName'] = $row['locationName'];
                $saleInfo = $this->mysql_model->get_row(SALE_INFO, '(id=' . $row ['srcId'] . ')');
                $v [$arr] ['qty'] = abs($saleInfo ['qty']);
                $v [$arr] ['outQty'] = abs($saleInfo ['outQty']);
                $v [$arr] ['unOutQty'] = $v [$arr] ['qty'] - $v [$arr] ['outQty'];
                $v [$arr] ['outingQty'] = abs($row ['qty']);
                $v [$arr] ['description'] = $row ['description'];
                $res = $this->mysql_model->query(INVOICE_INFO, "SELECT SUM(qty) as stockQty from " . INVOICE_INFO . " WHERE invId=$row[invId]");
                $v [$arr] ['stockQty'] = $res ['stockQty'];
            }

            $result['entries'] = $v;
            $result['accId'] = ( float )$data ['accId'];
            $accounts = $this->data_model->get_account_info('and (iid=' . $id . ') order by id');
            foreach ($accounts as $arr => $row) {
                $s [$arr] ['saleId'] = intval($id);
                $s [$arr] ['billNo'] = $row ['billNo'];
                $s [$arr] ['buId'] = intval($row ['buId']);
                $s [$arr] ['billType'] = $row ['billType'];
                $s [$arr] ['transType'] = $row ['transType'];
                $s [$arr] ['transTypeName'] = $row ['transTypeName'];
                $s [$arr] ['billDate'] = $row ['billDate'];
                $s [$arr] ['accId'] = intval($row ['accId']);
                $s [$arr] ['account'] = $row ['accountNumber'] . '' . $row ['accountName'];
                $s [$arr] ['payment'] = ( float )abs($row ['payment']);
                $s [$arr] ['wayId'] = ( float )$row ['wayId'];
                $s [$arr] ['way'] = $row ['categoryName'];
                $s [$arr] ['settlement'] = $row ['settlement'];
            }
            $result['accounts'] = $s;
        }
        return $result;
    }
}
