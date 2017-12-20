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

class OtherOutbound extends CI_Controller
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
        $transTypeID = intval($fetchConfig->transTypeID);
        $locationID = intval($fetchConfig->locationID);

        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;

        $sortKey = 'billDate';
        $sortType = 'desc';
        $order = $sortKey ? $sortKey . ' ' . $sortType : ' a.id desc';

        $where = ' and a.billType="OO"';
        $where .= $keyword ? ' and (a.userName like "%' . $keyword . '%" or a.description like "%' . $keyword . '%" or a.billNo like "%' . $keyword . '%")' : '';
        $where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
        $where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
        $where .= $transTypeID > 0 ? ' and a.transType=' . $transTypeID . '' : '';

//        if ($locationId > 0) {
//            $iid = $this->mysql_model->get_results(INVOICE_INFO, '(locationId=' . $locationId . ') and billType="OO" group by iid');
//            if (is_array($a1) && count($a1) > 0) {
//                $iid = array_column($iid, 'iid');
//                $iid = join(',', $iid);
//                $where .= ' and a.id in(' . $iid . ')';
//            } else {
//                $where .= ' and 1<>1';
//            }
//        }

        $offset = $pageSize * ($pageIndex - 1);
        $data['rowsCount'] = $this->data_model->get_invoice($where, 3);
        //总条数
        $data['pageCount'] = ceil($data['totalSize'] / $pageSize);
        //总分页数


        $list = $this->data_model->get_invoice($where . ' order by ' . $order . ' limit ' . $offset . ',' . $pageSize . '');
        foreach ($list as $arr=>$row) {
            $list[$arr]['checkName']    = $row['checkName'];
            $list[$arr]['checked']      = intval($row['checked']);
            $list[$arr]['billDate']     = $row['billDate'];
            $list[$arr]['billType']     = $row['billType'];
            $list[$arr]['id']           = intval($row['id']);
            $list[$arr]['amount']       = (float)abs($row['totalAmount']);
            $list[$arr]['transType']    = intval($row['transType']);;
            $list[$arr]['contactName']  = $row['contactName'];
            $list[$arr]['description']  = $row['description'];
            $list[$arr]['billNo']       = $row['billNo'];
            $list[$arr]['totalAmount']  = (float)abs($row['totalAmount']);
            $list[$arr]['userName']     = $row['userName'];
            $list[$arr]['transTypeName']= $row['transTypeName'];

        }
        $data['rows'] = $list;

        return $data;
    }
}
