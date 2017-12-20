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
}
