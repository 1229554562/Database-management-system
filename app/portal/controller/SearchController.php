<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\Session;


class SearchController extends HomeBaseController
{
  
     public function search()
    {  
        $key=input('name');
        if($key==''){
            $this->error('关键字不能为空');
        }
       else{
        $where['unitname|legalperson|industry'] = ['like', '%' . $key . '%'];
        $data=db('enterprise')->where($where)->select();
        $count=db('enterprise')->where($where)->count();
        $list=db('industry')->select();
        $aa = Session::get('user');
        $this->assign('user',$aa['mobile']);
        $this->assign('list',$list);
        $this->assign('data',$data);
        $this->assign('count',$count);
        return $this->fetch(':search');
       }
     
    
    }
    // public function test(){
    //     $name=input();
    //     $count=db('enterprise')->count();
    //     $data=db('enterprise')->where('industry',$name['name'])->select();
    //     $this->assign('count',$count);
    //     $this->assign('data',$data);
    //     $this->assign('name',$name['name']);

    //     return $this->fetch(':search');
    // }
    // public function money(){
    //     $min=input('min',0);
    //     // $max=input('max',0,'int');
    //     $max = $this->request->param('max',0);
    //     //var_dump($max);die;
    //     $count=db('enterprise')->count();
    //     $where['registermany'] = ['between',[$min,$max]];
    //     $data=db('enterprise')->where($where)->select();
    //     $this->assign('count',$count);
    //     $this->assign('data',$data);
    //     return $this->fetch(':search');
    // }
    // public function max(){
    //     $max=input('max');
    //     $count=db('enterprise')->count();
    //     $data=db('enterprise')->where('registermany','GT',$max)->select();
    //     $this->assign('count',$count);
    //     $this->assign('data',$data);
    //     return $this->fetch(':search');


    // }
    // //活动
    // public function activity(){
    //    $name=input('name');
    //    $count=db('enterprise')->count();
    //    $data=db('enterprise')->where('activities',$name)->select();
    //     $this->assign('count',$count);
    //     $this->assign('data',$data);
    //     return $this->fetch(':search');
    // }
    // //入园情况
    // public function status(){
    //     header("Content-Type: text/html;charset=utf-8");
    //     $name=input('name');
    //     $count=db('enterprise')->count();
    //     $data=db('enterprise')->where('admission',$name)->select();
    //     $this->assign('count',$count);
    //     $this->assign('data',$data);
    //     return $this->fetch(':search');
    // }
    // //活动搜索
    // public function activitys(){

    //     $a=input('id');
    //     $data=db('activities')->where('id',$a)->select();

    //     $this->assign('data',$data);
    //     return $this->fetch('/actinfo');
    // }
/*
 * 行业industry
活动name
入园情况in_status
最小金额$min;
最大金额$max
*/
public function search1(){
    $all=input('n');//全部
    $industry=input('name');//行业
    $act=input('act');//入园活动
    $status=input('status');//入园情况
    $min=input('min');//最小资金
    $max=input('max');//最大资金
   // dump($status);die;
    if(!empty($industry)){
        $list=db('industry')->select();
        $data=db('enterprise')->where('industry',$industry)->select();
        $count=db('enterprise')->where('industry',$industry)->count();
    }
    else if(!empty($act)){
        $list=db('industry')->select();
        $act_id=db('activity')->where('title',$act)->value('id');
        $unitname=db('activity_enterprise')->where('a_id',$act_id)->select();
        foreach ($unitname as $key => $value) {
            $data = db('enterprise')->where('id',$value['e_id'])->select(); 
            $count= db('enterprise')->where('id',$value['e_id'])->count();
        }
   
        //$data = db('enterprise')->where('unitname',$unitname)->select();  
        $count=db('enterprise')->where('unitname',$unitname)->count();
    }
    else if(!empty($status)){
        $list=db('industry')->select();
        $data=db('enterprise')->where('admission',$status)->select();
        $count=db('enterprise')->where('admission',$status)->count();
    }else if(!empty($min) and !empty($max)){
        $list=db('industry')->select();
        $where['registermany'] = ['between',[$min,$max]];
        $data=db('enterprise')->where($where)->select();
        $count=db('enterprise')->where($where)->count();
    }else if(!empty($max)){
        $list=db('industry')->select();
        $data=db('enterprise')->where('registermany','GT',$max)->select();
        $count=db('enterprise')->where('registermany','GT',$max)->count();
    }else if(!empty($all)){
        $list=db('industry')->select();
     
        $data=db('enterprise')->select();
        $count=db('enterprise')->count();
    }

    $aa = Session::get('user');
    $this->assign('user',$aa['mobile']);
    $this->assign('list',$list);
    $this->assign('count',$count);
    $this->assign('data',$data);
    return $this->fetch(':search');
}





}
