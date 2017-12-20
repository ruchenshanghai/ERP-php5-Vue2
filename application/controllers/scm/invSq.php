<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//销售报价单
class InvSq extends CI_Controller {
	private $billType="'SQ'";
	private $transType=159001;
	private $transTypeName="销售报价";
    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys = $this->session->userdata('jxcsys');
    }
	
	public function index() {
	    $action = $this->input->get('action',TRUE);
		switch ($action) {
			case 'initSq':
			    $this->common_model->checkpurview(7);
			    $this->load->view('scm/invSq/initSq');	
				break;  
			case 'editSq':
			    $this->common_model->checkpurview(6);
			    $this->load->view('scm/invSq/initSq');	
				break;  	
			case 'initSqList':
			    $this->common_model->checkpurview(6);
			    $this->load->view('scm/invSq/initSqList');
				break; 
			default:  
			    $this->common_model->checkpurview(6);
			    $this->sqList();	
		}
	}
	
	public function sqList(){
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page = max(intval($this->input->get_post('page',TRUE)),1);
		$rows = max(intval($this->input->get_post('rows',TRUE)),100);
		$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
		$sord = str_enhtml($this->input->get_post('sord',TRUE));
		$transType = $this->transType;
		$hxState   = intval($this->input->get_post('hxState',TRUE));
		$salesId   = intval($this->input->get_post('salesId',TRUE));
		$matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
		$endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
		$sale = $sidx ? $sidx.' '.$sord :' a.id desc';
		$where = " and a.billType=$this->billType";
		$where .= $transType>0  ? ' and a.transType='.$transType : ''; 
		$where .= $salesId>0    ? ' and a.salesId='.$salesId : ''; 
		$where .= $hxState>0    ? ' and a.hxStateCode='.$hxState : ''; 
		$where .= $matchCon     ? ' and (b.name like "%'.$matchCon.'%" or description like "%'.$matchCon.'%" or billNo like "%'.$matchCon.'%")' : ''; 
		$where .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : ''; 
		$where .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : ''; 
		$offset = $rows * ($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->data_model->get_sale($where,3);               //总条数
		$data['data']['total']     = ceil($data['data']['records']/$rows);                   //总分页数
		$list = $this->data_model->get_sale($where.' order by '.$sale.' limit '.$offset.','.$rows.'');  
		foreach ($list as $arr=>$row) {
		    $v[$arr]['hxStateCode']  = intval($row['hxStateCode']);
		    $v[$arr]['checkName']    = $row['checkName'];
			$v[$arr]['checked']      = intval($row['checked']);
			$v[$arr]['salesId']      = intval($row['salesId']);
			$v[$arr]['staffName']    = $row['staffName'];
			$v[$arr]['billDate']     = $row['billDate'];
			$v[$arr]['billStatus']   = $row['billStatus'];
			$v[$arr]['totalQty']     = (float)$row['totalQty'];
			$v[$arr]['id']           = intval($row['id']);
		    $v[$arr]['amount']       = (float)abs($row['amount']);
			$v[$arr]['transType']    = $this->transType; 
			$v[$arr]['rpAmount']     = (float)abs($row['rpAmount']);
			$v[$arr]['contactName']  = $row['contactName'];
			$v[$arr]['description']  = $row['description'];
			$v[$arr]['billNo']       = $row['billNo'];
			$v[$arr]['totalAmount']  = (float)abs($row['totalAmount']);
			$v[$arr]['userName']     = $row['userName'];
			$v[$arr]['transTypeName']= $this->transTypeName;
			if(!empty($row['deliveryDate']))
			{
				try {
					$datetime1 = date_create($row['deliveryDate']);
					$datetime2 = date_create($row['billDate']);
					$interval = date_diff($datetime1, $datetime2);
					$v[$arr]['deliveryDate']= $interval->days;
				}
				catch (Exception $e)
				{
				}
			}
		}
		$data['data']['rows']        = $v;
		die(json_encode($data));
	}
	
	//导出
	public function exportInvSq() { 
	    $this->common_model->checkpurview(10);
		$name = 'sq_record_'.date('YmdHis').'.xls';
		sys_csv($name);
		$this->common_model->logs('导出销售单据:'.$name);
		$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
		$sord = str_enhtml($this->input->get_post('sord',TRUE));
		$transType = $this->transType;
		$hxState   = intval($this->input->get_post('hxState',TRUE));
		$salesId   = intval($this->input->get_post('salesId',TRUE));
		$matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));
		$beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
		$endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
		$sale = $sidx ? $sidx.' '.$sord :' a.id desc';
		$where = " and billType=$this->billType";
		$where .= $transType>0  ? ' and transType='.$transType : ''; 
		$where .= $salesId>0    ? ' and salesId='.$salesId : ''; 
		$where .= $hxState>0    ? ' and hxStateCode='.$hxState : ''; 
		$where .= $matchCon     ? ' and (b.name like "%'.$matchCon.'%" or description like "%'.$matchCon.'%" or billNo like "%'.$matchCon.'%")' : ''; 
		$where .= $beginDate    ? ' and billDate>="'.$beginDate.'"' : ''; 
		$where .= $endDate      ? ' and billDate<="'.$endDate.'"' : ''; 
		
		$where1 = " and a.billType=$this->billType";
		$where1 .= $transType>0  ? ' and a.transType='.$transType : ''; 
		$where1 .= $salesId>0    ? ' and a.salesId='.$salesId : ''; 
		$where1 .= $hxState>0    ? ' and a.hxStateCode='.$hxState : ''; 
		$where1 .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : ''; 
		$where1 .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : ''; 
		$data['list1'] = $this->data_model->get_sale($where.' order by '.$sale.'');  
		$data['list2'] = $this->data_model->get_sale_info($where1.' order by billDate');  
		$this->load->view('scm/invSq/exportInvSq',$data);
	}

	//新增
	public function add(){
	    $this->common_model->checkpurview(7);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		    $data = (array)json_decode($data, true); 
			$data = $this->validform($data);
			if(!empty($data['deliveryDate']))
			{
				$nday=intval($data['deliveryDate']);
				$data['deliveryDate']=date("Y-m-d",strtotime('+'.$nday.'day'));
			}
			$info = elements(array(
				'billNo',
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
				'totalAmount',
				'hxStateCode',
				'totalArrears',
				'disRate',
				'disAmount',
				'salesId',
				'uid',
				'userName',
				'accId',
				'modifyTime',
				'deliveryDate'
				),$data);
			$this->db->trans_begin();
			$iid = $this->mysql_model->insert(SALE,$info);
			$this->sale_info($iid,$data);
			$this->account_info($iid,$data);
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'SQL错误或者提交的是空数据'); 
			} else {
			    $this->db->trans_commit();
				$this->common_model->logs('新增销售 单据编号：'.$data['billNo']);
				str_alert(200,'success',array('id'=>intval($iid))); 
			}
		}
		str_alert(-1,'提交的是空数据'); 
    }
	
	//新增
	public function addNew(){
	    $this->add();
    }
	
	//修改
	public function updateInvSq(){
	    $this->common_model->checkpurview(8);
	    $data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
		    $data = (array)json_decode($data, true); 
			$id   = intval($data['id']);
			$data = $this->validform($data);
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
				'totalAmount',
				'hxStateCode',
				'totalArrears',
				'disRate',
				'disAmount',
				'salesId',
				'uid',
				'userName',
				'accId',
				'modifyTime',
		    	'currency'
				),$data);
			$this->db->trans_begin();
			$this->mysql_model->update(SALE,$info,'(id='.$id.')');
			$this->sale_info($id,$data);
			$this->account_info($id,$data);
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'SQL错误或者提交的是空数据'); 
			} else {
			    $this->db->trans_commit(); 
				$this->common_model->logs('修改销售 单据编号：'.$data['billNo']);
				str_alert(200,'success',array('id'=>$id)); 
			}
		}
		str_alert(-1,'提交的数据不为空'); 
    }
	
	//获取修改信息
	public function update() {
	    $this->common_model->checkpurview(6);
	    $id   = intval($this->input->get_post('id',TRUE));
		$data =  $this->data_model->get_sale("and (a.id=".$id.") and billType=$this->billType",1);
		if (count($data)>0) {
			$s = $v = array();
			$info['status'] = 200;
			$info['msg']    = 'success'; 
			$info['data']['id']                 = intval($data['id']);
			$info['data']['buId']               = intval($data['buId']);
			$info['data']['cLevel']             = 0;
			$info['data']['contactName']        = $data['contactName'];
			$info['data']['salesId']            = intval($data['salesId']);
			$info['data']['date']               = $data['billDate'];
			$info['data']['billNo']             = $data['billNo'];
			$info['data']['billType']           = $data['billType'];
			$info['data']['transType']          = $this->transType;
			$info['data']['totalQty']           = (float)$data['totalQty'];
			$info['data']['modifyTime']         = $data['modifyTime'];
			$info['data']['checkName']          = $data['checkName'];
			$info['data']['disRate']            = (float)$data['disRate'];
			$info['data']['disAmount']          = (float)$data['disAmount'];
			$info['data']['amount']             = (float)abs($data['amount']);
			$info['data']['rpAmount']           = (float)abs($data['rpAmount']);
			$info['data']['customerFree']       = (float)$data['customerFree'];
			$info['data']['arrears']            = (float)abs($data['arrears']);
			$info['data']['userName']           = $data['userName'];
			$info['data']['status']             = intval($data['checked'])==1 ? 'view' : 'edit'; //edit
			$info['data']['totalDiscount']      = (float)$data['totalDiscount'];
			$info['data']['totalAmount']        = (float)abs($data['totalAmount']); 
			$info['data']['description']        = $data['description'];
			$info['data']['accId']        = $data['accId'];
			$info['data']['currency']        = $data['currency'];
			if(!empty($data['deliveryDate']))
			{
				try {
					$datetime1 = date_create($data['deliveryDate']);
					$datetime2 = date_create($data['billDate']);
					$interval = date_diff($datetime1, $datetime2);
					$info['data']['deliveryDate']= $interval->days;
				}
				catch (Exception $e)
				{
				}
			}
			
			$list = $this->data_model->get_sale_info('and (iid='.$id.') order by id');  
			foreach ($list as $arr=>$row) {
				$v[$arr]['invSpec']           = $row['invSpec'];
				$v[$arr]['taxRate']           = (float)$row['taxRate'];
				$v[$arr]['srcOrderEntryId']   = intval($row['srcOrderEntryId']);
				$v[$arr]['srcOrderNo']        = $row['srcOrderNo'];
				$v[$arr]['srcOrderId']        = intval($row['srcOrderId']);
				$v[$arr]['goods']             = $row['invName'];
				$v[$arr]['spec']             = $row['invSpec'];
				$v[$arr]['invName']      = $row['invName'];
				$v[$arr]['qty']          = (float)abs($row['qty']);
				$v[$arr]['locationName'] = $row['locationName'];
				$v[$arr]['amount']       = (float)abs($row['amount']);
				$v[$arr]['taxAmount']    = (float)$row['taxAmount'];
				$v[$arr]['price']        = (float)$row['price'];
				$v[$arr]['tax']          = (float)$row['tax'];
				$v[$arr]['mainUnit']     = $row['mainUnit'];
				$v[$arr]['deduction']    = (float)$row['deduction'];
				$v[$arr]['invId']        = intval($row['invId']);
				$v[$arr]['invNumber']    = $row['invNumber'];
				$v[$arr]['locationId']   = intval($row['locationId']);
				$v[$arr]['locationName'] = $row['locationName'];
				$v[$arr]['discountRate'] = $row['discountRate'];
				$v[$arr]['description']  = $row['description'];
				
				$v[$arr]['unitId']       = intval($row['unitId']);
				$v[$arr]['mainUnit']     = $row['mainUnit'];
			}

			$info['data']['entries']     = $v;
			$info['data']['accId']       = (float)$data['accId'];
			$accounts = $this->data_model->get_account_info('and (iid='.$id.') order by id');  
			foreach ($accounts as $arr=>$row) {
				$s[$arr]['orderId']     = intval($id);
				$s[$arr]['billNo']        = $row['billNo'];
				$s[$arr]['buId']          = intval($row['buId']);
			    $s[$arr]['billType']      = $row['billType'];
				$s[$arr]['transType']     = $this->transType;
				$s[$arr]['transTypeName'] = $this->transTypeName;
				$s[$arr]['billDate']      = $row['billDate']; 
			    $s[$arr]['accId']         = intval($row['accId']);
				$s[$arr]['account']       = $row['accountNumber'].' '.$row['accountName']; 
				$s[$arr]['payment']       = (float)abs($row['payment']); 
				$s[$arr]['wayId']         = (float)$row['wayId']; 
				$s[$arr]['way']           = $row['categoryName']; 
				$s[$arr]['settlement']    = $row['settlement']; 
		    }  
			$info['data']['accounts']     = $s;
			die(json_encode($info));
		}
		str_alert(-1,'单据不存在、或者已删除');  
    }
	
    function getLinkman($linkMans)
    {
    	if (strlen($linkMans) <= 2)
    	{
    		return null;
    	}
    	$list = (array)json_decode($linkMans,true);
    	$row = null;
    	foreach ($list as $arr1=>$row1)
    	{
    		if ($row1['linkFirst'] == 1)
    		{
    			$row = $row1;
    			break;
    		}
    	}
    	if (empty($row))
    	{
    		$row = $list[0];
    	}
    	return $row;
    }
	//打印
    public function toPdf() {
	    $this->common_model->checkpurview(88);
	    $id   = intval($this->input->get('id',TRUE));
	    if(empty($id))
	    {
	    	$sidx = str_enhtml($this->input->get_post('sidx',TRUE));
	    	$sord = str_enhtml($this->input->get_post('sord',TRUE));
	    	$transType = $this->transType;
	    	$hxState   = intval($this->input->get_post('hxState',TRUE));
	    	$salesId   = intval($this->input->get_post('salesId',TRUE));
	    	$matchCon  = str_enhtml($this->input->get_post('matchCon',TRUE));
	    	$beginDate = str_enhtml($this->input->get_post('beginDate',TRUE));
	    	$endDate   = str_enhtml($this->input->get_post('endDate',TRUE));
	    	$sale = $sidx ? $sidx.' '.$sord :' a.id desc';
	    	$where = " and billType=$this->billType";
	    	$where .= $transType>0  ? ' and transType='.$transType : '';
	    	$where .= $salesId>0    ? ' and salesId='.$salesId : '';
	    	$where .= $hxState>0    ? ' and hxStateCode='.$hxState : '';
	    	$where .= $matchCon     ? ' and (b.name like "%'.$matchCon.'%" or description like "%'.$matchCon.'%" or billNo like "%'.$matchCon.'%")' : '';
	    	$where .= $beginDate    ? ' and billDate>="'.$beginDate.'"' : '';
	    	$where .= $endDate      ? ' and billDate<="'.$endDate.'"' : '';	    	
	    	$where1 = " and a.billType=$this->billType";
	    	$where1 .= $transType>0  ? ' and a.transType='.$transType : '';
	    	$where1 .= $salesId>0    ? ' and a.salesId='.$salesId : '';
	    	$where1 .= $hxState>0    ? ' and a.hxStateCode='.$hxState : '';
	    	$where1 .= $beginDate    ? ' and a.billDate>="'.$beginDate.'"' : '';
	    	$where1 .= $endDate      ? ' and a.billDate<="'.$endDate.'"' : '';
	    	$data['list1'] = $this->data_model->get_sale($where.' order by '.$sale.'');
	    	$data['list2'] = $this->data_model->get_sale_info($where1.' order by billDate');
	    	if (count($data)>0) {
		    	ob_start();
		    	$this->load->view('scm/invSq/listToPdf',$data);
		    	$content = ob_get_clean();		    	 
		    	require_once('./application/libraries/html2pdf/html2pdf.php');
		    	try {
		    		$html2pdf = new HTML2PDF('L', 'A3', 'tr');
		    		$html2pdf->setDefaultFont('javiergb');
		    		$html2pdf->pdf->SetDisplayMode('fullpage');
		    		$html2pdf->writeHTML($content, '');
		    		$html2pdf->Output('invPur_'.date('ymdHis').'.pdf','I');
		    	}catch(HTML2PDF_exception $e) {
		    		echo $e;
		    		exit;
		    	}
	    	}
	    	else {
	    		str_alert(-1,'单据不存在、或者已删除');
	    	}
	    }
	    else 
	    {
			$data = $this->data_model->get_sale("and (a.id=".$id.") and billType=$this->billType",1);  
			if (count($data)>0) { 
				$linkMan = $this->getLinkman($data['linkMans']);
				if (!empty($linkMan))
				{
					$data['buyerName'] = $linkMan['linkName'];
					$data['buyerPhone'] = $linkMan['linkPhone'];
					$data['buyerMobile'] = $linkMan['linkMobile'];
					$data['buyerEmail'] = $linkMan['linkIm'];
					$data['buyerFax'] = $linkMan['linkFax'];
					$data['buyerZipcode'] = $linkMan['linkZipcode'];
					$data['buyerAddress'] = $linkMan['province'] . $linkMan['city'] . $linkMan['county'] . $linkMan['address'];
				}
				
				if(!empty($data['deliveryDate']))
				{
					try {
						$datetime1 = date_create($data['deliveryDate']);
						$datetime2 = date_create($data['billDate']);
						$interval = date_diff($datetime1, $datetime2);
						$data['deliveryDate']= $interval->days;
					}
					catch (Exception $e)
					{
					}
				}
				
				$data['num']    = 8;
				$data['system'] = $this->common_model->get_option('system'); 
				$list = $this->data_model->get_sale_info('and (iid='.$id.') order by id');  
				$data['countpage']  = ceil(count($list)/$data['num']);   //共多少页
				foreach($list as $arr=>$row) {
				    $data['list'][] = array(
					'i'=>$arr + 1,
					'goods'=>$row['invNumber'].' '.$row['invName'],
					'invSpec'=>$row['invSpec'],
				    'remark'=>$row['remark'],
				    'unitName'=>$row['mainUnit'],
					'qty'=>abs($row['qty']),
					'price'=>$row['price'],
				    'currencyCode'=>$row['currencyCode'],
					'discountRate'=>$row['discountRate']>0?$row['discountRate']:'',
					'deduction'=>$row['deduction']>0?$row['deduction']:'',
					'amount'=>$row['amount'],
					'locationName'=>$row['locationName']
					);  
				}
			    ob_start();
				$this->load->view('scm/invSq/toPdf',$data);
				$content = ob_get_clean();		
				require_once('./application/libraries/html2pdf/html2pdf.php');
				try {
				    $html2pdf = new HTML2PDF('P', 'A4', 'en');
					$html2pdf->setDefaultFont('javiergb');
					$html2pdf->pdf->SetDisplayMode('fullpage');
					$html2pdf->writeHTML($content, '');
					$html2pdf->Output('invSq_'.date('ymdHis').'.pdf','I');
				}catch(HTML2PDF_exception $e) {
					echo $e;
					exit;
				}  
			} else {
			    str_alert(-1,'单据不存在、或者已删除');  	  
			}
	    }
	}
	
	
	
	//删除 
    public function delete() {
	    $this->common_model->checkpurview(9);
	    $id   = intval($this->input->get('id',TRUE));
		$data = $this->mysql_model->get_row(SALE,"(id=".$id.") and billType=$this->billType");  
		if (count($data)>0) {
		    $data['checked'] >0 && str_alert(-1,'已审核的不可删除'); 
		    $info['isDelete'] = 1;
		    $this->db->trans_begin();
			$this->mysql_model->update(SALE,$info,'(id='.$id.')');   
			$this->mysql_model->update(SALE_INFO,$info,'(iid='.$id.')');  
			$this->mysql_model->update(ACCOUNT_INFO,$info,'(iid='.$id.')');    
			if ($this->db->trans_status() === FALSE) {
			    $this->db->trans_rollback();
				str_alert(-1,'删除失败'); 
			} else {
			    $this->db->trans_commit();
				$this->common_model->logs('删除采购订单 单据编号：'.$data['billNo']);
				str_alert(200,'success'); 	 
			}
		}
		str_alert(-1,'单据不存在、或者已删除');  
	}

	
	//库存查询
	public function justIntimeInv() {
	    $v = array();
		$qty = 0;
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$page  = max(intval($this->input->get_post('page',TRUE)),1);
		$rows  = max(intval($this->input->get_post('rows',TRUE)),100);
		$invid = intval($this->input->get_post('invId',TRUE));
		$where = $invid > 0 ? ' and a.invId='.$invid.'' : ''; 
		$data['data']['total']     = 1;                         
		$data['data']['records']   = $this->data_model->get_inventory($where.' GROUP BY locationId',3);    
		$list = $this->data_model->get_inventory($where.' GROUP BY locationId');    
		foreach ($list as $arr=>$row) {
		    $i = $arr + 1;
			$v[$arr]['locationId']   = intval($row['locationId']);
			$qty += $v[$arr]['qty']  = (float)$row['qty'];
			$v[$arr]['locationName'] = $row['locationName'];
			$v[$arr]['invId']        = $row['invId'];
		}
		$v[$i]['locationId']   = 0;
		$v[$i]['qty']          = $qty;
		$v[$i]['locationName'] = '合计';
		$v[$i]['invId']        = 0;
		$data['data']['rows']  = $v;
		die(json_encode($data));
	}
	

	public function findNearSqEmp() {
		die('{"status":200,"msg":"success","data":{"empId":0}}');
		
	}

	//批量审核
	public function batchcheckinvsq() {
		$this->common_model->checkpurview(89);
		$id   = str_enhtml($this->input->post('id',TRUE));
		$data = $this->mysql_model->get_results(SALE,'(id in('.$id.")) and billType=$this->billType and checked=0 and isDelete=0");
		if (count($data)>0) {
			$info['checked']   = 1;
			$info['checkName'] = $this->jxcsys['name'];
			$result = $this->mysql_model->update(SALE,$info,'(id in('.$id.'))');
			if ($result) {
				$billno = array_column($data,'billNo','id');
				$billno = join(',',$billno);
				$this->mysql_model->delete(SALE);
				$this->common_model->logs('采购订单订单编号：'.$billno.'的单据已被审核！');
				str_alert(200,'订单编号：'.$billno.'的单据已被审核！');
			} else {
				str_alert(-1,'审核失败');
			}
		}
		str_alert(-1,'所选的单据都已被审核，请选择未审核的单据进行审核！');
	}
	
	//批量反审核
	public function rsbatchcheckinvsq() {
		$this->common_model->checkpurview(90);
		$id   = str_enhtml($this->input->post('id',TRUE));
		$data = $this->mysql_model->get_results(SALE,'(id in('.$id.")) and billType=$this->billType and checked=1 and isDelete=0");
		if (count($data)>0) {
			$info['checked']   = 0;
			$info['checkName'] = '';
			$result = $this->mysql_model->update(SALE,$info,'(id in('.$id.'))');
			if ($result) {
				$billno = array_column($data,'billNo','id');
				$billno = join(',',$billno);
				$this->mysql_model->delete(SALE);
				$this->common_model->logs('采购订单单号：'.$billno.'的单据已被反审核！');
				str_alert(200,'订单编号：'.$billno.'的单据已被反审核！');
			} else {
				str_alert(-1,'审核失败');
			}
		}
		str_alert(-1,'所选的订单都是未审核，请选择已审核的订单进行反审核！');
	}
	
	
	//单个审核
	public function checkInvSq() {
		$this->common_model->checkpurview(89);
		$data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
			$data = (array)json_decode($data, true);
			$id   = intval($data['id']);
			$data = $this->validform($data);
			$data['checked']         = 1;
			$data['checkName']       = $this->jxcsys['name'];
			$info = elements(array(
					'checked',
					'checkName',
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
					'totalAmount',
					'hxStateCode',
					'totalArrears',
					'disRate',
					'disAmount',
					'salesId',
					'uid',
					'userName',
					'accId',
					'modifyTime'
			),$data);
			$this->db->trans_begin();
			$this->mysql_model->update(SALE,$info,'(id='.$id.')');
			$this->sale_info($id,$data);
			$this->account_info($id,$data);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'SQL错误或者提交的是空数据');
			} else {
				$this->db->trans_commit();
				$this->common_model->logs('销售单编号：'.$data['billNo'].'的单据已被审核！');
				str_alert(200,'success',array('id'=>$id));
			}
		}
		str_alert(-1,'提交的数据不为空');
	}	
	
	//单个反审核
	public function revsCheckInvSq() {
		$this->common_model->checkpurview(90);
		$data = $this->input->post('postData',TRUE);
		if (strlen($data)>0) {
			$data = (array)json_decode($data, true);
			$id   = intval($data['id']);
			$data = $this->validform($data);
			$data['checked']         = 0;
			$data['checkName']       = '';
			$info = elements(array(
					'checked',
					'checkName',
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
					'totalAmount',
					'hxStateCode',
					'totalArrears',
					'disRate',
					'disAmount',
					'salesId',
					'uid',
					'userName',
					'accId',
					'modifyTime'
			),$data);
			$this->db->trans_begin();
			$this->mysql_model->update(SALE,$info,'(id='.$id.')');
			$this->sale_info($id,$data);
			$this->account_info($id,$data);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				str_alert(-1,'SQL错误或者提交的是空数据');
			} else {
				$this->db->trans_commit();
				$this->common_model->logs('销售单号：'.$data['billNo'].'的单据已被反审核！');
				str_alert(200,'success',array('id'=>$id));
			}
		}
		str_alert(-1,'提交的数据不为空');
	}
		
	//公共验证
	private function validform($data) {
	    if (isset($data['id']) && intval($data['id'])>0) {
		    $sale = $this->mysql_model->get_row(SALE,'(id='.$data['id'].") and billType=$this->billType and isDelete=0");  //修改的时候判断
			count($sale)<1 && str_alert(-1,'单据不存在、或者已删除');
			//$order['checked']>0 && str_alert(-1,'审核后不可修改');
			$data['billNo']      =  $sale['billNo'];	
		} else {
		    $data['billNo']      = str_no('XS');    //修改的时候屏蔽
		}
	
	    $data['buId']            = intval($data['buId']);
		$data['salesId']         = intval($data['salesId']);
		$data['billType']        = 'SQ';
		$data['billDate']        = $data['date'];
		$data['transType']       = $this->transType;
		$data['transTypeName']   = $this->transTypeName;
		$data['description']     = $data['description'];
		$data['totalQty']        = (float)$data['totalQty'];
		$data['totalTax']        = isset($data['totalTax']) ? (float)$data['totalTax'] : 0;
		$data['totalTaxAmount']  = isset($data['totalTaxAmount']) ? (float)$data['totalTaxAmount'] : 0; 

		$data['amount']      = abs($data['amount']);
		$data['arrears']     = abs($data['arrears']);
		$data['rpAmount']    = abs($data['rpAmount']);
		$data['totalAmount'] = abs($data['totalAmount']);
		 
			 
		$data['disRate']        = (float)$data['disRate'];
		$data['disAmount']      = (float)$data['disAmount'];
		$data['hxStateCode']    = $data['rpAmount']==$data['amount'] ? 2 : ($data['rpAmount']>0 ? 1 : 0); 
		$data['totalArrears']   = (float)$data['totalArrears'];
		$data['totalDiscount']  = (float)$data['totalDiscount'];
		$data['customerFree']   = (float)$data['customerFree'];
		$data['accId']          = (float)$data['accId'];
		$data['uid']            = $this->jxcsys['uid'];
		$data['userName']       = $this->jxcsys['name'];  
		$data['modifyTime']     = date('Y-m-d H:i:s');
	
	
 
		(float)$data['arrears'] < 0  && str_alert(-1,'本次欠款要为数字，请输入有效数字！'); 
		(float)$data['disRate'] < 0  && str_alert(-1,'折扣率要为数字，请输入有效数字！'); 
		(float)$data['rpAmount'] < 0 && str_alert(-1,'本次收款要为数字，请输入有效数字！'); 
		(float)$data['customerFree'] < 0 && str_alert(-1,'客户承担费用要为数字，请输入有效数字！'); 
		//(float)$data['amount'] < (float)$data['rpAmount']  && str_alert(-1,'折扣率要为[0-100]之间数字，请输入有效数字！'); 
		//(float)$data['amount'] < (float)$data['disAmount'] && str_alert(-1,'折扣额不能大于合计金额！'); 
 
			
		//选择了结算账户 需要验证 
		if (isset($data['accounts']) && count($data['accounts'])>0) {
			foreach ($data['accounts'] as $arr=>$row) {
				(float)$row['payment'] < 0 && str_alert(-1,'结算金额要为数字，请输入有效数字！');
			}  
        }
			
		//商品录入验证
		if (is_array($data['entries'])) {
		    $system    = $this->common_model->get_option('system'); 
			
		    if ($system['requiredCheckStore']==1) {  //开启检查时判断
				$item = array();                     
				foreach($data['entries'] as $k=>$v){
				    !isset($v['invId']) && str_alert(-1,'参数错误');    
					!isset($v['locationId']) && str_alert(-1,'参数错误'); 
					if(!isset($item[$v['invId'].'-'.$v['locationId']])){    
						$item[$v['invId'].'-'.$v['locationId']] = $v;
					}else{
						$item[$v['invId'].'-'.$v['locationId']]['qty'] += $v['qty'];        //同一仓库 同一商品 数量累加
					}
				}
				$inventory = $this->data_model->get_invoice_info_inventory();
			} else {
			    $item = $data['entries'];	
			}
			
			//获取门店ID
			$storage   = array_column($this->mysql_model->get_results(STORAGE,'(disable=0)'),'id');  
			
			foreach ($item as $arr=>$row) {
			    !isset($row['invId']) && str_alert(-1,'参数错误');    
				!isset($row['locationId']) && str_alert(-1,'参数错误'); 
				(float)$row['qty'] < 0 || !is_numeric($row['qty']) && str_alert(-1,'商品数量要为数字，请输入有效数字！'); 
				(float)$row['price'] < 0 || !is_numeric($row['price']) && str_alert(-1,'商品销售单价要为数字，请输入有效数字！'); 
				//(float)$row['discountRate'] < 0 || !is_numeric($row['discountRate']) && str_alert(-1,'折扣率要为数字，请输入有效数字！');
				intval($row['locationId']) < 1 && str_alert(-1,'请选择相应的仓库！'); 
				!in_array(intval($row['locationId']),$storage) && str_alert(-1,$row['locationName'].'不存在或不可用！');
				
			}
			
		} else {	 
			str_alert(-1,'提交的是空数据'); 
		} 
		
		//供应商验证
		$this->mysql_model->get_count(CONTACT,'(id='.intval($data['buId']).')')<1 && str_alert(-1,'客户不存在'); 
		
		return $data;
		
	}  
	
	
	
	//组装数据
	private function sale_info($iid,$data) {
	    if (is_array($data['entries'])) {
			foreach ($data['entries'] as $arr=>$row) {
			    if (intval($row['invId'])>0) {
					$v[$arr]['iid']           = $iid;
					$v[$arr]['billNo']        = $data['billNo'];
					$v[$arr]['billDate']      = $data['billDate']; 
					$v[$arr]['buId']          = $data['buId'];
					$v[$arr]['transType']     = $this->transType;
					$v[$arr]['transTypeName'] = $this->transTypeName;
					$v[$arr]['billType']      = $data['billType'];
					$v[$arr]['salesId']       = $data['salesId'];
					$v[$arr]['invId']         = intval($row['invId']);
					$v[$arr]['skuId']         = intval($row['skuId']);
					$v[$arr]['unitId']        = intval($row['unitId']);
					$v[$arr]['locationId']    = intval($row['locationId']);
					$v[$arr]['qty']       = -abs($row['qty']); 
					$v[$arr]['amount']    = abs($row['amount']); 
					$v[$arr]['price']         = abs($row['price']);  
					$v[$arr]['discountRate']  = $row['discountRate'];  
					$v[$arr]['deduction']     = $row['deduction'];  
					$v[$arr]['description']   = $row['description'];   
					//$v[$arr]['srcOrderEntryId']    = intval($row['srcOrderEntryId']);
	//				$v[$arr]['srcOrderNo']         = $row['srcOrderNo'];
	//				$v[$arr]['srcOrderId']         = intval($row['srcOrderId']); 
				} 
			} 
			if (isset($v)) {
			    if (isset($data['id']) && $data['id']>0) {                    //修改的时候   
				    $this->mysql_model->delete(SALE_INFO,'(iid='.$iid.')');
				}
				$this->mysql_model->insert(SALE_INFO,$v);
			}
		} 
	}
	
	//组装数据
	private function account_info($iid,$data) {
	    if (isset($data['accounts']) && count($data['accounts'])>0) {
			foreach ($data['accounts'] as $arr=>$row) {
			    if (intval($row['accId'])>0) {
					$v[$arr]['iid']           = intval($iid);
					$v[$arr]['billNo']        = $data['billNo'];
					$v[$arr]['buId']          = $data['buId'];
					$v[$arr]['billType']      = $data['billType'];
					$v[$arr]['transType']     = $this->transType;
					$v[$arr]['transTypeName'] = $this->transTypeName;
					$v[$arr]['billDate']      = $data['billDate']; 
					$v[$arr]['accId']         = $row['accId']; 
					$v[$arr]['payment']       = abs($row['payment']); 
					$v[$arr]['wayId']         = $row['wayId'];
					$v[$arr]['settlement']    = $row['settlement'] ;
				} 
			} 
			if (isset($v)) {
			    if (isset($data['id']) && $data['id']>0) {                      //修改的时候
				    $this->mysql_model->delete(ACCOUNT_INFO,'(iid='.$iid.')');
				}
			    $this->mysql_model->insert(ACCOUNT_INFO,$v);
			}
		} 
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */