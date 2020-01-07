<?php
namespace app\admin\controller;
use cmf\controller\AdminBaseController;
use app\admin\model\ActivityModel;
use think\Db;
class ActivityController extends AdminBaseController{
    public function index(){
    $seche = input("get.search");
    $map['title'] = array("like","%$seche%");
    $data = db('activity')->where($map)->order('id desc')->paginate(10);
    $count = db('activity')->where($map)->count();
        $page=$data->render();
    $this->assign([
        'dat'=>$data,
        'count'=>$count,
        'page'=>$page
    ]);
    /*
$page=$data->render();
    $this->assign('data',$data);
*/
    return view();
}
    // 添加
    public function ajax_add()

    {
//       $tio =  input('post.str');
//      $ex =  explode('&amp;',$tio);
//      print_r($ex);
//        die;
        $data = input('post.str');
        $data = htmlspecialchars_decode($data);
//        $arr = explode('&',$data);
        parse_str($data,$arr);
        $model = new ActivityModel();
        //验证
        $validate =\think\Loader::validate('Activity');
        if (!$validate->scene('add')->check($arr)){
            return $arr=['error'=>$validate->getError(),'code'=>1];
        }else{
            $arr['time']=strtotime('now');
            $res = $model->addM($arr);
            if ($res){
                $arr['id'] = $model->id;
                $this->assign('dat',$arr);
                return view();
            }
        }
    }
    //删除
    public function  ajax_del(){
        $id = input('post.id');
        $model = new ActivityModel();
        $res = $model->delM($id);
        if ($res){
            echo 1;
        }else{
            echo 2;
        }
    }
    //查找
    public  function ajax_find(){
        $id = input('post.id');
        $dat =db('activity')->find($id);
        $this->assign('dat',$dat);
        return view();
    }
    //更新
    public function ajax_update(){
        $data = $_POST['str'];
        parse_str($data,$arr);
        $model = new ActivityModel();
        $res = $model->editM($arr);
        if ($res){
            $date= db('activity')->find($arr['id']);
            $this->assign('dat',$date);
            return view();
        }else{
            echo 2;
        }
    }
    //批量删除
    public function ajax_delAll(){
        $model=new ActivityModel;
        $res=$model->delM(input("post.str"));
        return $res;
    }
    //状态改变
    public function ajax_status(){
        $data = input('post.');
        $res = db('activity')->where(array('id'=>$data['id']))->update(['status'=>$data['status']]);
        if ($res){
            Db::commit();
            $adminId = cmf_get_current_admin_id();
            cmf_admin_log($adminId,"修改状态");
            echo 1;
           
        }else{
            Db::commit();
            $adminId = cmf_get_current_admin_id();
            cmf_admin_log($adminId,"修改状态");
            echo 2;
        }
    }
    /**
     * Undocumented
     * addetp @活动添加企业
     *
     */
    public function addetp()
    {
        $res = $this->request->param();
        if(request()->isPost()){            
            $data=[
                'e_id'      =>      $res['enter'],
                'a_id'      =>      $res['aid'],
                'count'     =>      $res['num']
            ];
            $complex = Db::name('activity_enterprise')->where('e_id',$data['e_id'])->find();
            if($complex){
                $data['count'] += $complex['count'];
                Db::name('activity_enterprise')->where('e_id',$data['e_id'])->update($data);
            }
            $result = Db::name('activity_enterprise')->insert($data);
            if($result){
                $this->success('添加成功','admin/activity/index',['parent'=>1]);
            }else{
                $this->error('添加失败');
            }
        }

        $title = Db::name('activity')
        ->field('id,title')
        ->where('id',$res['id'])
        ->find();

        $enterprise = Db::name('enterprise')
        ->field('id,unitname')
        ->select();
        $this->assign('qy',$enterprise);
        $this->assign('title',$title);
        return $this->fetch('activity');
    }

    /**
     * Undocumented
     * addetp @查看添加企业
     *
     */
    public function see(){
        $seeid = $this->request->param();
        $upe_id = Db::name('activity_enterprise')->alias('ae')
        ->field('ae.unitname,e.unitname,e.id')
        ->where('ae.a_id',$seeid['id'])
        ->join('__ENTERPRISE__ e','e.unitname = ae.unitname','left')
        ->select();
        foreach($upe_id as $k=>$v){
            if(!empty($v['id'])){
                $value=[
                    'e_id'    =>      $v['id'] 
                ];
                Db::name('activity_enterprise')->where('unitname',$v['unitname'])->update($value);
            }            
        }
        $res = Db::name('activity_enterprise')->alias('ae')
        ->field('a.title,e.unitname,ae.count')
        ->where('ae.a_id',$seeid['id'])
        ->join('__ACTIVITY__ a','a.id = ae.a_id','right')
        ->join('__ENTERPRISE__ e','e.id = ae.e_id','right')
        ->select();
        $this->assign('res',$res);
        $this->assign('res_status',count($res) == 0 ? 0 : 1);
        return $this->fetch('see_activity');
    }
}
