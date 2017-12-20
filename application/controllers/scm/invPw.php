<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//采购入库、出库
class InvPw extends CI_Controller
{
    private $billType = "'PUR'";
    private $transType = 150501;
    private $transTypeName = "采购入库";

    public function __construct()
    {
        parent::__construct();
        $this->common_model->checkpurview();
        $this->jxcsys = $this->session->userdata('jxcsys');
    }

    public function index()
    {
        $action = $this->input->get('action', TRUE);
        switch ($action) {
            case 'initPw':
                $this->common_model->checkpurview(2);
                $this->load->view('scm/invPw/initPw');
                break;
            case 'editPw':
                $this->common_model->checkpurview(1);
                $this->load->view('scm/invPw/initPw');
                break;
            case 'initPwList':
                $this->common_model->checkpurview(1);
                $this->load->view('scm/invPw/initPwList');
                break;
            case 'unStorageList':
                $this->unStorageList();
                break;
            case 'list':
                $this->common_model->checkpurview(1);
                $this->pwList();
                break;
            default:
                $this->common_model->checkpurview(1);
                $this->pwList();
        }
    }

    public function pwList()
    {
        $v = array();
        $data['status'] = 200;
        $data['msg'] = 'success';
        $page = max(intval($this->input->get_post('page', TRUE)), 1);
        $rows = max(intval($this->input->get_post('rows', TRUE)), 100);
        $sidx = str_enhtml($this->input->get_post('sidx', TRUE));
        $sord = str_enhtml($this->input->get_post('sord', TRUE));
        $transType = intval($this->input->get_post('transType', TRUE));
        $matchCon = str_enhtml($this->input->get_post('matchCon', TRUE));
        $beginDate = str_enhtml($this->input->get_post('beginDate', TRUE));
        $endDate = str_enhtml($this->input->get_post('endDate', TRUE));
        $order = $sidx ? $sidx . ' ' . $sord : ' a.id desc';
        $where = ' and a.billType="PUR"';
        $where .= $transType > 0 ? ' and a.transType=' . $transType : '';
        $where .= $matchCon ? ' and (b.name like "%' . $matchCon . '%" or description like "%' . $matchCon . '%" or billNo like "%' . $matchCon . '%")' : '';
        $where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
        $where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
        $offset = $rows * ($page - 1);
        $data['data']['page'] = $page;
        $data['data']['records'] = $this->data_model->get_invoice($where, 3);                             //总条数
        $data['data']['total'] = ceil($data['data']['records'] / $rows);                                 //总分页数
        $list = $this->data_model->get_invoice($where . ' order by ' . $order . ' limit ' . $offset . ',' . $rows . '');
        foreach ($list as $arr => $row) {
            $v[$arr]['id'] = intval($row['id']);
            $v[$arr]['checkName'] = $row['checkName'];
            $v[$arr]['checked'] = intval($row['checked']);
            $v[$arr]['billDate'] = $row['billDate'];
            $v[$arr]['contactName'] = $row['contactName'];//$row['contactNo'].' '.$row['contactName'];
            $v[$arr]['description'] = $row['description'];
            $v[$arr]['billNo'] = $row['billNo'];
            $v[$arr]['userName'] = $row['userName'];
            $v[$arr]['locationName'] = $row['locationName'];
            $v[$arr]['disEditable'] = 0;
            $v[$arr]['totalQty'] = $row['totalQty'];
            if (!empty($row['srcId'])) {
                $orderInfo = $this->mysql_model->get_row(ORDER, '(id=' . $row['srcId'] . ')');
                if (count($orderInfo) > 0) {
                    $v[$arr]['puQty'] = $orderInfo['totalQty'];
                    if ($orderInfo['billStatus'] == 2) {
                        $v[$arr]['billStatus'] = "全部入库";
                    } elseif ($orderInfo['billStatus'] == 1) {
                        $v[$arr]['billStatus'] = "部分入库";
                    } else {
                        $v[$arr]['billStatus'] = "未入库";
                    }
                    $v[$arr]['srcBillNo'] = $orderInfo['billNo'];
                    $v[$arr]['srcId'] = $orderInfo['id'];
                }
            }
            //exit(print_r($v[$arr]));
        }
        $data['data']['rows'] = $v;
        die(json_encode($data));
    }

    //导出
    public function exportInvPw()
    {
        $this->common_model->checkpurview(5);
        $name = 'pw_record_' . date('YmdHis') . '.xls';
        sys_csv($name);
        $this->common_model->logs('导出采购入库单据:' . $name);
        $sidx = str_enhtml($this->input->get_post('sidx', TRUE));
        $sord = str_enhtml($this->input->get_post('sord', TRUE));
        $transType = intval($this->input->get_post('transType', TRUE));
        $matchCon = str_enhtml($this->input->get_post('matchCon', TRUE));
        $beginDate = str_enhtml($this->input->get_post('beginDate', TRUE));
        $endDate = str_enhtml($this->input->get_post('endDate', TRUE));
        $order = $sidx ? $sidx . ' ' . $sord : ' a.id desc';
        $where = ' and a.billType="PUR"';
        $where .= $transType > 0 ? ' and a.transType=' . $transType : '';
        $where .= $matchCon ? ' and (b.name like "%' . $matchCon . '%" or description like "%' . $matchCon . '%" or billNo like "%' . $matchCon . '%")' : '';
        $where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
        $where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
        $where1 = ' and a.billType="PUR"';
        $where1 .= $transType > 0 ? ' and a.transType=' . $transType : '';
        $where1 .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
        $where1 .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
        $data['list1'] = $this->data_model->get_invoice($where . ' order by ' . $order . '');
        $data['list2'] = $this->data_model->get_invoice_info($where1 . ' order by a.billDate');
        $this->load->view('scm/invPw/exportInvPw', $data);
    }

    //新增
    public function add()
    {
        $this->common_model->checkpurview(2);
        $data = $this->input->post('postData', TRUE);
        if (strlen($data) > 0) {
            $data = (array)json_decode($data, true);
            $order = $this->mysql_model->get_row(ORDER, '(id=' . $data['id'] . ')');  //修改的时候判断
            count($order) < 1 && str_alert(-1, '单据不存在、或者已删除');
            if ($order['billStatus'] == 2) {
                //已经全部入库
                str_alert(200, 'success', '已经全部入库');
            }

            //$invoice = $this->mysql_model->get_row(INVOICE,'(srcId='.$data['id'].')');
            $this->db->trans_begin();
            //if(count($invoice)==0)
            //每次都插入新数据
            {
                //插入新数据
                $invoice = elements(array(
                    'buId',
                    'uid',
                    'transType',
                    'transTypeName',
                    'billStatus',
                    'totalAmount',
                    'amount',
                    'rpAmount',
                    'arrears',
                    'disRate',
                    'disAmount',
                    'totalQty',
                    'totalArrears',
                    'checkName',
                    'totalTax',
                    'totalTaxAmount',
                    'accId',
                    'billType',
                    'hxStateCode',
                    'totalDiscount',
                    'salesId',
                    'customerFree',
                    'hxAmount',
                    'hasCheck',
                    'notCheck',
                    'nowCheck',
                    'payment',
                    'discount',
                    'locationId',
                    'isDelete',
                    'deliveryDate'
                ), $order);
                $invoice['srcId'] = $order['id'];
                $invoice['srcBillNo'] = $order['billNo'];
                $invoice['billDate'] = $data['date'];
                $invoice['description'] = $data['description'];
                $invoice['billNo'] = $data['billNo'];
                $invoice['checked'] = 0;
                $invoice['checkName'] = "";
                $invoice['userName'] = $data['userName'];
                //invoice总是入库完成
                $invoice['billStatus'] = 2;
                $iid = $this->mysql_model->insert(INVOICE, $invoice);
            }
// 		    else 
// 		    {
// 		    	$iid = $invoice['id'];
// 		    }
            //exit("iid:".$iid);
            $status = $this->invoice_info($iid, $data);
            //更新采购数据库order
            $info = array('billStatus' => $status['billStatus']);
            $ret = $this->mysql_model->update(ORDER, $info, '(id=' . $data['id'] . ')');
            //更新数据库invoice
            $info = array('totalQty' => $status['stockinSum']);
            $ret = $this->mysql_model->update(INVOICE, $info, '(id=' . $iid . ')');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $sql = $this->db->last_query();
                str_alert(-1, $sql);
                //str_alert(-1,'SQL错误');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('新增采购 入库单据编号：' . $iid);
                str_alert(200, 'success', array('id' => intval($iid)));
            }
        }
        str_alert(-1, '提交的是空数据');
    }

    //新增
    public function addnew()
    {
        $this->add();
    }

    //修改保存
    public function updateInvPw()
    {
        $this->common_model->checkpurview(3);
        $data = $this->input->post('postData', TRUE);
        if (strlen($data) > 0) {
            $data = json_decode($data, true);
            $ret = $this->mysql_model->get_row(INVOICE, "(billNo='$data[billNo]')");
            //exit(print_r($ret));
            $invoiceID = $ret['id'];
            $orderID = $ret['srcId'];
            $oldBillStatus = $ret['billStatus'];

            $this->db->trans_begin();
            $status = $this->invoice_info($invoiceID, $data);
            //更新采购数据库order
            $info = array('billStatus' => $status['billStatus']);
            $ret = $this->mysql_model->update(ORDER, $info, '(id=' . $orderID . ')');
            if ($ret == false) {
                exit("updateInvPw更新出错1" . '(id=' . $orderID . ')');
            }

            $info = array('modifyTime' => $data['date'], 'totalQty' => $status['stockinSum']);
            $ret = $this->mysql_model->update(INVOICE, $info, '(id=' . $invoiceID . ')');
            if ($ret == false) {
                exit("updateInvPw更新出错1");
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, 'SQL错误');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('修改采购入库单 单据编号：' . $data['billNo']);
                str_alert(200, 'success', array('id' => $data['id']));
            }
        }
        str_alert(-1, '提交的数据不能为空');
    }

    //获取未全部入库的订单编号
    public function unStorageList()
    {
        $this->common_model->checkpurview(1);
        $sql = "select billNo,userName,billDate from " . ORDER .
            " where (isDelete=0) and billType='PUR' and transType='150501' and billStatus!=2 and checked=1 order  by id desc";
        $result = $this->mysql_model->query(ORDER, $sql, 2);
        foreach ($result as $row) {
            $new[] = array('billNo' => $row['billNo'], 'userName' => $row['userName'], 'billDate' => $row['billDate']);
        }
        exit(json_encode($new));
    }

    //获取未完成入库订单信息
    public function order()
    {
        $this->common_model->checkpurview(1);
        //$id   = intval($this->input->get_post('id',TRUE));

        $id = intval($_REQUEST['id']);
        $condition = "";
        if (!empty($id)) {
            $condition .= " and (a.id=$id)";
        }
        if (!empty($_REQUEST['billNo'])) {
            $condition .= " and (a.billNo='$_REQUEST[billNo]')";
        }
        $data = $this->data_model->get_order($condition . ' and billType="PUR"', 1);
        if (count($data) > 0) {
            $s = $v = array();
            $info['status'] = 200;
            $info['msg'] = 'success';
            $id = $info['data']['id'] = intval($data['id']);
            $info['data']['buId'] = intval($data['buId']);
            $info['data']['contactName'] = $data['contactName'];
            $info['data']['date'] = $data['billDate'];
            $info['data']['billNo'] = $data['billNo'];
            $info['data']['billType'] = $data['billType'];
            $info['data']['modifyTime'] = $data['modifyTime'];
            $info['data']['transType'] = intval($data['transType']);
            $info['data']['totalQty'] = (float)$data['totalQty'];
            $info['data']['totalTaxAmount'] = (float)$data['totalTaxAmount'];
            $info['data']['billStatus'] = intval($data['billStatus']);
            $info['data']['disRate'] = (float)$data['disRate'];
            $info['data']['disAmount'] = (float)$data['disAmount'];
            $info['data']['amount'] = (float)abs($data['amount']);
            $info['data']['rpAmount'] = (float)abs($data['rpAmount']);
            $info['data']['arrears'] = (float)abs($data['arrears']);
            $info['data']['userName'] = $data['userName'];


            $invoice = $this->mysql_model->get_row(INVOICE, '(srcId=' . $id . ')');
            if (count($invoice) > 0) {
                $info['data']['checkName'] = $invoice['checkName'];
                $info['data']['checked'] = intval($invoice['checked']);
                $info['data']['status'] = intval($invoice['checked']) == 1 ? 'view' : 'edit';
            } else {
                $info['data']['checkName'] = "";
                $info['data']['checked'] = 0;
                $info['data']['status'] = 'edit';
            }
            $info['data']['totalDiscount'] = (float)$data['totalDiscount'];
            $info['data']['totalTax'] = (float)$data['totalTax'];
            $info['data']['totalAmount'] = (float)abs($data['totalAmount']);
            //$info['data']['description']        = $data['description'];
            $list = $this->data_model->get_order_info('and (iid=' . $id . ') order by id');
            //exit(print_r($list));
            foreach ($list as $arr => $row) {
                //order info id
                $v[$arr]['srcId'] = $row['id'];
                $v[$arr]['spec'] = $row['invSpec'];
                $v[$arr]['srcEntryId'] = $row['srcEntryId'];
                $v[$arr]['srcBillNo'] = $row['srcBillNo'];
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
                //$v[$arr]['description']         = $row['description'];
                $v[$arr]['skuId'] = intval($row['skuId']);
                $v[$arr]['skuName'] = '';
            }
            $info['data']['entries'] = $v;
            $info['data']['accId'] = (float)$data['accId'];
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
            $info['data']['accounts'] = $s;
            //exit(print_r($info));
            die(json_encode($info));
        }
        str_alert(-1, '单据不存在、或者已删除');
    }

    //获取采购入库订单信息
    public function getPurchaseWarehouse()
    {
        $this->common_model->checkpurview(1);
        //$id   = intval($this->input->get_post('id',TRUE));

        $id = intval($_REQUEST['id']);
        $condition = "";
        if (!empty($id)) {
            $condition .= " and (a.id=$id)";
        }
        if (!empty($_REQUEST['billNo'])) {
            $condition .= " and (a.billNo='$_REQUEST[billNo]')";
        }
        $data = $this->data_model->get_invoice($condition . ' and billType="PUR"', 1);
        if (count($data) > 0) {
            $s = $v = array();
            $info['status'] = 200;
            $info['msg'] = 'success';
            $id = $info['data']['id'] = intval($data['id']);
            $info['data']['buId'] = intval($data['buId']);
            $info['data']['contactName'] = $data['contactName'];
            $info['data']['date'] = $data['billDate'];
            $info['data']['billNo'] = $data['billNo'];
            $info['data']['billType'] = $data['billType'];
            $info['data']['modifyTime'] = $data['modifyTime'];
            $info['data']['transType'] = intval($data['transType']);
            $info['data']['totalQty'] = (float)$data['totalQty'];
            $info['data']['totalTaxAmount'] = (float)$data['totalTaxAmount'];
            $info['data']['billStatus'] = intval($data['billStatus']);
            $info['data']['disRate'] = (float)$data['disRate'];
            $info['data']['disAmount'] = (float)$data['disAmount'];
            $info['data']['amount'] = (float)abs($data['amount']);
            $info['data']['rpAmount'] = (float)abs($data['rpAmount']);
            $info['data']['arrears'] = (float)abs($data['arrears']);
            $info['data']['userName'] = $data['userName'];
            $info['data']['checked'] = intval($data['checked']);
            $info['data']['status'] = intval($data['checked']) == 1 ? 'view' : 'edit';

            $info['data']['totalDiscount'] = (float)$data['totalDiscount'];
            $info['data']['totalTax'] = (float)$data['totalTax'];
            $info['data']['totalAmount'] = (float)abs($data['totalAmount']);
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
            $info['data']['entries'] = $v;
            $info['data']['accId'] = (float)$data['accId'];
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
            $info['data']['accounts'] = $s;
            die(json_encode($info));
        }
        str_alert(-1, '单据不存在、或者已删除');
    }

    //打印
    public function toPdf()
    {
        //Array ( [sidx] => [sord] => asc [op] => 2 [matchCon] => [transType] => 150501 [beginDate] => 2017-09-01 [endDate] => 2017-09-20 [marginLeft] => ) 1
        $this->common_model->checkpurview(85);
        $id = intval($this->input->get('id', TRUE));
        if (empty($id)) {   
            //list
            $sidx = str_enhtml($this->input->get_post('sidx', TRUE));
            $sord = str_enhtml($this->input->get_post('sord', TRUE));
            $transType = intval($this->input->get_post('transType', TRUE));
            $matchCon = str_enhtml($this->input->get_post('matchCon', TRUE));
            $beginDate = str_enhtml($this->input->get_post('beginDate', TRUE));
            $endDate = str_enhtml($this->input->get_post('endDate', TRUE));
            $order = $sidx ? $sidx . ' ' . $sord : ' a.id desc';
            $where = ' and a.billType="PUR"';
            $where .= $transType > 0 ? ' and a.transType=' . $transType : '';
            $where .= $matchCon ? ' and (b.name like "%' . $matchCon . '%" or description like "%' . $matchCon . '%" or billNo like "%' . $matchCon . '%")' : '';
            $where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
            $where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
            $where1 = ' and a.billType="PUR"';
            $where1 .= $transType > 0 ? ' and a.transType=' . $transType : '';
            $where1 .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
            $where1 .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
            $data['list1'] = $this->data_model->get_invoice($where . ' order by ' . $order . '');
            $data['list2'] = $this->data_model->get_invoice_info($where1 . ' order by a.billDate');
            if (count($data) > 0) {
                ob_start();
                $this->load->view('scm/invPw/listToPdf', $data);
                $content = ob_get_clean();

//	    		require_once('./application/libraries/html2pdf/html2pdf.php');
//				try {
//					$html2pdf = new HTML2PDF('L', 'A4', 'tr');
//					$html2pdf->setDefaultFont('javiergb');
//					$html2pdf->pdf->SetDisplayMode('fullpage');
//					$html2pdf->writeHTML($content, '');
//					$html2pdf->Output('invPw_'.date('ymdHis').'.pdf','I');
//				}catch(HTML2PDF_exception $e) {
//					echo $e;
//					exit;
//				}
                require_once('./application/libraries/mpdf/mpdf.php');
                try {
                    $mpdf = new mPDF('UTF-8', 'A4', '8', '', 10, 10, 10, 10, 10, 10, 'l');
                    $mpdf->displayDefaultOrientation = true;
                    $mpdf->useAdobeCJK = true;
                    $mpdf->SetAutoFont(AUTOFONT_ALL);
                    $mpdf->SetDisplayMode('fullpage');
                    //$mpdf->SetAutoFont();
                    $mpdf->WriteHTML($content);
                    $mpdf->Output('invPwList_' . date('ymdHis') . '.pdf', 'I');
                } catch (HTML2PDF_exception $e) {
                    echo $e;
                    exit;
                }
            } else {
                str_alert(-1, '单据不存在、或者已删除');
            }
        } else {
            //item
            $data = $this->data_model->get_invoice('and (a.id=' . $id . ') and billType="PUR"', 1);
            if (count($data) > 0) {
                $list = $this->data_model->get_invoice_info('and (iid=' . $id . ') order by id');
                $data['num'] = 8;
                $data['system'] = $this->common_model->get_option('system');
                $data['countpage'] = ceil(count($list) / $data['num']);
                foreach ($list as $arr => $row) {
                    $orderInfo = $this->mysql_model->get_row(ORDER_INFO, '(id=' . $row['srcId'] . ')');
                    $data['list'][] = array(
                        'i' => $arr + 1,
                        'goods' => $row['invName'],
                        'invSpec' => $row['invSpec'],
                        'unitName' => $row['mainUnit'],
                        'qty' => abs($orderInfo['qty']),
                        'stockQty' => abs($orderInfo['stockQty'] - $row['qty']),
                        'stockinQty' => abs($row['qty']),
                        'locationName' => $row['locationName'],
                        'description' => $row['description']
                    );
                }
                ob_start();
                $this->load->view('scm/invPw/toPdf', $data);
                $content = ob_get_clean();

//                require_once('./application/libraries/html2pdf/html2pdf.php');
//                try {
//                    $html2pdf = new HTML2PDF('L', 'A4', 'tr');
//                    $html2pdf->setDefaultFont('javiergb');
//                    $html2pdf->pdf->SetDisplayMode('fullpage');
//                    $html2pdf->writeHTML($content, '');
//                    $html2pdf->Output('invPw_' . date('ymdHis') . '.pdf', 'I');
//                } catch (HTML2PDF_exception $e) {
//                    echo $e;
//                    exit;
//                }
                require_once('./application/libraries/mpdf/mpdf.php');
                try {
                    $mpdf = new mPDF('UTF-8', 'A4', '8', '', 10, 10, 10, 10, 10, 10, 'l');
                    $mpdf->displayDefaultOrientation = true;
                    $mpdf->useAdobeCJK = true;
                    $mpdf->SetAutoFont(AUTOFONT_ALL);
                    $mpdf->SetDisplayMode('fullpage');
                    //$mpdf->SetAutoFont();
                    $mpdf->WriteHTML($content);
                    $mpdf->Output('invPw_' . date('ymdHis') . '.pdf', 'I');
                } catch (HTML2PDF_exception $e) {
                    echo $e;
                    exit;
                }
            } else {
                str_alert(-1, '单据不存在、或者已删除');
            }
        }
    }

    //购购单删除
    public function delete()
    {
        $this->common_model->checkpurview(4);
        $id = intval($this->input->get('id', TRUE));
        $data = $this->mysql_model->get_row(INVOICE, '(id=' . $id . ') and billType="PUR"');
        if (count($data) > 0) {
            $data['checked'] > 0 && str_alert(-1, '已审核的不可删除');
            $info['isDelete'] = 1;
            $this->db->trans_begin();
            $this->mysql_model->update(INVOICE, $info, '(id=' . $id . ')');
            $this->mysql_model->update(INVOICE_INFO, $info, '(iid=' . $id . ')');
            $this->mysql_model->update(ACCOUNT_INFO, $info, '(iid=' . $id . ')');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, '删除失败');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('删除采购入库订单 单据编号：' . $data['billNo']);
                str_alert(200, 'success');
            }
        }
        str_alert(-1, '单据不存在、或者已删除');
    }

    //批量审核
    public function batchCheckInvPw()
    {
        $this->common_model->checkpurview(86);
        $id = str_enhtml($this->input->post('id', TRUE));
        $data = $this->mysql_model->get_results(INVOICE, '(id in(' . $id . ')) and billType="PUR" and checked=0 and (isDelete=0)');
        if (count($data) > 0) {
            $info['checked'] = 1;
            $info['checkName'] = $this->jxcsys['name'];
            $this->db->trans_begin();
            $this->mysql_model->update(INVOICE, $info, '(id in(' . $id . '))');
            $this->mysql_model->update(INVOICE_INFO, $info, '(id in(' . $id . '))');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, '审核失败');
            } else {
                $this->db->trans_commit();
                $billno = array_column($data, 'billNo');
                $billno = join(',', $billno);
                $this->common_model->logs('采购入库单编号：' . $billno . '的单据已被审核！');
                str_alert(200, '订单编号：' . $billno . '的单据已被审核！');
            }
        }
        str_alert(-1, '所选的单据都已被审核，请选择未审核的单据进行审核！');
    }

    //批量反审核
    public function rsBatchCheckInvPw()
    {
        $this->common_model->checkpurview(87);
        $id = str_enhtml($this->input->post('id', TRUE));
        $data = $this->mysql_model->get_results(INVOICE, '(id in(' . $id . ')) and billType="PUR" and checked=1 and (isDelete=0)');
        if (count($data) > 0) {
            $info['checked'] = 0;
            $info['checkName'] = '';
            $this->db->trans_begin();
            $this->mysql_model->update(INVOICE, $info, '(id in(' . $id . '))');
            $this->mysql_model->update(INVOICE_INFO, $info, '(id in(' . $id . '))');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, '反审核失败');
            } else {
                $this->db->trans_commit();
                $billno = array_column($data, 'billNo', 'id');
                $billno = join(',', $billno);
                $this->common_model->logs('采购入库单号：' . $billno . '的单据已被反审核！');
                str_alert(200, '订单编号：' . $billno . '的单据已被反审核！');
            }
        }
        str_alert(-1, '所选的订单都是未审核，请选择已审核的订单进行反审核！');
    }


    //单个审核
    public function checkInvPw()
    {
        $this->common_model->checkpurview(86);
        $data = $this->input->post('postData', TRUE);
        if (strlen($data) > 0) {
            $data = (array)json_decode($data, true);

            $id = intval($data['id']);
            if ($id <= 0) {
                str_alert(-1, '数据还未保存' . $data['id']);
            }
            //$data = $this->validform($data);
            $data['checked'] = 1;
            $data['checkName'] = $this->jxcsys['name'];
            $info = elements(array(
                'checked',
                'checkName'
            ), $data);
            $this->db->trans_begin();
            $this->mysql_model->update(INVOICE, $info, '(id=' . $id . ')');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, 'SQL错误');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('采购入库单 单据编号：' . $data['billNo'] . '的单据已被审核！');
                str_alert(200, 'success', array('id' => $id));
            }
        }
        str_alert(-1, '提交的数据不能为空');
    }


    //单个反审核
    public function revsCheckInvPw()
    {
        $this->common_model->checkpurview(87);
        $data = $this->input->post('postData', TRUE);
        if (strlen($data) > 0) {
            $data = (array)json_decode($data, true);
            $id = intval($data['id']);
            //$data = $this->validform($data);
            $data['checked'] = 0;
            $data['checkName'] = '';
            $info = elements(array(
                'checked',
                'checkName'
            ), $data);
            $this->db->trans_begin();
            $this->mysql_model->update(INVOICE, $info, '(id=' . $id . ')');
            //$this->invoice_info($id,$data);
            //$this->account_info($id,$data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, 'SQL错误');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('采购入库单 单据编号：' . $data['billNo'] . '的单据已被反审核！');
                str_alert(200, 'success', array('id' => $id));
            }
        }
        str_alert(-1, '提交的数据不能为空');
    }

    //公共验证
    private function validform($data)
    {

        if (isset($data['id']) && intval($data['id']) > 0) {
            $data['id'] = intval($data['id']);
            $invoice = $this->mysql_model->get_row(INVOICE, '(id=' . $data['id'] . ') and billType="PUR" and isDelete=0');  //修改的时候判断
            count($invoice) < 1 && str_alert(-1, '单据不存在、或者已删除');
            //jason.xie 暂时删除
            //$invoice['checked']>0 && str_alert(-1,'审核后不可修改');
            $data['billNo'] = $invoice['billNo'];
        } else {
            $data['billNo'] = str_no('PI');    //修改的时候屏蔽
        }

        $data['billType'] = 'PUR';
        $data['transType'] = intval($data['transType']);
        $data['transTypeName'] = $data['transType'] == $this->transType ? '采购入库' : '退货';
        $data['buId'] = intval($data['buId']);
        $data['billDate'] = $data['date'];
        $data['description'] = $data['description'];
        $data['totalQty'] = (float)$data['totalQty'];
        if ($data['transType'] == $this->transType) {
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
        $data['hxStateCode'] = $data['rpAmount'] == $data['amount'] ? 2 : ($data['rpAmount'] > 0 ? 1 : 0);
        $data['totalArrears'] = (float)$data['totalArrears'];
        $data['disRate'] = (float)$data['disRate'];
        $data['disAmount'] = (float)$data['disAmount'];
        $data['uid'] = $this->jxcsys['uid'];
        $data['userName'] = $this->jxcsys['name'];
        $data['accId'] = (float)$data['accId'];

        $data['modifyTime'] = date('Y-m-d H:i:s');

        //选择了结算账户 需要验证
        if (isset($data['accounts']) && count($data['accounts']) > 0) {
            foreach ($data['accounts'] as $arr => $row) {
                (float)$row['payment'] < 0 || !is_numeric($row['payment']) && str_alert(-1, '结算金额要为数字，请输入有效数字！');
            }
        }

        //供应商验证
        $this->mysql_model->get_count(CONTACT, '(id=' . intval($data['buId']) . ')') < 1 && str_alert(-1, '采购单位不存在');

        //商品录入验证
        if (is_array($data['entries'])) {
            $system = $this->common_model->get_option('system');
            if ($system['requiredCheckStore'] == 1) {  //开启检查时判断
                $item = array();
                foreach ($data['entries'] as $k => $v) {
                    !isset($v['invId']) && str_alert(-1, '参数错误');
                    !isset($v['locationId']) && str_alert(-1, '参数错误');
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
            foreach ($item as $arr => $row) {
                !isset($row['invId']) && str_alert(-1, '参数错误');
                !isset($row['locationId']) && str_alert(-1, '参数错误');
                (float)$row['qty'] < 0 || !is_numeric($row['qty']) && str_alert(-1, '商品数量要为数字，请输入有效数字！');
                (float)$row['price'] < 0 || !is_numeric($row['price']) && str_alert(-1, '商品销售单价要为数字，请输入有效数字！');
                (float)$row['discountRate'] < 0 || !is_numeric($row['discountRate']) && str_alert(-1, '折扣率要为数字，请输入有效数字！');
                intval($row['locationId']) < 1 && str_alert(-1, '请选择相应的仓库！');
                !in_array(intval($row['locationId']), $storage) && str_alert(-1, $row['locationName'] . '不存在或不可用！');
                //库存判断
                if ($system['requiredCheckStore'] == 1) {
                    if (intval($data['transType']) == 150502) {                        //退货才验证
                        if (isset($inventory[$row['invId']][$row['locationId']])) {
                            $inventory[$row['invId']][$row['locationId']] < (float)$row['qty'] && str_alert(-1, $row['locationName'] . $row['invName'] . '商品库存不足！');
                        } else {
                            str_alert(-1, $row['invName'] . '库存不足！');
                        }
                    }
                }
            }
        } else {
            str_alert(-1, '提交的是空数据');
        }
        return $data;
    }


    //组装数据
    private function invoice_info($invoiceID, $data)
    {
        //入库完成的商品数
        $finishStock = 0;
        //仓库里面为0的商品数
        $unstock = 0;
        $stockinSum = 0;

        if (is_array($data['entries'])) {
            foreach ($data['entries'] as $arr => $row) {
                $orderInfoid = $row['srcId'];
                if (empty($orderInfoid)) {
                    exit("orderinfo id 为空");
                }
                $orderInfo = $this->mysql_model->get_row(ORDER_INFO, '(id=' . $orderInfoid . ')');
                //exit(print_r($orderInfo));


                if (count($orderInfo) == 0) continue;
                $res = $this->mysql_model->query(INVOICE_INFO,
                    "SELECT SUM(qty) as stockQty from ci_invoice_info
						WHERE srcId=$orderInfoid and billNo!='$data[billNo]'");//要排除本次输入的数字
                $stockQty = $res['stockQty'];
                $newStockQty = $stockQty + $row['stockinQty'];
                if ($newStockQty >= $orderInfo['qty']) {
                    //入库完成商品数
                    $finishStock++;
                } else if ($newStockQty == 0) {
                    $unstock++;
                }
                if ($row['stockinQty'] == 0) continue;
                $stockinSum += $row['stockinQty'];
                //更新采购数据库order_info
                $info = array('stockQty' => $newStockQty);
                $ret = $this->mysql_model->update(ORDER_INFO, $info, '(id=' . $orderInfoid . ')');
                if ($ret == false) {
                    exit("更新出错1");
                }
                $invoiceInfo = $this->mysql_model->get_row(INVOICE_INFO, "(srcId=$orderInfoid and
							billNo='$data[billNo]')");
                if (count($invoiceInfo) > 0) {
                    //更新数据
                    $info = array(
                        'description' => $row['description'],
                        'billDate' => $data['date'],
                        'qty' => $row['stockinQty'],
                        'locationId' => $row['locationId'],
                        'deduction' => ($orderInfo['discountRate'] * $row['stockinQty']),
                        'amount' => ($orderInfo['price'] * $row['stockinQty'] - $invoiceInfo['deduction'])
                    );
                    $ret = $this->mysql_model->update(INVOICE_INFO, $info, "(srcId=$orderInfoid and
							billNo='$data[billNo]')");
                    if ($ret == false) {
                        exit("更新出错2");
                    }
                } else {
                    //新的数据插入库存数据库
                    $invoiceInfo = elements(array(
                        'buId',
                        'invId',
                        'transType',
                        'transTypeName',
                        'price',
                        'discountRate',
                        'locationId',
                        'tax',
                        'taxRate',
                        'taxAmount',
                        'unitId',
                        'skuId',
                        'entryId',
                        'billType',
                        'salesId'
                    ), $orderInfo);
                    $invoiceInfo['srcId'] = $orderInfo['id'];
                    $invoiceInfo['srcBillNo'] = $orderInfo['billNo'];
                    $invoiceInfo['description'] = $row['description'];
                    $invoiceInfo['locationId'] = $row['locationId'];
                    $invoiceInfo['iid'] = $invoiceID;
                    $invoiceInfo['billNo'] = $data['billNo'];
                    $invoiceInfo['billDate'] = $data['date'];
                    $invoiceInfo['qty'] = $row['stockinQty'];
                    $invoiceInfo['deduction'] = $orderInfo['discountRate'] * $invoiceInfo['qty'];
                    $invoiceInfo['amount'] = $orderInfo['price'] * $invoiceInfo['qty'] - $invoiceInfo['deduction'];
                    $this->mysql_model->insert(INVOICE_INFO, $invoiceInfo);
                }
            }
        }
        $billStatus = 1;
        if ($finishStock == count($data['entries'])) {
            //入库完成
            $billStatus = 2;
        }
        if ($unstock == count($data['entries'])) {
            //未入库
            $billStatus = 0;
        }
        return array('billStatus' => $billStatus, 'stockinSum' => $stockinSum);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */