<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Right extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->common_model->checkpurview(82);
    }

    //用户数检测
    public function isMaxShareUser()
    {
        die('{"status":200,"data":{"totalUserNum":10000,"shareTotal":1},"msg":"success"}');
    }

    //用户列表
    public function queryAllUser()
    {
        $v = array();
        $data['status'] = 200;
        $data['msg'] = 'success';
        $list = $this->mysql_model->get_results(STAFF, '(isDelete!=1) order by roleid');
        foreach ($list as $arr => $row) {
            $v[$arr]['share'] = intval($row['status']) > 0 ? true : false;
            $v[$arr]['admin'] = $row['roleid'] > 0 ? false : true;
            $v[$arr]['userId'] = intval($row['id']);
            $v[$arr]['isCom'] = intval($row['status']);
            $v[$arr]['role'] = intval($row['roleid']);
            $v[$arr]['userName'] = $row['userName'];
            $v[$arr]['realName'] = $row['name'];
            $v[$arr]['shareType'] = intval($row['status']);
            $v[$arr]['mobile'] = $row['mobile'];
        }
        $data['data']['items'] = $v;
        $data['data']['shareTotal'] = $this->mysql_model->get_count(STAFF);
        $data['data']['totalsize'] = $data['data']['shareTotal'];
        $data['data']['corpID'] = 0;
        $data['data']['totalUserNum'] = 1000;
        die(json_encode($data));
    }

    //判断用户名是否存在
    public function queryUserById()
    {
        $uid = $_REQUEST['id'];
        if (!empty($uid)) {
            $user = $this->mysql_model->get_row(STAFF,
                "(id='$uid' and isDelete!=1)");
            if (count($user) > 0) {
                $info['share'] = true;
                $info['email'] = $user['email'];
                $info['userId'] = $user['id'];
                $info['userMobile'] = $user['mobile'];
                $info['userName'] = $user['userName'];
                $info['realName'] = $user['name'];

                str_alert(200, 'success', $info);
            }
            str_alert(502, '用户名不存在');
        }
        str_alert(502, '用户名不存在');
    }

    //判断用户名是否存在
    public function queryUserByName()
    {
        $data = str_enhtml($this->input->get_post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            $user = $this->mysql_model->get_row(STAFF,
                "(userName='$data[userName]' and isDelete!=1)");
            if (count($user) > 0) {
                $info['share'] = true;
                $info['email'] = '';
                $info['userId'] = $user['id'];
                $info['userMobile'] = $user['mobile'];
                $info['userName'] = $user['userName'];
                str_alert(200, 'success', $info);
            }
            str_alert(502, '用户名不存在');
        }
        str_alert(502, '用户名不存在');
    }

    //新增用户
    public function adduser()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userName']) || strlen($data['userName']) < 1 && str_alert(-1, '用户名不能为空');
            !isset($data['password']) || strlen($data['password']) < 1 && str_alert(-1, '密码不能为空');
            $this->mysql_model->get_count(STAFF, "(userName='$data[userName]' and isDelete!=1)") > 0 && str_alert(-1, '用户名已经存在');
            $this->mysql_model->get_count(STAFF, "(mobile='$data[userMobile]' and isDelete!=1)") > 0 && str_alert(-1, '该手机号已被使用');

            //jason.xie 改为系统自建用户编号
            $count = $this->mysql_model->query(STAFF,"SELECT MAX(id) as numrows FROM ".STAFF,1);
            $data['number'] = '95ZYEP'.str_pad($count[numrows]+1,4,"0",STR_PAD_LEFT);
            //end

            $info = array(
                'userName' => $data['userName'],
                'userpwd' => md5($data['password']),
                'mobile' => $data['userMobile'],
                'name' => $data['realName'],
                'email' => $data['email'],
                'number'=>$data['number']
            );
            $sql = $this->mysql_model->insert(STAFF, $info);
            if ($sql) {
                $this->common_model->logs('新增用户:' . $data['userName']);
                die('{"status":200,"msg":"注册成功","userName":"' . $data['userName'] . '"}');
            }
            str_alert(-1, '添加失败');
        }
        str_alert(-1, '添加失败');
    }

    //删除用户
    public function deleteuser()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userName']) || strlen($data['userName']) < 1 && str_alert(-1, '用户名不能为空');
            $sql = $this->mysql_model->update(STAFF, array('isDelete' => 1), "(userName='$data[userName]' and roleid!=0)");
            if ($sql) {
                $this->common_model->logs('删除用户:' . $data['userName']);
                die('{"status":200,"msg":"删除成功","userName":"' . $data['userName'] . '"}');
            }
            str_alert(-1, '删除失败');
        }
        str_alert(-1, '删除失败');
    }

    //编辑用户
    public function edituser()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userName']) || strlen($data['userName']) < 1 && str_alert(-1, '用户名不能为空');
            $info = array('userName' => $data['userName'],
                'name' => $data['realName'],
                'mobile' => $data['userMobile'],
                'email' => $data['email']
            );
            if (!empty($data['password'])) {
                $info['userpwd'] = $data['password'];
            };
            $sql = $this->mysql_model->update(STAFF, $info, "(id='$data[id]' and roleid!=0)");
            if ($sql) {
                $this->common_model->logs('编辑用户:' . $data['userName']);
                die('{"status":200,"msg":"编辑成功","userName":"' . $data['userName'] . '"}');
            }
            str_alert(-1, '编辑失败');
        }
        str_alert(-1, '编辑失败');
    }

    //更新权限
    public function addrights2Outuser()
    {
        $data = str_enhtml($this->input->get_post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userName']) || strlen($data['userName']) < 1 && str_alert(-1, '用户名不能为空');
            !isset($data['rightid']) && str_alert(-1, '参数错误');
            $sql = $this->mysql_model->update(STAFF, array('lever' => $data['rightid']),
                "(userName='$data[userName]' and isDelete!=1)");
            if ($sql) {
                $this->common_model->logs('更新权限:' . $data['userName']);
                str_alert(200, '操作成功');
            }
            str_alert(-1, '操作失败');
        }
        str_alert(-1, '添加失败');
    }

    //详细权限设置
    public function queryalluserright()
    {
        $userName = $_REQUEST['userName'];//str_enhtml($this->input->get_post('userName',TRUE));
        if (strlen($userName) > 0) {
            $lever = $this->mysql_model->get_row(STAFF,
                "(userName='$userName' and isDelete!=1)",
                'lever');
            $lever = strlen($lever) > 0 ? explode(',', $lever) : array();
        } else {
            $lever = array();
        }


        $v = array();
        $data['status'] = 200;
        $data['msg'] = 'success';
        $data['data']['totalsize'] = $this->mysql_model->get_count(MENU, '(isDelete=0)');
        $list = $this->mysql_model->get_results(MENU, '(isDelete=0) order by path');
        $name = array_column($list, 'name', 'id');
        foreach ($list as $arr => $row) {
            $v[$arr]['fobjectid'] = $row['parentId'] > 0 ? $row['parentId'] : $row['id'];
            $v[$arr]['fobject'] = $row['parentId'] > 0 ? @$name[$row['parentId']] : $row['name'];
            $v[$arr]['faction'] = $row['level'] > 1 ? $row['name'] : '查询';
            $v[$arr]['fright'] = in_array($row['id'], $lever) ? 1 : 0;
            $v[$arr]['frightid'] = intval($row['id']);
        }
        $data['data']['items'] = $v;
        die(json_encode($data));
    }

    //停用
    public function auth2UserCancel()
    {
        $data = str_enhtml($this->input->get_post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userName']) && str_alert(-1, '用户名不能为空');
            $data['userName'] == 'admin' && str_alert(-1, '管理员不可操作');
            $sql = $this->mysql_model->update(STAFF, array('status' => 0),
                "(userName='$data[userName]' and isDelete!=1)");
            if ($sql) {
                $this->common_model->logs('用户停用:' . $data['userName']);
                str_alert(200, 'success', $data);
            }
            str_alert(-1, '停用失败');
        }
        str_alert(-1, '停用失败');

    }

    //启用
    public function auth2User()
    {
        $data = str_enhtml($this->input->get_post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userName']) && str_alert(-1, '用户名不能为空');
            $data['userName'] == 'admin' && str_alert(-1, '管理员不可操作');
            $sql = $this->mysql_model->update(STAFF, array('status' => 1),
                "(userName='$data[userName]' and isDelete!=1)");
            if ($sql) {
                $this->common_model->logs('用户启用:' . $data['userName']);
                str_alert(200, 'success', $data);
            }
            str_alert(-1, '启用失败');
        }
        str_alert(-1, '启用失败');
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */