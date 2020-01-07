<?php
namespace app\admin\model;

use think\Db;
use app\user\model\UserModel;
use think\Model;
use think\Cache;

class AdminLogModel extends Model{
    
    public function addlog($userId,$remark){
        $userId = !empty($userId) ? $userId : '1';
        $user = new UserModel();
        $userInfo = $user::get($userId);
        $data['admin_name'] = $userInfo['user_login'];
        $data['admin_log'] = $remark;
        $data['time'] = time();
        $data['ip'] = get_client_ip();
        $this->insert($data);
    }

}