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
use think\Validate;
use app\user\model\UserModel;
use think\Session;

class IndexController extends HomeBaseController
{
    

    public function index()
    {
        $list=db('activity')->limit(3)->select();
        $news=db('news')->select();
        $aa=db('news')->field('time')->select();
        foreach ($aa as $key => $value) {
            $date = $value['time'];
           $y =  date("Y-m",$date);
          
           $d = date("d",$date);

        }
        $this->assign('d',$d);
        $this->assign('y',$y);
       
        $data = db('industry')->select();
        $act=db('activity')->select();
        $this->assign('data',$data);
        $this->assign('list',$list);
        $this->assign('news',$news);
        $this->assign('act',$act);
        $aa = Session::get('user');

       $this->assign('user',$aa);
       return $this->fetch(':index');
    }
}
