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
use think\Session;
class NewsinfoController extends HomeBaseController
{
    public function newsinfo()
    {
        $data =  input('');
        $aa=db('news')->where('id',$data['id'])->field('time')->select();
        foreach ($aa as $key => $value) {
            $date = $value['time'];
           $time= date("Y-m-d H:i:s",$date);

        }
        
       $list = db('news')->where('id',$data['id'])->select();
       $aa = Session::get('user');
       $this->assign('user',$aa['mobile']);
       $this->assign('list',$list);
       $this->assign('aa',$time);
       return $this->fetch(':newsinfo');
    }

  






}
