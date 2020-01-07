<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 耝猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\model;

use think\Model;
use think\Db;

class EnterpriseModel extends Model
{
    protected static function init()
    {
        EnterpriseModel::event('before_insert', function ($add) {
            if ($_FILES['license']['tmp_name']) {
                $img = request()->file('license');
                $info = $img->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'portal' . DS . "image" );
                if($info){
                    $img =  'portal' . DS . "image" . DS .$info->getSaveName();
                    $add['license'] = $img;
                }else{
                    echo $info->getSaveName();
                    return false;
                }
            }
        });
    }

    public function qy_edit($edit)
    {

        if (isset($_FILES['license']['tmp_name'])&&$_FILES['license']['tmp_name']) {
            $image = request()->file('license');
            $edit_info = $image->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'portal' . DS . "image" );
            if($edit_info){
                $img =   'portal' . DS . "image" . DS . $edit_info->getSaveName();
            }else{
                echo $edit_info->getSaveName();
                return 3;
            }
        }else{
            $img = Db::name('enterprise')->where('credit_code',$edit['credit_code'])->value('license');
        }

        $enter_id = Db::name('enterprise')->field('id')->where('credit_code',$edit['credit_code'])->where('del_type',0)->find();
        if($enter_id['id'] != null){
            if ($enter_id['id'] != $edit['id']){
                return 2;
            }
        }

        $data = [
            'unitname'          =>      $edit['unitname'],
            'credit_code'       =>      $edit['credit_code'],
            'legalperson'       =>      $edit['legalperson'],
            'regtime'           =>      strtotime(trim($edit['regtime'])),
            'regmany'           =>      $edit['regmany'],
            'regplace'          =>      $edit['regplace'],
            'regauthority'      =>      $edit['regauthority'],
            'approval_date'     =>      $edit['approval_date'],
            'busterm_start'     =>      $edit['busterm_start'],
            'busterm_end'       =>      $edit['busterm_end'],
            'industry'          =>      $edit['industry'],
            'management'        =>      $edit['management'],
            'place'             =>      $edit['place'],
            'is_abnormal'       =>      $edit['is_abnormal'],
            'abnormal_mark'     =>      $edit['abnormal_mark'],
           
//            'add_time'          =>      time()
        ];
        if($img!=null){
            $data["license"] = $img;
        }else{
            $data["license"] = '';
        }
        $val = Db::name('enterprise')->where('id',$edit['id'])->update($data);
        if($val){
            return 1 ;
        }else{
            return 0;

        }
    }

}