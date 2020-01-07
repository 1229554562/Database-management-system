<?php
namespace app\admin\controller;
use cmf\controller\AdminBaseController;
use app\admin\model\NewsModel;
use think\Db;
 class  NewsController extends AdminBaseController{
     public function index(){
         $search= input('get.search');
         $map['title']= array("like","%$search%");
         $data = db('news')->where($map)->order('id desc')->paginate(10);
         //设置分页
          $page = $data->render();
          //统计当前分页的数据
          $pagecount = $data->count();

         $count = db('news')->count();
         $this->assign([
             'data'=>$data,
             'count'=>$count,
             'page'=>$page,
             'pagecount'=>$pagecount
         ]);
         return view();
     }

     // 添加
     public function add(){
         if(Request()->isPost()){
             $data=input('post.');
//             print_r($data);
//             die;
             $NewsModel=new NewsModel;
             //验证
             $validate =\think\Loader::validate('News');
             if (!$validate->scene('add')->check($data)) {
                 //return $arr = ['error' => $validate->getError(), 'code' => 1];
                  $this->error($validate->getError(),url('index'));
             }else{
                 if($_FILES['img']['tmp_name']){
                     $file = request()->file('img');
                     $info = $file->move("./static/uploads/news");
                     if($info){
                         $NewsModel['img']=$info->getSaveName();
                     }else{
                         $this->error($file->getError());
                     }
                 }
                 $data['time']=time();
                 $res=$NewsModel->save($data);
                 if($res){
                     Db::commit();
                     $adminId = cmf_get_current_admin_id();
                     cmf_admin_log($adminId,"添加新闻");
                     $this->success('添加成功',url('index'));
                 }else{
                     $this->error('添加失败');
                 }
             }

         }else{
             return view();
         }
     }
     // 删除
     public function del($id){

         $article=new NewsModel();
         $res=$article->delA($id);
         if($res){
             Db::commit();
             $adminId = cmf_get_current_admin_id();
             cmf_admin_log($adminId,"修改新闻");
             $this->success('删除成功',url('index'));
         }else{
             $this->error('删除失败');
         }
     }
     // 修改
     public function update($id){
//         $mobile= 15550784456;
//         $ty =request()->ip();
//         echo $ty;
//         die;
         if(request()->isPost()){
             $article=new NewsModel();
             $data=input('post.');
             if($_FILES['img']['tmp_name']){
                 $file = request()->file('img');
                 $info = $file->move("./static/uploads/news");
                 if($info){
                     unlink("./static/uploads/news/{$data['img']}");
                     $data['img']=$info->getSaveName();
                 }else{
                     $this->error($file->getError());
                 }
             }
             $data['time']=time();
             $res=$article->save($data,['id'=>$id]);
             if($res){
                 Db::commit();
                 $adminId = cmf_get_current_admin_id();
                 cmf_admin_log($adminId,"修改新闻");
                 $this->success('修改成功',url('index'));
             }else{
                 $this->error('修改失败');
             }
         }else{
            $article = db('news')->find($id);
            $this->assign('article',$article);
             return view();
         }

     }
     //状态的改变
    public function ajax_status(){
        $data = input('post.');
        $res = db('news')->where(array('id'=>$data['id']))->update(['status'=>$data['status']]);
        if ($res){
            echo 1;
        }else{
            echo 2;
        }
    }
 }