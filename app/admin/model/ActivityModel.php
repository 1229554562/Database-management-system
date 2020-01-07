<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class ActivityModel extends Model{
    public function addM($arr){
        if ($this->save($arr)){
            Db::commit();
            $adminId = cmf_get_current_admin_id();
            cmf_admin_log($adminId,"添加活动");
            return true;
        }else{
            return false;
        }
    }
    //删除
    public function delM($id)
    {
        Db::commit();
        $adminId = cmf_get_current_admin_id();
        cmf_admin_log($adminId,"删除活动");
        return $this::destroy($id);
    }
    /*public function delA($id){
        $data=$this->find($id);
//        unlink("./static/uploads/news/{$data['img']}");
        return $this::destroy($id);
    }*/
    //修改
    public function editM($arr){
        $arr['time']= strtotime('now');
        $res =  $this::update(['title'=>$arr['title'],'content'=>$arr['content'],'id'=>$arr['id'],'status'=>$arr['status'],'time'=>$arr['time']]);
        Db::commit();
        $adminId = cmf_get_current_admin_id();
        cmf_admin_log($adminId,"修改活动");
        return $res;
    }
}