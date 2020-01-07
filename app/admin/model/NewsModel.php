<?php
namespace app\admin\model;
use think\Model;
class NewsModel extends Model{

    // 删除
    public function delA($id){
        $data=$this->find($id);
        unlink("./static/uploads/news/{$data['img']}");
        return $this::destroy($id);
    }
}