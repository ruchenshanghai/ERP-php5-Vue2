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

class AllocationList extends CI_Controller
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
        $inLocationID = $fetchConfig->inLocationID;
        $outLocationID = $fetchConfig->outLocationID;


        $data['pageIndex'] = $pageIndex;
        $data['pageSize'] = $pageSize;

        $sortKey = 'billDate';
        $sortType = 'desc';
        $order = $sortKey ? $sortKey . ' ' . $sortType : ' a.id desc';
        $transType = '103091';
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

        $info = $this->data_model->get_invoice_info($where . ' order by id');
        if ($inLocationID > 0 || $outLocationID > 0) {
            $a1 = $this->data_model->get_invoice_info($where . ' and locationId=' . $inLocationID . ' and qty>0 group by iid');
            $a2 = $this->data_model->get_invoice_info($where . ' and locationId=' . $outLocationID . ' and qty<0 group by iid');
            $a1 = count($a1) > 0 ? array_column($a1, 'iid') : array();
            $a2 = count($a2) > 0 ? array_column($a2, 'iid') : array();
            $a3 = array_intersect($a1, $a2);
            if (is_array($a3) && count($a3) > 0) {
                $id = join(',', $a3);
                $where .= ' and a.id in(' . $id . ')';
            } else {
                $where .= ' and 1<>1';
            }
        }
        // fetch data from db
        $list = $this->data_model->get_invoice($where . ' order by ' . $order . ' limit ' . $offset . ',' . $pageSize . '');
        foreach ($list as $arr => $row) {
            foreach ($info as $arr1 => $row1) {
                if ($row1['iid'] == $row['id']) {
                    if ($row1['qty'] > 0) {
                        $qty[$row['id']][] = abs($row1['qty']);
                        $mainUnit[$row['id']][] = $row1['mainUnit'];
                        $goods[$row['id']][] = $row1['invNumber'] . ' ' . $row1['invName'] . ' ' . $row1['invSpec'];
                        $inLocationName[$row['id']][] = $row1['locationName'];
                    } else {
                        $outLocationName[$row['id']][] = $row1['locationName'];
                    }
                }
            }
            $list[$arr]['id'] = intval($row['id']);
            $list[$arr]['billDate'] = $row['billDate'];
            $list[$arr]['qty'] = $qty[$row['id']];
            $list[$arr]['goods'] = $goods[$row['id']];
            $list[$arr]['mainUnit'] = $mainUnit[$row['id']];
            $list[$arr]['description'] = $row['description'];
            $list[$arr]['billNo'] = $row['billNo'];
            $list[$arr]['userName'] = $row['userName'];
            $list[$arr]['outLocationName'] = $outLocationName[$row['id']];
            $list[$arr]['inLocationName'] = $inLocationName[$row['id']];
        }
        $data['rows'] = $list;
        return $data;
    }
}
