<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 雷 < 1229554562@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AdminLogController extends AdminBaseController
{

    public function index()
    {   
        $where = [];
        if(request()->isGet()){
            $res = $this->request->param();
            if(!empty($res['uname'])){
                $where['admin_name'] = $res['uname'];
            }
            if(!empty($res['ulog'])){
                $bet = $res['ulog'];
                $where['admin_log'] = ['like',"%$bet%"];
            }
        }
        $res = Db::name('admin_log')->where($where)->order('time','desc')->paginate(8,false,['query'=>request()->param()]);
        $this->assign('res',$res);
        return $this->fetch();
    }


}