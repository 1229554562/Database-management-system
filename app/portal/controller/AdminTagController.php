<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:kane < chengjin005@163.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\portal\model\PortalTagModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Loader;
use app\portal\model\PortalCategoryModel;
use DateTime;
use  PHPExcel_Shared_Date;
/**
 * Class AdminTagController 标签管理控制器
 * @package app\portal\controller
 */
class AdminTagController extends AdminBaseController
{
    /*
    *财务
    */
    public function finance()
    {
        $where = [];
        if(request()->isPost()){
           $staff =  $this->request->param();
           if(!empty($staff['keyword'])){
                $keyword = trim($staff['keyword']);
                $where['unitname'] = ['like',"%$keyword%"];
           }
           if(!empty($staff['start_time'])&&!empty($staff['end_time'])){
                $start_time =  strtotime(trim($staff['start_time']));
                $de_start_time =  strtotime(trim($staff['end_time']));
                $end_time  =  $de_start_time+60*60*24-1;
                $where['regtime'] = ['between',[$start_time,$end_time]];
           }
        }
        $query = Db::name('enterprise')
        ->where('del_type',0)
        ->where($where)
        ->order('id desc')
        ->paginate(8,false,['query'=>request()->param()]);
    $this->assign('query',$query);
        return $this->fetch();
    }

