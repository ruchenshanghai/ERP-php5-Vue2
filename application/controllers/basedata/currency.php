<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Currency extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
    }
	
	//币种列表
	public function index(){
		$v = '';
	    $data['status'] = 200;
		$data['msg']    = 'success'; 
		$list = $this->mysql_model->get_results(CURRENCY,'(isDelete=0) order by id'); 
		//exit(print_r($list));
		foreach ($list as $arr=>$row) {
			$v[$arr]['id']         = intval($row['id']);
			$v[$arr]['code']       = $row['code'];
			$v[$arr]['name']       = $row['name'];			
			$v[$arr]['symbol']       = $row['symbol'];
			$v[$arr]['rate']       = $row['rate'];
			$v[$arr]['note']       = $row['note'];
			$v[$arr]['isDelete']   = intval($row['isDelete']);
		}
		$data['data']['items']     = is_array($v) ? $v : '';
		$data['data']['totalsize'] = $this->mysql_model->get_count(CURRENCY,'(isDelete=0)');
		die(json_encode($data));	 
	}
	
	//新增
	public function add(){
		$this->common_model->checkpurview(78);
		$data = str_enhtml($this->input->post(NULL,TRUE));
		if (count($data)>0) {
		    $data = $this->validform($data);
			$sql  = $this->mysql_model->insert(CURRENCY,elements(array('code','name',					
			'symbol','rate','note'),$data));
			if ($sql) {
				$data['id'] = $sql;
				$this->common_model->logs('新增币种:'.$data['name']);  
				die('{"status":200,"msg":"success","data":{"default":false,"guid":"","id":'.$sql.',"isdelete":0
						,"name":"'.$data['name'].'"
						,"rate":"'.$data['rate'].'"
						,"code":"'.$data['code'].'"
						,"symbol":"'.$data['symbol'].'"
						,"note":"'.$data['note'].'"
						}}');
				str_alert(200,'success',$data);
			}
		}
		str_alert(-1,'添加失败');
	}
	
	//修改
	public function update(){
		$this->common_model->checkpurview(79);
		$id   = intval($this->input->post('id',TRUE));
		$data = str_enhtml($this->input->post(NULL,TRUE));
		if (count($data)>0) {
			$data = $this->validform($data);
			$sql  = $this->mysql_model->update(CURRENCY,elements(array('code','name',					
			'symbol','rate','note'),$data),'(id='.$id.')');
			if ($sql) {
				str_alert(200,'success',$data);
			}
		}
		str_alert(-1,'更新失败');
	}
	
	//删除
	public function delete(){
		$this->common_model->checkpurview(80);
		$id = intval($this->input->post('id',TRUE));
		$data = $this->mysql_model->get_row(CURRENCY,'(id='.$id.') and (isDelete=0)'); 
		if (count($data)>0) {
		    $info['isDelete'] = 1;
			$sql = $this->mysql_model->update(CURRENCY,$info,'(id='.$id.')');        
		    if ($sql) {
				$this->common_model->logs('删除币种:ID='.$id.' 名称:'.$data['name']);
				str_alert(200,'success',array('msg'=>'成功删除','id'=>'['.$id.']'));
			}
		}
		str_alert(-1,'删除失败');
	}
	
	//公共验证
	private function validform($data) {
        !isset($data['name']) || strlen($data['name']) < 1 && str_alert(-1,'币种名称不能为空');
		$where = isset($data['id']) ? ' and (id<>'.$data['id'].')' :'';
		$this->mysql_model->get_count(CURRENCY,'(isDelete=0) and (name="'.$data['name'].'")'.$where) && str_alert(-1,'币种名称重复');
		return $data;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */