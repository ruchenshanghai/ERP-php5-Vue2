<?php

if(! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	// 销售出库
class InvSd extends CI_Controller
{
	private $billType = "'SALE'";
	private $transType = 150601;
	private $transTypeName = "销售出库";
	public function __construct()
	{
		parent::__construct ();
		$this->common_model->checkpurview ();
		$this->jxcsys = $this->session->userdata ( 'jxcsys' );
	}
	public function index()
	{
		$action = $this->input->get ( 'action', TRUE );
		switch ($action)
		{
			case 'initSd' :
				$this->common_model->checkpurview ( 2 );
				$this->load->view ( 'scm/invSd/initSd' );
				break;
			case 'editSd' :
				$this->common_model->checkpurview ( 1 );
				$this->load->view ( 'scm/invSd/initSd' );
				break;
			case 'initSdList' :
				$this->common_model->checkpurview ( 1 );
				$this->load->view ( 'scm/invSd/initSdList' );
				break;
			case 'unDeliveryList' :
				$this->unDeliveryList ();
				break;
            case 'list':
                $this->common_model->checkpurview ( 1 );
                $this->sdList ();
                break;
			default :
				$this->common_model->checkpurview ( 1 );
				$this->sdList ();
		}
	}
	public function sdList()
	{
		$v = array ();
		$data ['status'] = 200;
		$data ['msg'] = 'success';
		$page = max ( intval ( $this->input->get_post ( 'page', TRUE ) ), 1 );
		$rows = max ( intval ( $this->input->get_post ( 'rows', TRUE ) ), 100 );
		$sidx = str_enhtml ( $this->input->get_post ( 'sidx', TRUE ) );
		$sord = str_enhtml ( $this->input->get_post ( 'sord', TRUE ) );
		$transType = intval ( $this->input->get_post ( 'transType', TRUE ) );
		$matchCon = str_enhtml ( $this->input->get_post ( 'matchCon', TRUE ) );
		$beginDate = str_enhtml ( $this->input->get_post ( 'beginDate', TRUE ) );
		$endDate = str_enhtml ( $this->input->get_post ( 'endDate', TRUE ) );
		$sale = $sidx ? $sidx . ' ' . $sord : ' a.id desc';
		$where = " and a.billType=$this->billType";
		$where .= $transType > 0 ? ' and a.transType=' . $transType : '';
		$where .= $matchCon ? ' and (b.name like "%' . $matchCon . '%" or description like "%' . $matchCon . '%" or billNo like "%' . $matchCon . '%")' : '';
		$where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
		$where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
		$offset = $rows * ($page - 1);
		$data ['data'] ['page'] = $page;
		$data ['data'] ['records'] = $this->data_model->get_invoice ( $where, 3 ); // 总条数
		$data ['data'] ['total'] = ceil ( $data ['data'] ['records'] / $rows ); // 总分页数
		$list = $this->data_model->get_invoice ( $where . ' order by ' . $sale . ' limit ' . $offset . ',' . $rows . '' );
		foreach ( $list as $arr => $row )
		{
			$v [$arr] ['id'] = intval ( $row ['id'] );
			$v [$arr] ['checkName'] = $row ['checkName'];
			$v [$arr] ['checked'] = intval ( $row ['checked'] );
			$v [$arr] ['billDate'] = $row ['billDate'];
			$v [$arr] ['contactName'] = $row ['contactName']; // $row['contactNo'].' '.$row['contactName'];
			$v [$arr] ['description'] = $row ['description'];
			$v [$arr] ['billNo'] = $row ['billNo'];
			$v [$arr] ['userName'] = $row ['userName'];
			$v [$arr] ['locationName'] = $row ['locationName'];
			$v [$arr] ['disEditable'] = 0;
			$v [$arr] ['totalQty'] = $row ['totalQty'];
			if(! empty ( $row ['srcId'] ))
			{
				$saleInfo = $this->mysql_model->get_row ( SALE, '(id=' . $row ['srcId'] . ')' );
                $v [$arr] ['saleQty'] = $saleInfo ['totalQty'];
				if(count ( $saleInfo ) > 0)
				{
					if($saleInfo ['billStatus'] == 2)
					{
						$v [$arr] ['billStatus'] = "全部发货";
					}
					elseif($saleInfo ['billStatus'] == 1)
					{
						$v [$arr] ['billStatus'] = "部分发货";
					}
					else
					{
						$v [$arr] ['billStatus'] = "未发货";
					}
                    $v[$arr]['srcBillNo']=$saleInfo['billNo'];
                    $v[$arr]['srcId']=$saleInfo['id'];
				}
			}			
			// exit(print_r($v[$arr]));
		}
		$data ['data'] ['rows'] = $v;
		die ( json_encode ( $data ) );
	}
	
	// 导出
	public function exportInvSd()
	{
		$this->common_model->checkpurview ( 5 );
		$name = 'sd_record_' . date ( 'YmdHis' ) . '.xls';
		sys_csv ( $name );
		$this->common_model->logs ( '导出采购发货单据:' . $name );
		$sidx = str_enhtml ( $this->input->get_post ( 'sidx', TRUE ) );
		$sord = str_enhtml ( $this->input->get_post ( 'sord', TRUE ) );
		$transType = intval ( $this->input->get_post ( 'transType', TRUE ) );
		$matchCon = str_enhtml ( $this->input->get_post ( 'matchCon', TRUE ) );
		$beginDate = str_enhtml ( $this->input->get_post ( 'beginDate', TRUE ) );
		$endDate = str_enhtml ( $this->input->get_post ( 'endDate', TRUE ) );
		$sale = $sidx ? $sidx . ' ' . $sord : ' a.id desc';
		$where = " and a.billType=$this->billType";
		$where .= $transType > 0 ? ' and a.transType=' . $transType : '';
		$where .= $matchCon ? ' and (b.name like "%' . $matchCon . '%" or description like "%' . $matchCon . '%" or billNo like "%' . $matchCon . '%")' : '';
		$where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
		$where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
		$where1 = " and a.billType=$this->billType";
		$where1 .= $transType > 0 ? ' and a.transType=' . $transType : '';
		$where1 .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
		$where1 .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
		$data ['list1'] = $this->data_model->get_invoice ( $where . ' order by ' . $sale . '' );
		$data ['list2'] = $this->data_model->get_invoice_info ( $where1 . ' order by a.billDate' );
		$this->load->view ( 'scm/invSd/exportInvSd', $data );
	}
	
	// 新增
	public function add()
	{
		$this->common_model->checkpurview ( 2 );
		$data = $this->input->post ( 'postData', TRUE );
		//exit(print_r($data));
		if(strlen ( $data ) > 0)
		{
		    //exit(print_r($data));
			$data = ( array ) json_decode ( $data, true );
            //exit(print_r($data));
			$sale = $this->mysql_model->get_row ( SALE, '(id=' . $data ['id'] . ')' ); // 修改的时候判断
			count ( $sale ) < 1 && str_alert ( - 1, '单据不存在、或者已删除' );
			if($sale ['billStatus'] == 2)
			{
				// 已经全部发货
				str_alert ( 200, 'success', '已经全部发货' );
			}
			
			// $invoice = $this->mysql_model->get_row(INVOICE,'(srcId='.$data['id'].')');
			$this->db->trans_begin ();
			// if(count($invoice)==0)
			// 每次都插入新数据
			{
				// 插入新数据
				$invoice = elements ( array (
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
				), $sale );
                $invoice ['transTypeName'] = $this->transTypeName;
				$invoice ['srcId'] = $sale ['id'];
				$invoice ['srcBillNo'] = $sale ['billNo'];
				//出货日期
				$invoice ['billDate'] = date('Y-m-d');
				$invoice ['description'] = $data ['description'];
				$invoice ['billNo'] = $data ['billNo'];
				$invoice ['checked'] = 0;
				$invoice ['checkName'] = "";
				$invoice ['userName'] = $data ['userName'];
                $invoice ['uid'] = $this->mysql_model->get_row(STAFF,"(name='$data[userName]')",'id');
				// invoice总是发货完成
				$invoice ['billStatus'] = 2;
				//exit(print_r($invoice));
				$iid = $this->mysql_model->insert ( INVOICE, $invoice );
				if(empty($iid)) {
                    str_alert(-1,'票据插入错误');
                }
			}
			// else
			// {
			// $iid = $invoice['id'];
			// }
			$status = $this->invoice_info ( $iid, $data );
			// 更新采购数据库sale
			$info = array (
					'billStatus' => $status ['billStatus'] 
			);
			$ret = $this->mysql_model->update ( SALE, $info, '(id=' . $data ['id'] . ')' );
			// 更新数据库invoice
			$info = array (
					'billStatus' => $status ['billStatus'],
					'totalQty' => $status ['outSum']
			);
			$ret = $this->mysql_model->update ( INVOICE, $info, '(id=' . $iid . ')' );
			
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				$sql = $this->db->last_query ();
				str_alert ( - 1, $sql );
				// str_alert(-1,'SQL错误');
			}
			else
			{
				$this->db->trans_commit ();
				$this->common_model->logs ( '新增销售出库单据编号：' . $iid );
				str_alert ( 200, 'success', array (
						'id' => intval ( $iid ) 
				) );
			}
		}
		str_alert ( - 1, '提交的是空数据' );
	}
	
	// 新增
	public function addnew()
	{
		$this->add ();
	}
	
	// 修改保存
	public function updateInvSd()
	{
		$this->common_model->checkpurview ( 3 );
		$data = $this->input->post ( 'postData', TRUE );
		if(strlen ( $data ) > 0)
		{
			$data = json_decode ( $data, true );

			$invoiceID = $data ['id'];
			
			$ret = $this->mysql_model->get_row ( INVOICE, "(id=$invoiceID)" );
			$saleID = $ret ['srcId'];
			
			$this->db->trans_begin ();
			$status = $this->invoice_info ( $invoiceID, $data );
			// 更新采购数据库sale
			$info = array (
					'billStatus' => $status ['billStatus'] 
			);
			$ret = $this->mysql_model->update ( SALE, $info, '(id=' . $saleID . ')' );
			if($ret == false)
			{
				exit ( "updateInvSd更新出错1" . '(id=' . $saleID . ')' );
			}
			
			$info = array (
					'billStatus' => $status ['billStatus'],
					'modifyTime' => date('y-m-d h:i:s',time()),
					'totalQty' => $status ['outSum'] 
			);
			$ret = $this->mysql_model->update ( INVOICE, $info, '(id=' . $invoiceID . ')' );
			if($ret == false)
			{
				exit ( "updateInvSd更新出错1" );
			}
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				str_alert ( - 1, 'SQL错误' );
			}
			else
			{
				$this->db->trans_commit ();
				$this->common_model->logs ( '修改采购发货单 单据编号：' . $data ['billNo'] );
				str_alert ( 200, 'success', array (
						'id' => $data ['id'] 
				) );
			}
		}
		str_alert ( - 1, '提交的数据不能为空' );
	}
	
	// 获取未全部发货的订单编号
	public function unDeliveryList()
	{
		$this->common_model->checkpurview ( 1 );
		$sql = "select billNo,userName,billDate from " . SALE . " where (isDelete=0) and billType=$this->billType and transType='150601' and billStatus!=2 and checked=1 order by id desc";
		$result = $this->mysql_model->query ( SALE, $sql, 2 );
		foreach ( $result as $row )
		{
			$new [] = array (
					'billNo' => $row ['billNo'],
					'userName' => $row ['userName'],
					'billDate' => $row ['billDate'] 
			);
		}
		exit ( json_encode ( $new ) );
	}

	//获取销售订单
	public function sale()
	{
		$this->common_model->checkpurview ( 1 );
		// $id = intval($this->input->get_post('id',TRUE));
		$id = intval ( $_REQUEST ['id'] );
		$condition = "";
		if(! empty ( $id ))
		{
			$condition .= " and (a.id=$id)";
		}
		if(! empty ( $_REQUEST ['billNo'] ))
		{
			$condition .= " and (a.billNo='" . $_REQUEST [billNo] . "')";
		}
		//获取销售订单单据
        //exit($condition . " and billType=$this->billType");
		//$data = $this->data_model->get_sale ( $condition . " and billType=$this->billType", 1 );
        $data = $this->data_model->get_sale ( $condition . " and billType=$this->billType", 1 );
		if(count ( $data ) > 0)
		{
			$id = $info['data']['id'] = intval($data['id']);
			$s = $v = array ();
			$info ['status'] = 200;
			$info ['msg'] = 'success';
			// 单据编号
			$info ['data'] ['billNo'] = $data ['billNo'];
			$info ['data'] ['billType'] = $data ['billType'];
			$info ['data'] ['modifyTime'] = $data ['modifyTime'];
			$info ['data'] ['userName'] = $data ['userName'];
			// 单据日期
			$info ['data'] ['date'] = $data ['billDate'];
			$info ['data'] ['description'] = $data ['description'];
			$info['data']['status'] = 'edit';//intval($data['checked'])==1 ? 'view' : 'edit';
			//$info ['data'] ['checked'] = $data['checked'];
            $info ['data'] ['totalQty'] = $data['totalQty'];

			//订单有可能分次出库、获取已经出库的单据
			$list = $this->data_model->get_sale_info ( 'and (iid=' . $id . ') order by id' );
			foreach ( $list as $arr => $row )
			{
				// 对应的销售商品id
				$v [$arr] ['siid'] = $row ['id'];
				// 商品
				$v [$arr] ['goods'] = $row ['invName'];
				// 商品型号
				$v [$arr] ['spec'] = $row ['invSpec'];
				// 单位
				$v [$arr] ['mainUnit'] = $row ['mainUnit'];
				// 仓库				
				$v[$arr]['locationId']          = intval($row['locationId']);
				$v[$arr]['locationName']        = $row['locationName'];
                // 销售数量
                $v [$arr] ['qty'] = abs($row ['qty']);
				// 已发货数量
				$v [$arr] ['outQty'] = abs($row ['outQty']);
                // 未发货数量
                $v [$arr] ['unOutQty'] = $v [$arr] ['qty'] - $v [$arr] ['outQty'];
				// 备注
				$v [$arr] ['description'] = $row ['description'];
				
				// 库存数量
				// 获取该商品在库数量
				$res = $this->mysql_model->query ( INVOICE_INFO, "SELECT SUM(qty) as stockQty from " . INVOICE_INFO . " WHERE invId=$row[invId]" );
				$v [$arr] ['stockQty'] = $res ['stockQty'];
			}
			
			//exit(print_r($v));
			
			$info ['data'] ['entries'] = $v;
			$info ['data'] ['accId'] = ( float ) $data ['accId'];
			$accounts = $this->data_model->get_account_info ( 'and (iid=' . $id . ') order by id' );
			foreach ( $accounts as $arr => $row )
			{
				$s [$arr] ['saleId'] = intval ( $id );
				$s [$arr] ['billNo'] = $row ['billNo'];
				$s [$arr] ['buId'] = intval ( $row ['buId'] );
				$s [$arr] ['billType'] = $row ['billType'];
				$s [$arr] ['transType'] = $row ['transType'];
				$s [$arr] ['transTypeName'] = $row ['transTypeName'];
				$s [$arr] ['billDate'] = $row ['billDate'];
				$s [$arr] ['accId'] = intval ( $row ['accId'] );
				$s [$arr] ['account'] = $row ['accountNumber'] . '' . $row ['accountName'];
				$s [$arr] ['payment'] = ( float ) abs ( $row ['payment'] );
				$s [$arr] ['wayId'] = ( float ) $row ['wayId'];
				$s [$arr] ['way'] = $row ['categoryName'];
				$s [$arr] ['settlement'] = $row ['settlement'];
			}
			$info ['data'] ['accounts'] = $s;
			die ( json_encode ( $info ) );
		}
		str_alert ( - 1, '单据不存在、或者已删除' );
	}

    //获取销售出库单
    public function getSalesDischarge()
    {
        $this->common_model->checkpurview ( 1 );
        // $id = intval($this->input->get_post('id',TRUE));
        $id = intval ( $_REQUEST ['id'] );
        $condition = "";
        if(! empty ( $id ))
        {
            $condition .= " and (a.id=$id)";
        }
        if(! empty ( $_REQUEST ['billNo'] ))
        {
            $condition .= " and (a.billNo='" . $_REQUEST [billNo] . "')";
        }
        //获取销售订单单据
        $data = $this->data_model->get_invoice ( $condition . " and billType=$this->billType", 1 );
        if(count ( $data ) > 0)
        {
            $id = $info['data']['id'] = intval($data['id']);
            $s = $v = array ();
            $info ['status'] = 200;
            $info ['msg'] = 'success';
            // 单据编号
            $info ['data'] ['billNo'] = $data ['billNo'];
            $info ['data'] ['billType'] = $data ['billType'];
            $info ['data'] ['modifyTime'] = $data ['modifyTime'];
            $info ['data'] ['userName'] = $data ['userName'];
            // 单据日期
            $info ['data'] ['date'] = $data ['billDate'];
            $info ['data'] ['description'] = $data ['description'];
            $info['data']['status'] = intval($data['checked'])==1 ? 'view' : 'edit';
            $info ['data'] ['checked'] = intval($data['checked']);
            $info ['data'] ['totalQty'] = $data['totalQty'];

            //订单有可能分次出库、获取已经出库的单据
            $list = $this->data_model->get_invoice_info ( 'and (iid=' . $id . ') order by id' );
            foreach ( $list as $arr => $row )
            {
                // 对应的销售商品id
                $v [$arr] ['siid'] = $row ['id'];
                // 商品
                $v [$arr] ['goods'] = $row ['invName'];
                // 商品型号
                $v [$arr] ['spec'] = $row ['invSpec'];
                // 单位
                $v [$arr] ['mainUnit'] = $row ['mainUnit'];
                // 仓库
                $v[$arr]['locationId']          = intval($row['locationId']);
                $v[$arr]['locationName']        = $row['locationName'];

                $saleInfo = $this->mysql_model->get_row ( SALE_INFO, '(id=' . $row ['srcId'] . ')' );

                // 销售数量
                $v [$arr] ['qty'] = abs($saleInfo ['qty']);
                // 已发货数量
                $v [$arr] ['outQty'] = abs($saleInfo ['outQty']);
                // 未发货数量
                $v [$arr] ['unOutQty'] = $v [$arr] ['qty'] - $v [$arr] ['outQty'];
                //本次发货数量
                $v [$arr] ['outingQty'] = abs($row ['qty']);
                // 备注
                $v [$arr] ['description'] = $row ['description'];

                // 库存数量
                // 获取该商品在库数量
                $res = $this->mysql_model->query ( INVOICE_INFO, "SELECT SUM(qty) as stockQty from " . INVOICE_INFO . " WHERE invId=$row[invId]" );
                $v [$arr] ['stockQty'] = $res ['stockQty'];
            }

            //exit(print_r($v));

            $info ['data'] ['entries'] = $v;
            $info ['data'] ['accId'] = ( float ) $data ['accId'];
            $accounts = $this->data_model->get_account_info ( 'and (iid=' . $id . ') order by id' );
            foreach ( $accounts as $arr => $row )
            {
                $s [$arr] ['saleId'] = intval ( $id );
                $s [$arr] ['billNo'] = $row ['billNo'];
                $s [$arr] ['buId'] = intval ( $row ['buId'] );
                $s [$arr] ['billType'] = $row ['billType'];
                $s [$arr] ['transType'] = $row ['transType'];
                $s [$arr] ['transTypeName'] = $row ['transTypeName'];
                $s [$arr] ['billDate'] = $row ['billDate'];
                $s [$arr] ['accId'] = intval ( $row ['accId'] );
                $s [$arr] ['account'] = $row ['accountNumber'] . '' . $row ['accountName'];
                $s [$arr] ['payment'] = ( float ) abs ( $row ['payment'] );
                $s [$arr] ['wayId'] = ( float ) $row ['wayId'];
                $s [$arr] ['way'] = $row ['categoryName'];
                $s [$arr] ['settlement'] = $row ['settlement'];
            }
            $info ['data'] ['accounts'] = $s;
            die ( json_encode ( $info ) );
        }
        str_alert ( - 1, '单据不存在、或者已删除' );
    }

	// 打印
	public function toPdf()
	{
		// Array ( [sidx] => [sord] => asc [op] => 2 [matchCon] => [transType] => 150501 [beginDate] => 2017-09-01 [endDate] => 2017-09-20 [marginLeft] => ) 1
		$this->common_model->checkpurview ( 85 );
		$id = intval ( $this->input->get ( 'id', TRUE ) );
		if(empty ( $id ))
		{
			// list
			$sidx = str_enhtml ( $this->input->get_post ( 'sidx', TRUE ) );
			$sord = str_enhtml ( $this->input->get_post ( 'sord', TRUE ) );
			$transType = intval ( $this->input->get_post ( 'transType', TRUE ) );
			$matchCon = str_enhtml ( $this->input->get_post ( 'matchCon', TRUE ) );
			$beginDate = str_enhtml ( $this->input->get_post ( 'beginDate', TRUE ) );
			$endDate = str_enhtml ( $this->input->get_post ( 'endDate', TRUE ) );
			$sale = $sidx ? $sidx . ' ' . $sord : ' a.id desc';
			$where = " and a.billType=$this->billType";
			$where .= $transType > 0 ? ' and a.transType=' . $transType : '';
			$where .= $matchCon ? ' and (b.name like "%' . $matchCon . '%" or description like "%' . $matchCon . '%" or billNo like "%' . $matchCon . '%")' : '';
			$where .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
			$where .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
			$where1 = " and a.billType=$this->billType";
			$where1 .= $transType > 0 ? ' and a.transType=' . $transType : '';
			$where1 .= $beginDate ? ' and a.billDate>="' . $beginDate . '"' : '';
			$where1 .= $endDate ? ' and a.billDate<="' . $endDate . '"' : '';
			$data ['list1'] = $this->data_model->get_invoice ( $where . ' order by ' . $sale . '' );
			$data ['list2'] = $this->data_model->get_invoice_info ( $where1 . ' order by a.billDate' );
			if(count ( $data ) > 0)
			{
				ob_start ();
				$this->load->view ( 'scm/invSd/listToPdf', $data );
				$content = ob_get_clean ();
				
				require_once ('./application/libraries/html2pdf/html2pdf.php');
				try
				{
					$html2pdf = new HTML2PDF ( 'L', 'A4', 'tr' );
					$html2pdf->setDefaultFont ( 'javiergb' );
					$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
					$html2pdf->writeHTML ( $content, '' );
					$html2pdf->Output ( 'invSd_' . date ( 'ymdHis' ) . '.pdf', 'I' );
				}
				catch ( HTML2PDF_exception $e )
				{
					echo $e;
					exit ();
				}
			}
			else
			{
				str_alert ( - 1, '单据不存在、或者已删除' );
			}
		}
		else
		{
			// item
			$data = $this->data_model->get_invoice ( "and (a.id=$id) and billType=$this->billType", 1 );
			if(count ( $data ) > 0)
			{
				$list = $this->data_model->get_invoice_info ( 'and (iid=' . $id . ') order by id' );
				$data ['num'] = 8;
				$data ['system'] = $this->common_model->get_option ( 'system' );
				$data ['countpage'] = ceil ( count ( $list ) / $data ['num'] );
				$totalQty=0;
				foreach ( $list as $arr => $row )
				{
					//本次发货数量
					$outingQty=abs($row['qty']);
					// 获取该商品在库数量
					$res = $this->mysql_model->query ( INVOICE_INFO, "SELECT SUM(qty) as stockQty from " . INVOICE_INFO . " WHERE invId=$row[invId]" );
					$stockQty = $res ['stockQty'];
					// 获取销售数量和发货数量
					$res = $this->mysql_model->query ( SALE_INFO, "SELECT qty,outQty from " . SALE_INFO . " WHERE id=$row[srcId]" );
					//销售总数
					$salesQty=abs($res ['qty']);
					$totalQty+=$salesQty;
					//累计发货总数
					$outQty=abs($res ['outQty']);
					// 未发货数量
					$unOutQty = $salesQty - $outQty;
					
					$data ['list'] [] = array (
							'i' => $arr + 1,
							'goods' => $row ['invName'],
							'invSpec' => $row ['invSpec'],
							'unitName' => $row ['mainUnit'],
							'stockQty' => $stockQty,
							'salesQty'=>$salesQty,
							'unOutQty'=>$unOutQty,
							'outingQty'=>$outingQty,
							'locationName' => $row ['locationName'],
							'description' => $row ['description'] 
					);					
				}
				$data['totalQty']=$totalQty;
				ob_start ();
				$this->load->view ( 'scm/invSd/toPdf', $data );
				$content = ob_get_clean ();
				
				require_once ('./application/libraries/html2pdf/html2pdf.php');
				try
				{
					$html2pdf = new HTML2PDF ( 'L', 'A4', 'tr' );
					$html2pdf->setDefaultFont ( 'javiergb' );
					$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
					$html2pdf->writeHTML ( $content, '' );
					$html2pdf->Output ( 'invSd_' . date ( 'ymdHis' ) . '.pdf', 'I' );
				}
				catch ( HTML2PDF_exception $e )
				{
					echo $e;
					exit ();
				}
			}
			else
			{
				str_alert ( - 1, '单据不存在、或者已删除' );
			}
		}
	}
	
	// 销售单删除
	public function delete()
	{
		$this->common_model->checkpurview ( 4 );
		$id = intval ( $this->input->get ( 'id', TRUE ) );
		$data = $this->mysql_model->get_row ( INVOICE, "(id=$id) and billType=$this->billType" );
		if(count ( $data ) > 0)
		{
			$data ['checked'] > 0 && str_alert ( - 1, '已审核的不可删除' );
			$info ['isDelete'] = 1;
			$this->db->trans_begin ();
			$this->mysql_model->update ( INVOICE, $info, '(id=' . $id . ')' );
			$this->mysql_model->update ( INVOICE_INFO, $info, '(iid=' . $id . ')' );
			$this->mysql_model->update ( ACCOUNT_INFO, $info, '(iid=' . $id . ')' );
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				str_alert ( - 1, '删除失败' );
			}
			else
			{
				$this->db->trans_commit ();
				$this->common_model->logs ( '删除采购发货订单 单据编号：' . $data ['billNo'] );
				str_alert ( 200, 'success' );
			}
		}
		str_alert ( - 1, '单据不存在、或者已删除' );
	}
	
	// 批量审核
	public function batchCheckInvSd()
	{
		$this->common_model->checkpurview ( 86 );
		$id = str_enhtml ( $this->input->post ( 'id', TRUE ) );
		$data = $this->mysql_model->get_results ( INVOICE, "(id in($id)) and billType=$this->billType and checked=0 and (isDelete=0)" );
		if(count ( $data ) > 0)
		{
			$info ['checked'] = 1;
			$info ['checkName'] = $this->jxcsys ['name'];
			$this->db->trans_begin ();
			$this->mysql_model->update ( INVOICE, $info, '(id in(' . $id . '))' );
			$this->mysql_model->update ( INVOICE_INFO, $info, '(id in(' . $id . '))' );
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				str_alert ( - 1, '审核失败' );
			}
			else
			{
				$this->db->trans_commit ();
				$billno = array_column ( $data, 'billNo' );
				$billno = join ( ',', $billno );
				$this->common_model->logs ( '采购发货单编号：' . $billno . '的单据已被审核！' );
				str_alert ( 200, '订单编号：' . $billno . '的单据已被审核！' );
			}
		}
		str_alert ( - 1, '所选的单据都已被审核，请选择未审核的单据进行审核！' );
	}
	
	// 批量反审核
	public function rsBatchCheckInvSd()
	{
		$this->common_model->checkpurview ( 87 );
		$id = str_enhtml ( $this->input->post ( 'id', TRUE ) );
		$data = $this->mysql_model->get_results ( INVOICE, "(id in($id)) and billType=$this->billType and checked=1 and (isDelete=0)" );
		if(count ( $data ) > 0)
		{
			$info ['checked'] = 0;
			$info ['checkName'] = '';
			$this->db->trans_begin ();
			$this->mysql_model->update ( INVOICE, $info, '(id in(' . $id . '))' );
			$this->mysql_model->update ( INVOICE_INFO, $info, '(id in(' . $id . '))' );
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				str_alert ( - 1, '反审核失败' );
			}
			else
			{
				$this->db->trans_commit ();
				$billno = array_column ( $data, 'billNo', 'id' );
				$billno = join ( ',', $billno );
				$this->common_model->logs ( '采购发货单号：' . $billno . '的单据已被反审核！' );
				str_alert ( 200, '订单编号：' . $billno . '的单据已被反审核！' );
			}
		}
		str_alert ( - 1, '所选的订单都是未审核，请选择已审核的订单进行反审核！' );
	}
	
	// 单个审核
	public function checkInvSd()
	{
		$this->common_model->checkpurview ( 86 );
		$data = $this->input->post ( 'postData', TRUE );
		if(strlen ( $data ) > 0)
		{
			$data = ( array ) json_decode ( $data, true );
			
			$id = intval ( $data ['id'] );
			if($id <= 0)
			{
				str_alert ( - 1, '数据还未保存' . $data ['id'] );
			}
			// $data = $this->validform($data);
			$data ['checked'] = 1;
			$data ['checkName'] = $this->jxcsys ['name'];
			$info = elements ( array (
					'checked',
					'checkName' 
			), $data );
			$this->db->trans_begin ();
			$this->mysql_model->update ( INVOICE, $info, '(id=' . $id . ')' );
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				str_alert ( - 1, 'SQL错误' );
			}
			else
			{
				$this->db->trans_commit ();
				$this->common_model->logs ( '采购发货单 单据编号：' . $data ['billNo'] . '的单据已被审核！' );
				str_alert ( 200, 'success', array (
						'id' => $id 
				) );
			}
		}
		str_alert ( - 1, '提交的数据不能为空' );
	}
	
	// 单个反审核
	public function revsCheckInvSd()
	{
		$this->common_model->checkpurview ( 87 );
		$data = $this->input->post ( 'postData', TRUE );
		if(strlen ( $data ) > 0)
		{
			$data = ( array ) json_decode ( $data, true );
			$id = intval ( $data ['id'] );
			// $data = $this->validform($data);
			$data ['checked'] = 0;
			$data ['checkName'] = '';
			$info = elements ( array (
					'checked',
					'checkName' 
			), $data );
			$this->db->trans_begin ();
			$this->mysql_model->update ( INVOICE, $info, '(id=' . $id . ')' );
			// $this->invoice_info($id,$data);
			// $this->account_info($id,$data);
			if($this->db->trans_status () === FALSE)
			{
				$this->db->trans_rollback ();
				str_alert ( - 1, 'SQL错误' );
			}
			else
			{
				$this->db->trans_commit ();
				$this->common_model->logs ( '采购发货单 单据编号：' . $data ['billNo'] . '的单据已被反审核！' );
				str_alert ( 200, 'success', array (
						'id' => $id 
				) );
			}
		}
		str_alert ( - 1, '提交的数据不能为空' );
	}
	
	// 公共验证
	private function validform($data)
	{
		if(isset ( $data ['id'] ) && intval ( $data ['id'] ) > 0)
		{
			$data ['id'] = intval ( $data ['id'] );
			$invoice = $this->mysql_model->get_row ( INVOICE, "(id=$data[id]) and billType=$this->billType and isDelete=0" ); // 修改的时候判断
			count ( $invoice ) < 1 && str_alert ( - 1, '单据不存在、或者已删除' );
			// jason.xie 暂时删除
			// $invoice['checked']>0 && str_alert(-1,'审核后不可修改');
			$data ['billNo'] = $invoice ['billNo'];
		}
		else
		{
			$data ['billNo'] = str_no ( 'SD' ); // 修改的时候屏蔽
		}
		
		$data ['billType'] = $this->billType;
		$data ['transType'] = intval ( $data ['transType'] );
		$data ['transTypeName'] = $data ['transType'] == $this->transType ? '采购发货' : '退货';
		$data ['buId'] = intval ( $data ['buId'] );
		$data ['billDate'] = $data ['date'];
		$data ['description'] = $data ['description'];
		$data ['totalQty'] = ( float ) $data ['totalQty'];
		if($data ['transType'] == $this->transType)
		{
			$data ['amount'] = abs ( $data ['amount'] );
			$data ['arrears'] = abs ( $data ['arrears'] );
			$data ['rpAmount'] = abs ( $data ['rpAmount'] );
			$data ['totalAmount'] = abs ( $data ['totalAmount'] );
		}
		else
		{
			$data ['amount'] = - abs ( $data ['amount'] );
			$data ['arrears'] = - abs ( $data ['arrears'] );
			$data ['rpAmount'] = - abs ( $data ['rpAmount'] );
			$data ['totalAmount'] = - abs ( $data ['totalAmount'] );
		}
		$data ['hxStateCode'] = $data ['rpAmount'] == $data ['amount'] ? 2 : ($data ['rpAmount'] > 0 ? 1 : 0);
		$data ['totalArrears'] = ( float ) $data ['totalArrears'];
		$data ['disRate'] = ( float ) $data ['disRate'];
		$data ['disAmount'] = ( float ) $data ['disAmount'];
		$data ['uid'] = $this->jxcsys ['uid'];
		$data ['userName'] = $this->jxcsys ['name'];
		$data ['accId'] = ( float ) $data ['accId'];
		
		$data ['modifyTime'] = date ( 'Y-m-d H:i:s' );
		
		// 选择了结算账户 需要验证
		if(isset ( $data ['accounts'] ) && count ( $data ['accounts'] ) > 0)
		{
			foreach ( $data ['accounts'] as $arr => $row )
			{
				( float ) $row ['payment'] < 0 || ! is_numeric ( $row ['payment'] ) && str_alert ( - 1, '结算金额要为数字，请输入有效数字！' );
			}
		}
		
		// 供应商验证
		$this->mysql_model->get_count ( CONTACT, '(id=' . intval ( $data ['buId'] ) . ')' ) < 1 && str_alert ( - 1, '采购单位不存在' );
		
		// 商品录入验证
		if(is_array ( $data ['entries'] ))
		{
			$system = $this->common_model->get_option ( 'system' );
			if($system ['requiredCheckStore'] == 1)
			{ // 开启检查时判断
				$item = array ();
				foreach ( $data ['entries'] as $k => $v )
				{
					! isset ( $v ['invId'] ) && str_alert ( - 1, '参数错误' );
					! isset ( $v ['locationId'] ) && str_alert ( - 1, '参数错误' );
					if(! isset ( $item [$v ['invId'] . '-' . $v ['locationId']] ))
					{
						$item [$v ['invId'] . '-' . $v ['locationId']] = $v;
					}
					else
					{
						$item [$v ['invId'] . '-' . $v ['locationId']] ['qty'] += $v ['qty']; // 同一仓库 同一商品 数量累加
					}
				}
				$inventory = $this->data_model->get_invoice_info_inventory ();
			}
			else
			{
				$item = $data ['entries'];
			}
			$storage = array_column ( $this->mysql_model->get_results ( STORAGE, '(disable=0)' ), 'id' );
			foreach ( $item as $arr => $row )
			{
				! isset ( $row ['invId'] ) && str_alert ( - 1, '参数错误' );
				! isset ( $row ['locationId'] ) && str_alert ( - 1, '参数错误' );
				( float ) $row ['qty'] < 0 || ! is_numeric ( $row ['qty'] ) && str_alert ( - 1, '商品数量要为数字，请输入有效数字！' );
				( float ) $row ['price'] < 0 || ! is_numeric ( $row ['price'] ) && str_alert ( - 1, '商品销售单价要为数字，请输入有效数字！' );
				( float ) $row ['discountRate'] < 0 || ! is_numeric ( $row ['discountRate'] ) && str_alert ( - 1, '折扣率要为数字，请输入有效数字！' );
				intval ( $row ['locationId'] ) < 1 && str_alert ( - 1, '请选择相应的仓库！' );
				! in_array ( intval ( $row ['locationId'] ), $storage ) && str_alert ( - 1, $row ['locationName'] . '不存在或不可用！' );
//				// 库存判断
//				if($system ['requiredCheckStore'] == 1)
//				{
//					if(intval ( $data ['transType'] ) == 150602)
//					{ // 退货才验证
//						if(isset ( $inventory [$row ['invId']] [$row ['locationId']] ))
//						{
//							$inventory [$row ['invId']] [$row ['locationId']] < ( float ) $row ['qty'] && str_alert ( - 1, $row ['locationName'] . $row ['invName'] . '商品库存不足！' );
//						}
//						else
//						{
//							str_alert ( - 1, $row ['invName'] . '库存不足！' );
//						}
//					}
//				}
			}
		}
		else
		{
			str_alert ( - 1, '提交的是空数据' );
		}
		return $data;
	}
	
	// 组装数据
	private function invoice_info($invoiceID, $data)
	{
		// 每个销售单有好几类商品
		// 发货完成的商品种类数
		$finishOut = 0;
		// 未发货的商品种类数
		$unOut = 0;
		// 总的发货商品数
		$outSum = 0;
		//exit(print_r($data ['entries']));
		if(is_array ( $data ['entries'] ))
		{
			foreach ( $data ['entries'] as $arr => $row )
			{
				$saleInfoid = $row ['siid'];
				$saleInfo = $this->mysql_model->get_row ( SALE_INFO, '(id=' . $saleInfoid . ')' );				
				if(count ( $saleInfo ) == 0)
					continue;
					// 获取此销售订单该商品的已经出货总数
				$res = $this->mysql_model->query ( INVOICE_INFO, "SELECT SUM(qty) as outQty from " . INVOICE_INFO . " WHERE srcId=$saleInfoid and billNo!='$data[billNo]'" ); // 要排除本次输入的数字
//exit(print_r($res));
				//已发货数量
				$outQty = abs($res ['outQty']);
				//本次发货数量、此为用户输入的发货数量
				$outingQty=abs($row ['outingQty']);

				// 此销售订单，该类产品发货总量
				$outGoodsQty = $outQty + $outingQty;
				if($outGoodsQty >= abs ( $saleInfo ['qty'] ))
				{
					// 发货数量大于等于销售数量，发货完成
					// 发货完成商品种类数
					$finishOut ++;
				}
				else if($outGoodsQty == 0)
				{
					// 发货数量为0，未发货
					$unOut ++;
				}
//				// 本次发货数量为0,也要更新其它信息
				if($outingQty == 0)
					continue;
					// 所有商品的发货总数
				$outSum += $outingQty;
				// 更新销售数据库sale_info
				$info = array (
						'outQty' => -$outGoodsQty 
				);
				$ret = $this->mysql_model->update ( SALE_INFO, $info, '(id=' . $saleInfoid . ')' );
				if($ret == false)
				{
					exit ( "更新出错1" );
				}
				$invoiceInfo = $this->mysql_model->get_row ( INVOICE_INFO, "(srcId=$saleInfoid and
							billNo='$data[billNo]')" );
				if(count ( $invoiceInfo ) > 0)
				{
					// 更新数据
					$info = array (
							'description' => $row ['description'],
							'billDate' => $data ['date'],
							'qty' => -$outingQty,
							'locationId' => $row ['locationId'],
							'deduction' => ($saleInfo ['discountRate'] * $outingQty),
							'amount' => ($saleInfo ['price'] * $outingQty - $invoiceInfo ['deduction']) 
					);
					$ret = $this->mysql_model->update ( INVOICE_INFO, $info, "(srcId=$saleInfoid and
							billNo='$data[billNo]')" );
					if($ret == false)
					{
						exit ( "更新出错2" );
					}
				}
				else
				{
					// 新的数据插入库存数据库
					$invoiceInfo = elements ( array (
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
					), $saleInfo );
                    $invoiceInfo ['transTypeName'] = $this->transTypeName;
					$invoiceInfo ['srcId'] = $saleInfoid;
					$invoiceInfo ['srcBillNo'] = $saleInfo ['billNo'];
					$invoiceInfo ['description'] = $row ['description'];
					$invoiceInfo ['locationId'] = $row ['locationId'];
					$invoiceInfo ['iid'] = $invoiceID;
					$invoiceInfo ['billNo'] = $data ['billNo'];
					$invoiceInfo ['billDate'] = $data ['date'];
					$invoiceInfo ['qty'] = -$outingQty;
					$invoiceInfo ['deduction'] = $saleInfo ['discountRate'] * $outingQty;
					$invoiceInfo ['amount'] = $saleInfo ['price'] * $outingQty - $invoiceInfo ['deduction'];
					//exit(print_r($invoiceInfo));
					$this->mysql_model->insert ( INVOICE_INFO, $invoiceInfo );
                    if($ret == false)
                    {
                        exit ( "INVOICE_INFO出错3" );
                    }
				}
			}
		}
		$billStatus = 1;
		if($finishOut == count ( $data ['entries'] ))
		{
			// 发货完成
			$billStatus = 2;
		}
		if($unstock == count ( $data ['entries'] ))
		{
			// 未发货
			$billStatus = 0;
		}
		return array (
				'billStatus' => $billStatus,
				'outSum' => $outSum 
		);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */