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
class ActinfoController extends HomeBaseController
{
    public function actinfo()
    {
        $data =  input('');
      
       $list = db('activity')->where('id',$data['id'])->select();
       $aa = Session::get('user');
       $this->assign('user',$aa['mobile']);
       $this->assign('list',$list);
       return $this->fetch(':actinfo');
    }

  






}