    /**
     * 利润
    */
    /*
    *财务
    */
    public function profit()
    {
        $where = [];
        if(request()->isPost()){
           $staff =  $this->request->param();
           if(!empty($staff['keyword'])){
                $keyword = trim($staff['keyword']);
                $where['unitname'] = ['like',"%$keyword%"];
           }
           if(!empty($staff['start_time'])&&!empty($staff['end_time'])){
                $start_time =  strtotime(trim($staff['start_time']));
                $de_start_time =  strtotime(trim($staff['end_time']));
                $end_time  =  $de_start_time+60*60*24-1;
                $where['regtime'] = ['between',[$start_time,$end_time]];
           }
           if(!empty($staff['regmany_start'])&&!empty($staff['regmany_end'])){
                $regmany_start = trim($staff['regmany_start']);
                $regmany_end   = trim($staff['regmany_end']);
                $where['regmany'] = ['between',[$regmany_start,$regmany_end]];

           }
        }
        $query = Db::name('enterprise')
        ->where('del_type',0)
        ->where($where)
        ->order('id desc')
        ->paginate(8,false,['query'=>request()->param()]);
    $this->assign('query',$query);
        return $this->fetch();
    }
    /**
     * cash_flow
    */
    public function cash_flow()
    {
        $where = [];
        if(request()->isPost()){
           $staff =  $this->request->param();
           if(!empty($staff['keyword'])){
                $keyword = trim($staff['keyword']);
                $where['unitname'] = ['like',"%$keyword%"];
           }
           if(!empty($staff['start_time'])&&!empty($staff['end_time'])){
                $start_time =  strtotime(trim($staff['start_time']));
                $de_start_time =  strtotime(trim($staff['end_time']));
                $end_time  =  $de_start_time+60*60*24-1;
                $where['regtime'] = ['between',[$start_time,$end_time]];
           }
           if(!empty($staff['regmany_start'])&&!empty($staff['regmany_end'])){
                $regmany_start = trim($staff['regmany_start']);
                $regmany_end   = trim($staff['regmany_end']);
                $where['regmany'] = ['between',[$regmany_start,$regmany_end]];

           }
        }
        $query = Db::name('enterprise')
        ->where('del_type',0)
        ->where($where)
        ->order('id desc')
        ->paginate(8,false,['query'=>request()->param()]);
        $this->assign('query',$query);
        return $this->fetch();
    }
    /**
     * 文章标签管理
     * @adminMenu(
     *     'name'   => '文章标签',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章标签',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $portalTagModel = new PortalTagModel();
        $tags           = $portalTagModel->paginate();

        $this->assign("arrStatus", $portalTagModel::$STATUS);
        $this->assign("tags", $tags);
        $this->assign('page', $tags->render());
        return $this->fetch();
    }
    /**
     * 测试
    */
    public function ceshi()
    {
        if($this->request->isPost()){
            $excel_name = $this->request->param('excel_name');
            $excel_url = $this->request->param('excel_url');
            if(!empty($excel_name) && !empty($excel_url)){

                Loader::import('PHPExcel.Classes.PHPExcel');
                Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');
                Loader::import('PHPExcel.Classes.PHPExcel.Reader.Excel5');
                ini_set("memory_limit","1024M");
                set_time_limit(60); //执行时间为60秒
                $filename = ROOT_PATH.'public/upload/'.$excel_url;  //文件路径
                $extension = cmf_get_file_extension($excel_url);    //文件扩展名
                if($extension == 'xlsx'){
                    $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');  //加载文件内容,编码utf-8
                }else if($extension == 'xls'){
                    $objReader =\PHPExcel_IOFactory::createReader('Excel5');
                    $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');  //加载文件内容,编码utf-8
                }else{
                    $this->error('请上传Excel格式的文件！','');
                }

                $excel_array=$objPHPExcel->getsheet(0)->toArray();   //转换为数组格式              
                array_shift($excel_array);

                if(empty($excel_array[27][1])||strtotime($excel_array[27][1])==false){

                    $this->error('文件导入失败');
                }
                $times = DateTime::createFromFormat('m-d-y',$excel_array[27][1]);
                if($times){
                    $times_array = get_object_vars($times);
                }
                $data = [
                    'monetary_fund'             =>      $excel_array[0][1],
                    'ord_fixed_assets'          =>      $excel_array[0][3],
                    'short_Investment'          =>      $excel_array[1][1],
                    'sum_ord_fixed'             =>      $excel_array[1][3],
                    'rv_bill'                   =>      $excel_array[2][1],
                    'sum_assets'                =>      $excel_array[2][3],
                    'rv_paragraph'              =>      $excel_array[3][1],
                    'short_loan'                =>      $excel_array[3][3],
                    'pia_paragraph'             =>      $excel_array[4][1],
                    'sd_payable'                =>      $excel_array[4][3],
                    'rv_thigh'                  =>      $excel_array[5][1],
                    'sd_paragraph'              =>      $excel_array[5][3],
                    'rv_Interest'               =>      $excel_array[6][1],
                    'ca_paragraph'              =>      $excel_array[6][3],
                    'ord_receivables'           =>      $excel_array[7][1],
                    'sd_workers_pay'            =>      $excel_array[7][3],
                    'stock'                     =>      $excel_array[8][1],
                    'sd_taxation'               =>      $excel_array[8][3],
                    'raw_material'              =>      $excel_array[9][1],
                    'sd_interest'               =>      $excel_array[9][3],
                    'products_in'               =>      $excel_array[10][1],
                    'sd_profit'                 =>      $excel_array[10][3],
                    'sk_commodity'              =>      $excel_array[11][1],
                    'ord_sd_payment'            =>      $excel_array[11][3],
                    'turn_material'             =>      $excel_array[12][1],
                    'ord_flow_payment'          =>      $excel_array[12][3],
                    'ord_flow_asets'            =>      $excel_array[13][1],
                    'sum_flow_payment'          =>      $excel_array[13][3],
                    'sum_flow_asets'            =>      $excel_array[14][1],
                    'long_loan'                 =>      $excel_array[14][3],
                    'long_bond'                 =>      $excel_array[15][1],
                    'long_payment'              =>      $excel_array[15][3],
                    'long_thigh'                =>      $excel_array[16][1],
                    'deferred_profit'           =>      $excel_array[16][3],
                    'fixed_money_primary'       =>      $excel_array[17][1],
                    'ord_fixed_liabilities'     =>      $excel_array[17][3],
                    'cum_depreciation'          =>      $excel_array[18][1],
                    'sum_fixed_liabilities'     =>      $excel_array[18][3],
                    'fixed_primary_value'       =>      $excel_array[19][1],
                    'sum_liabilities'           =>      $excel_array[19][3],
                    'being_engineering'         =>      $excel_array[20][1],
                    'paid_capital'              =>      $excel_array[20][3],
                    'engineering_material'      =>      $excel_array[21][1],
                    'capital_surplus'           =>      $excel_array[21][3],
                    'fixed_money_clear'         =>      $excel_array[22][1],
                    'surplus_reserves'          =>      $excel_array[22][3],
                    'biology_money'             =>      $excel_array[23][1],
                    'unallocated_profit'        =>      $excel_array[23][3],
                    'hide_assets'               =>      $excel_array[24][1],
                    'sum_rights_interests'      =>      $excel_array[24][3],
                    'development_out'           =>      $excel_array[25][1],
                    'sum_liabilities_equity'    =>      $excel_array[25][3],
                    'long_pending'              =>      $excel_array[26][1],
                    'time'                      =>      $times==false? strtotime(date('Y-m',strtotime($excel_array[27][1]))):strtotime(date('Y-m',strtotime($times_array['date']))),
                    'qid'               =>      session('cwid')
                ];
                $up = Db::name('asset_liability')->where('time',$data['time'])->where('qid',$data['qid'])->find();
                if($up){
                    $updata = Db::name('asset_liability')->where('time',$data['time'])->where('qid',$data['qid'])->update($data);
                    if($updata){
                        $this->success('更新成功');
                    }else{
                        $this->error('更新失败');
                    }
                }
                if(Db::name('asset_liability')->insert($data)){
                    $this->success('导入数据成功');
                }else{
                    $this->error('导入文件失败');
                }
              
            }else{
                $this->error('文件导入失败!');
            }
        }
    }
     /**
     * 查看详情
    */
    public  function see()
    {   
        $id = $this->request->param();
        $see =  Db::name('enterprise')->where('id',$id['id'])->find();
        $this->assign('see',$see);

        $season = ceil(date('n')); //    '2019/4'
        $sea = '';
        for ($i=$season;$i>4;$i--){
            $sea .= date('Y').'/'.$i.',';
            if ($i == 1){
                for($j=4;$j>0;$j--){
                    $sea .= (date('Y')-1).'/'.$j.',';
                    if ($j == 1){
                        for($n=4;$n>0;$n--){
                            $sea .= (date('Y')-2).'/'.$n.',';
                        }
                    }
                }
            }
        }
        $eight_sea = array_slice(explode(',',$sea),0,8);

         foreach($eight_sea as $k=>$v){

            $money[$k]['business_income'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.qid',$id['id'])
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('business_income');
           
            $money[$k]['operating_cost'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.qid',$id['id'])
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('operating_cost');

            $money[$k]['total_profit'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.qid',$id['id'])
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('total_profit');
        
            $money[$k]['income_tax'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.qid',$id['id'])
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('income_tax');

        //============================================
            $cashf[$k]['net_cash_flow'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.qid',$id['id'])
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('net_cash_flow');
            
            $cashf[$k]['investment_flow'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.qid',$id['id'])
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('investment_flow');
            
            $cashf[$k]['net_cash_activities'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.qid',$id['id'])
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('net_cash_activities');
            
            $cashf[$k]['exchange_rate_effect'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.qid',$id['id'])
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('exchange_rate_effect');

            //==============================================
            $asset[$k]['sum_assets'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.qid',$id['id'])
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_assets');
            
            $asset[$k]['sum_liabilities'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.qid',$id['id'])
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_liabilities');
            
            $asset[$k]['sum_rights_interests'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.qid',$id['id'])
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_rights_interests');
            
            $asset[$k]['sum_liabilities_equity'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.qid',$id['id'])
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_liabilities_equity');
            // $money[$k]['assets'] = Db::name('enter_operate')
            //     ->where('enter_id',$id['id'])
            //     ->where('time',$v)
            //     ->value('assets');  // 上上个季度资产总额
        //     $money[$k]['tax'] = Db::name('enter_operate')
        //         ->where('enter_id',$id['id'])
        //         ->where('time',$v)
        //         ->value('tax');  // 上上个季度资产总额
        //     $money[$k]['sales'] = Db::name('enter_operate')->alias("op")
        //         ->where('enter_id',$id['id'])
        //         ->where('time',$v)
        //         ->value('sales');  // 上上季度营业总收入
        //     $money[$k]['profit'] = Db::name('enter_operate')
        //         ->where('enter_id',$id['id'])
        //         ->where('time',$v)
        //         ->value('profit');  // 上上季度总利润
         }
        // $mtime = strtotime(date("Y-m", strtotime("-8 month")));
        // $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));//获取本月月末的时间戳
        // $where = [];
        // $where['p.time'] = ['between time',[$mtime,$endThismonth]];
        // $wherecf['cf.time']  = ['between time',[$mtime,$endThismonth]];
        // $wherea['a.time']  = ['between time',[$mtime,$endThismonth]];
        // //=====================利润


        $this->assign('money',json_encode($money));
        $this->assign('cashf',json_encode($cashf));
        $this->assign('asset',json_encode($asset));
        $this->assign('season',json_encode($eight_sea));
        return $this->fetch();
    }

     /**
     * 企业员工页面
    */
    public function staff()
    {   
        $where = [];
        if(request()->isPost()){
           $staff =  $this->request->param();
           if(!empty($staff['keyword'])){
                $keyword = trim($staff['keyword']);
                $where['unitname'] = ['like',"%$keyword%"];
           }
           if(!empty($staff['start_time'])&&!empty($staff['end_time'])){
                $start_time =  strtotime(trim($staff['start_time']));
                $de_start_time =  strtotime(trim($staff['end_time']));
                $end_time  =  $de_start_time+60*60*24-1;
                $where['regtime'] = ['between',[$start_time,$end_time]];
           }
           if(!empty($staff['regmany_start'])&&!empty($staff['regmany_end'])){
                $regmany_start = trim($staff['regmany_start']);
                $regmany_end   = trim($staff['regmany_end']);
                $where['regmany'] = ['between',[$regmany_start,$regmany_end]];

           }
        }
        $query = Db::name('enterprise')
            ->where('del_type',0)
            ->where($where)
            ->order('id desc')
            ->paginate(8,false,['query'=>request()->param()]);
        $this->assign('query',$query);
        return $this->fetch();
    }

    /**
     * 添加文章标签
     * @adminMenu(
     *     'name'   => '添加文章标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章标签',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $portalTagModel = new PortalTagModel();
        $this->assign("arrStatus", $portalTagModel::$STATUS);
        return $this->fetch();
    }
   
    /**
     * 添加文章标签提交
     * @adminMenu(
     *     'name'   => '添加文章标签提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章标签提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {

        $arrData = $this->request->param();

        $portalTagModel = new PortalTagModel();
        $portalTagModel->isUpdate(false)->allowField(true)->save($arrData);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 更新文章标签状态
     * @adminMenu(
     *     'name'   => '更新标签状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '更新标签状态',
     *     'param'  => ''
     * )
     */
    public function upStatus()
    {
        $intId     = $this->request->param("id");
        $intStatus = $this->request->param("status");
        $intStatus = $intStatus ? 1 : 0;
        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }

        $portalTagModel = new PortalTagModel();
        $portalTagModel->isUpdate(true)->save(["status" => $intStatus], ["id" => $intId]);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 删除文章标签
     * @adminMenu(
     *     'name'   => '删除文章标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除文章标签',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        $portalTagModel = new PortalTagModel();

        $portalTagModel->where(['id' => $intId])->delete();
        Db::name('portal_tag_post')->where('tag_id', $intId)->delete();
        $this->success(lang("DELETE_SUCCESS"));
    }
}
