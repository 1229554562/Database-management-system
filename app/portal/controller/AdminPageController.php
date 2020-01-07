<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\portal\model\PortalPostModel;
use app\portal\service\PostService;
use app\admin\model\ThemeModel;
use app\portal\model\EnterpriseModel;
use think\Db;
use think\Loader;
use think\Db\Query;
use think\Config;
use vendor\phpoffice\phpexcel\classes\PHPExcel;



class AdminPageController extends AdminBaseController
{

    /**
     * 页面管理
     * @adminMenu(
     *     'name'   => '页面管理',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '页面管理',
     *     'param'  => ''
     * )
     */

    public function index()
    {   
        $where = [];
        $having = '';
        $group = 0;
        if(request()->isPost()){
            $param = $this->request->param();

            if(!empty($param['keyword'])){
                $keyword = trim($param['keyword']);
                $where['unitname'] = ['like',"%$keyword%"];
            }

            if(!empty($param['start_time'])){
                $start_time = strtotime($param['start_time']);
                $where['regtime'] = ['gt',$start_time];
            }
            if(!empty($param['end_time'])){
                $end_time = strtotime($param['end_time']);
                $where['regtime'] = ['lt',$end_time];
            }

            if(!empty($param['start_time'])&&!empty($param['end_time'])){
                $start_time = strtotime(date('Y-m-d', strtotime(trim($param['start_time']))));
                $de_start_time =  strtotime(date('Y-m-d', strtotime(trim($param['end_time']))));
                $end_time_js   =  $de_start_time+60*60*24-1;
                $where['regtime'] = ['between',[$start_time,$end_time_js]];
            }

            if(!empty($param['regmany_start'])){
                $regmany_start = trim($param['regmany_start']);
                $where['regmany'] = ['gt',$regmany_start];
            }

            if(!empty($param['regmany_end'])){
                $regmany_start = trim($param['regmany_end']);
                $where['regmany'] = ['lt',$regmany_start];
            }

            if(!empty($param['regmany_start'])&&!empty($param['regmany_end'])){
                $regmany_start = trim($param['regmany_start']);
                $regmany_end = trim($param['regmany_end']);
                $where['regmany'] = ['between',[$regmany_start,$regmany_end]];
            }

            
           

           
        }

        if ($group == 1){
            $keword = Db::name('enterprise')
                ->where($where)
                ->where('del_type',0)
                ->where($having)
                ->paginate(10,false,['query'=>request()->param()]);
        }else{
            $keword = Db::name('enterprise')
                ->where($where)
                ->where('del_type',0)
                ->paginate(10,false,['query'=>request()->param()]);
        }

        $this->assign('query',$keword);
        $this->assign('count',count($keword));
        return $this->fetch();
    }
    /**
     * 添加企业
    */
    public function addcompany()
    {
        if(request()->isPost()){
            $add = $this->request->param();
            $add['add_time']      = time();
            $add['regtime']       = strtotime(trim($add['regtime']));
            $judge = Db::name('enterprise')->where('credit_code',$add['credit_code'])->find();
            if($judge){
                $this->error('统一社会信用代码已存在！');
            }
            $pagemodel = new EnterpriseModel();      
            if($pagemodel->save($add)){
                $this->success('添加成功',url('AdminPage/index'),['parent'=>1]);
            }else{
                $this->error('添加失败');
            }
        }
        return $this->fetch();
    }
    /**
     * 添加页面
     * @adminMenu(
     *     'name'   => '添加页面',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加页面',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $themeModel     = new ThemeModel();
        $pageThemeFiles = $themeModel->getActionThemeFiles('portal/Page/index');
        $this->assign('page_theme_files', $pageThemeFiles);
        return $this->fetch();
    }
    /**
     * 运营异常企业
    */
    public function abnormal()
    {
        $where = [];
        $having = '';
        $group = 0;
        if(request()->isPost()){
            $search = $this->request->param();
            if(!empty($search['keyword'])){
                $vague = trim($search['keyword']);
                $where['unitname'] = ['like',"%$vague%"];
            }
            if(!empty($search['start_time'])){
                $start_time = strtotime(trim($search['start_time']));

                $where['regtime'] = ['gt',$start_time];
            }
            if(!empty($search['end_time'])){
                $end_time_cl = strtotime(trim($search['end_time']));
                $end_time   =   $end_time_cl+60*60*24-1;
                $where['regtime'] = ['lt',$end_time];
            }
            if(!empty($search['start_time'])&&!empty($search['end_time'])){
                $start_time = strtotime(trim($search['start_time']));
                $end_time_cl = strtotime(trim($search['end_time']));
                $end_time   =   $end_time_cl+60*60*24-1;
                $where['regtime'] = ['between',[$start_time,$end_time]];
            }

            if(!empty($search['regmany_start'])){
                $regmany_start = trim($search['regmany_start']);
                $where['regmany'] = ['gt',$regmany_start];
            }
            if(!empty($search['regmany_end'])){
                $regmany_end = trim($search['regmany_end']);
                $where['regmany'] = ['lt',$regmany_end];
            }

            if(!empty($search['regmany_start'])&&!empty($search['regmany_end'])){
               $regmany_start = trim($search['regmany_start']);
               $regmany_end = trim($search['regmany_end']);
               $where['regmany'] = ['between',[$regmany_start,$regmany_end]];
            }

          
        }
        if ($group == 1){
            $keword = Db::name('enterprise')
                ->where($where)
                ->where('del_type',0)
                ->where('is_abnormal',1)
                ->where($having)
                ->paginate(10,false,['query'=>request()->param()]);
        }else{
            $keword = Db::name('enterprise')
                ->where($where)
                ->where('del_type',0)
                ->where('is_abnormal',1)
                ->paginate(10,false,['query'=>request()->param()]);
        }

        $this->assign('abnormal',$keword);
        return $this->fetch();
    }

    /**
     * 添加页面提交
     * @adminMenu(
     *     'name'   => '添加页面提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加页面提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data['post'], 'AdminPage');
        if ($result !== true) {
            $this->error($result);
        }

        $portalPostModel = new PortalPostModel();
        $portalPostModel->adminAddPage($data['post']);
        $this->success(lang('ADD_SUCCESS'), url('AdminPage/edit', ['id' => $portalPostModel->id]));

    }

    /**
     * 编辑页面
     * @adminMenu(
     *     'name'   => '编辑页面',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑页面',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $portalPostModel = new PortalPostModel();
        $post            = $portalPostModel->where('id', $id)->find();

        $themeModel     = new ThemeModel();
        $pageThemeFiles = $themeModel->getActionThemeFiles('portal/Page/index');

        $routeModel         = new RouteModel();
        $alias              = $routeModel->getUrl('portal/Page/index', ['id' => $id]);
        $post['post_alias'] = $alias;
        $this->assign('page_theme_files', $pageThemeFiles);
        $this->assign('post', $post);

        return $this->fetch();
    }

    /**
     * 编辑企业提交
     * @adminMenu(
     *     'name'   => '编辑页面提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑页面提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        if(request()->isPost()){
            $edit = $this->request->param();
            $enterprise =  new EnterpriseModel();
            $res = $enterprise->qy_edit($edit);
             
            if($res == 1 ){
                $this->success('编辑成功',url('AdminPage/index'),['parent'=>1]);
            }elseif($res == 2){
                $this->error('统一社会信用代码已存在，请修改后编辑！');
            }else {            
                $this->error('未做编辑',url('AdminPage/index'),['parent'=>1]);
            }
        }
        $editid = $this->request->param();
        $edit = Db::name('enterprise')->where('id',$editid['qy_id'])->find();
        $this->assign('edit',$edit);
        $license = substr($edit['license'],strripos($edit['license'],"/")+1);
        $this->assign('license_name',$license);
        return $this->fetch('editcompany');
    }
    
    public function see(){
        $editid = $this->request->param();
        $edit = Db::name('enterprise')
            ->where('id',$editid['id'])
            ->find();
        $activity = Db::name('activity_enterprise')->alias('ae')
            ->field('ae.a_id,a.title,ae.count')
            ->where('ae.e_id',$editid['id'])
            ->join('__ACTIVITY__ a','a.id = ae.a_id','right')
            ->select();
        $this->assign('num',count($activity)==0? 0:1);
        $this->assign('activity',$activity);
        $this->assign('edit',$edit);
        return $this->fetch('information');
    }

    /**
     * 删除页面
     * )
     */
    public function delete()
    {
        $deleid = $this->request->param();
        $result = Db::name('enterprise')->where('id',$deleid['id'])->update(['del_type'=>1]);
        if($result){
            $this->success('删除成功',url('/portal/admin_page'));
        }else{
            $this->error('删除失败');
        }
    }

    public function editcompany()
    {   
        if(request()->isPost()){
            $edit = $this->request->post();
            if(empty($edit['admission'])){
                $this->error('请填写入住方式');   
            }
            if(!preg_match("/^1[34578]{1}\d{9}$/",$edit['information'])){

                $this->error('手机号码格式有误');    
            }
            $date =[
                "industry"              =>      $edit['industry'],
                "unitname"              =>      $edit['unitname'],
                "registermany"          =>      $edit['registermany'],
                "registeredplace"       =>      $edit['Registeredplace'],
                "management"            =>      $edit['Management'],
                "legalperson"           =>      $edit['Legalperson'],
                "customer"              =>      $edit['Customer'],
                "contacts"              =>      $edit['Contacts'],
                "admission"             =>      $edit['admission'],
                "information"           =>      $edit['information'],
                "situation"             =>      $edit['situation'],
                "pickpeople"            =>      $edit['Pickpeople']
           ];
           $result = Db::name('enterprise')
           ->where('id',$edit['id'])
           ->update($date);
           
           if($result){
              Db::commit();
              $adminId = cmf_get_current_admin_id();
              cmf_admin_log($adminId,"成功修改了企业管理【{$date['unitname']}】");
              $this->success('修改成功','/portal/admin_page',['parent'=>1]);

           }else{
               $this->error("修改失败");
           }
        }else{
            $this->error('修改失败');
        }
  
    }

    public function addindeurty()
    {
        if(request()->isPost()){
            $res = $this->request->param();
            if($res['a_name']==$res['b_name']){
               $val = Db::name('industry')->insert(['industry'=>$res['a_name']]);
               if($val){
                    Db::commit();
                    $adminId = cmf_get_current_admin_id();
                    cmf_admin_log($adminId,"新增加了行业【$res[a_name]】");
                   $this->success('添加行业成功','/portal/admin_page',['parent'=>1]);
               }else{
                   $this->error('添加行业失败');
               }
            }else{
                $this->error('添加行业失败');
            }
        }
        return $this->fetch();
    }

    public function enteroperate(){

        $where = [];
        $having = '';
        $group = 0;
        if(request()->isPost()){
            $param = $this->request->param();

            if(!empty($param['keyword'])){
                $keyword = trim($param['keyword']);
                $where['en.unitname'] = ['like',"%$keyword%"];
            }

            if(!empty($param['start_time'])){
                $start_time = strtotime($param['start_time']);
                $where['en.regtime'] = ['gt',$start_time];
            }
            if(!empty($param['end_time'])){
                $end_time = strtotime($param['end_time']);
                $where['en.regtime'] = ['lt',$end_time];
            }

            if(!empty($param['start_time'])&&!empty($param['end_time'])){
                $start_time = strtotime(date('Y-m-d', strtotime(trim($param['start_time']))));
                $de_start_time =  strtotime(date('Y-m-d', strtotime(trim($param['end_time']))));
                $end_time_js   =  $de_start_time+60*60*24-1;
                $where['en.regtime'] = ['between',[$start_time,$end_time_js]];
            }

            if(!empty($param['regmany_start'])){
                $regmany_start = trim($param['regmany_start']);
                $where['en.regmany'] = ['gt',$regmany_start];
            }

            if(!empty($param['regmany_end'])){
                $regmany_start = trim($param['regmany_end']);
                $where['en.regmany'] = ['lt',$regmany_start];
            }

            if(!empty($param['regmany_start'])&&!empty($param['regmany_end'])){
                $regmany_start = trim($param['regmany_start']);
                $regmany_end = trim($param['regmany_end']);
                $where['en.regmany'] = ['between',[$regmany_start,$regmany_end]];
            }

            $pre = Config::get("database.prefix");
            if (is_numeric($param['profit_start'])){
                $profit_start = trim($param['profit_start']);
                $group = 1;
                $having = "en.id in (select enter_id from {$pre}enter_operate GROUP BY enter_id HAVING SUM(profit) > {$profit_start} AND SUM(is_report)>0)";
            }
            if (is_numeric($param['profit_end'])){
                $profit_end = trim($param['profit_end']);
                $group = 1;
                $having = "en.id in (select enter_id from {$pre}enter_operate GROUP BY enter_id HAVING SUM(profit) < {$profit_end} AND SUM(is_report)>0)";
            }
            if(is_numeric($param['profit_start'])&&is_numeric($param['profit_end'])){
                $profit_start = trim($param['profit_start']);
                $profit_end = trim($param['profit_end']);
                $group = 1;
                $having = "en.id in (select enter_id from {$pre}enter_operate GROUP BY enter_id HAVING SUM(profit) > {$profit_start} AND SUM(profit) < {$profit_end} AND SUM(is_report)>0)";

            }

            if (is_numeric($param['is_report'])){
                $where['en.is_report'] = trim($param['is_report']);
            }
        }

        if ($group == 1){
            $keword = Db::name('enterprise')
                ->alias('en')
                ->join("__ENTER_OPERATE__ op",'en.id = op.enter_id','left')
                ->field('en.*,en.id enterid,sum(op.profit) sum_profit')
                ->where($where)
                ->where('en.del_type',0)
                ->where($having)
                ->group('en.id')
                ->paginate(10,false,['query'=>request()->param()]);

        }else{
            $keword = Db::name('enterprise')
                ->alias('en')
                ->join("__ENTER_OPERATE__ op",'en.id = op.enter_id','left')
                ->field('en.*,en.id enterid,sum(op.profit) sum_profit')
                ->where($where)
                ->where('en.del_type',0)
                ->group('en.id')
                ->paginate(10,false,['query'=>request()->param()]);

        }

        $this->assign('query',$keword);
        $this->assign('count',count($keword));
        return $this->fetch();
    }

	public function operate(){
        $data = $this->request->param();
        $season = date('Y').'/'.ceil(date('n')/3);   // '2019/4'
        $enter_time = Db::name('enter_operate')->where('enter_id',$data['enter_id'])->where('enter_time',$season)->order('id desc')->value('enter_time');
        if (!empty($enter_time)){
           if ($season != $enter_time){
               if (date('Y') != substr($enter_time,0,4)){
                   for($i=substr($enter_time,0,4);$i<=date('Y');$i++){
                       if ($i == substr($enter_time,0,4)){
                           for($j=substr($enter_time,-1)+1;$j<=4;$j++){
                               $insert['enter_time'] = $i.'/'.$j;
                               $insert['enter_id'] = $data['enter_id'];
                               Db::name('enter_operate')->insert($insert);
                           }
                       }else{
                           for($j=1;$j<=4;$j++){
                               $insert['enter_time'] = $i.'/'.$j;
                               $insert['enter_id'] = $data['enter_id'];
                               Db::name('enter_operate')->insert($insert);
                           }
                       }
                   }
               }else{
                   for($i=substr($enter_time,-1)+1;$i<=ceil(date('n')/3);$i++){
                       $insert['enter_time'] = date('Y').'/'.$i;
                       $insert['enter_id'] = $data['enter_id'];
                       Db::name('enter_operate')->insert($insert);
                   }
               }
           }
        }else{
           $insert['enter_time'] = $season;
           $insert['enter_id'] = $data['enter_id'];
           Db::name('enter_operate')->insert($insert);
        }

        $list = Db::name('enter_operate')->where('enter_id',$data['enter_id'])->order('enter_time desc')->paginate(10);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('enter_id',$data['enter_id']);
        $this->assign('name',Db::name('enterprise')->where('id',$data['enter_id'])->value('unitname'));
        return $this->fetch();
    }

    public function operate_excel(){
        if($this->request->isPost()){
            $excel_name = $this->request->param('excel_name');
            $excel_url = $this->request->param('excel_url');
            $enter_id = $this->request->param('enter_id');
            if(!empty($excel_name) && !empty($excel_url)){
                Loader::import('PHPExcel.Classes.PHPExcel');
                Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');
                Loader::import('PHPExcel.Classes.PHPExcel.Reader.Excel5');
                Vendor('phpexcel.PHPExcel');
                ini_set("memory_limit","1024M");
                set_time_limit(60); //执行时间为60秒
                $filename = ROOT_PATH.'public/upload/'.$excel_url;  //文件路径
                $extension = cmf_get_file_extension($excel_url);    //文件扩展名
                if($extension == 'xlsx'){
                    $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
                }else if($extension == 'xls'){
                    $objReader =\PHPExcel_IOFactory::createReader('Excel5');
                }else{
                    $this->error('请上传Excel格式的文件！','');
                }
                $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$objPHPExcel->getsheet(0)->toArray();   //转换为数组格式
                $enterprise = Db::name('enterprise')->field('credit_code,profit')->where('id',$enter_id)->find();
                $data = [];
                $sj = [];
                $error = '';
                $profit = $enterprise['profit'];
                array_shift($excel_array);
                foreach($excel_array as $k=>$v) {

                    if (empty($v[0])&&empty($v[1])&&empty($v[2])&&empty($v[3])&&empty($v[4])&&empty($v[5])&&empty($v[6])&&empty($v[7])&&empty($v[8])&&empty($v[9])&&empty($v[10])&&empty($v[11])&&empty($v[12])&&empty($v[13])){
                        break;
                    }
                    if ($v[14] == $enterprise['credit_code'] && $v[0]){
                        $v[0] == ''?$this->error('运营时间不能为空！'):$data["enter_time"]=$v[0];
                        is_numeric($v[1])?$data["assets"]=$v[1]:$this->error('资产总额不能为空！');
//                        $v[1] == ''?$this->error('资产总额不能为空！'):$data["assets"]=$v[1];
                        is_numeric($v[2])?$data["profit"]=$v[2]:$this->error('利润总额不能为空！');
                        is_numeric($v[3])?$data["sales"]=$v[3]:$this->error('营业总收入不能为空！');
                        is_numeric($v[4])?$data["net_profit"]=$v[4]:$this->error('净利润不能为空！');
                        is_numeric($v[5])?$data["main_business"]=$v[5]:$this->error('营业总收入中主营业务收入不能为空！');
                        is_numeric($v[6])?$data["debt"]=$v[6]:$this->error('负债总额不能为空！');
                        is_numeric($v[7])?$data["tax"]=$v[7]:$this->error('纳税总额不能为空！');
                        is_numeric($v[8])?$data["equity"]=$v[8]:$this->error('所有者权益合计不能为空！');
                        is_numeric($v[9])?$data["pension"]=$v[9]:$this->error('城镇职工基本养老保险人数不能为空！');
                        is_numeric($v[10])?$data["unemployment"]=$v[10]:$this->error('失业保险人数不能为空！');
                        is_numeric($v[11])?$data["medical"]=$v[11]:$this->error('职工基本医疗保险人数不能为空！');
                        is_numeric($v[12])?$data["work_injury"]=$v[12]:$this->error('工伤保险人数不能为空！');
                        is_numeric($v[13])?$data["fertility"]=$v[13]:$this->error('生育保险人数不能为空！');
                        $data["enter_id"]                =     $enter_id;
                        $data["is_report"]               =     1;
                        $data["add_time"]                =     time();
                        $profit += $v[2];

                        $season = date('Y').'/'.ceil(date('n')/3);   // '2019/4'

                        if ($v[0] == $season){
                            Db::name('enterprise')->where('id',$enter_id)->update(['is_report'=>1]);
                        }

                        $is_report = Db::name('enter_operate')->where('enter_time',$v[0])->where('enter_id',$enter_id)->value('is_report');
                        if (is_numeric($is_report)){
                            if ($is_report == 1){
                                $error .= $v[0].',';
                            }else{
                                $operate_add = Db::name('enter_operate')->where('enter_time',$v[0])->where('enter_id',$enter_id)->update($data);
                            }
                        }else{
                            $operate_add = Db::name('enter_operate')->insert($data);
                        }
                    } else{
//                        $error .= '请检查统一社会信用代码！';
                        $this->error('请检查统一社会信用代码！');
                    }
                }
                $update['profit'] = $profit;
                $enter_update = Db::name('enterprise')->where('id',$enter_id)->update($update);
                if(isset($operate_add)){
                    if ($operate_add && $enter_update){
                        if ($error != ''){
                            cmf_admin_log(cmf_get_current_admin_id(),"导入了Excel表格");
                            $this->success($error.'季度数据已存在，请直接编辑，其他数据导入数据成功！',url('portal/admin_page/operate',['enter_id'=>$enter_id])); // $enter_id
                        }else{
                            cmf_admin_log(cmf_get_current_admin_id(),"导入了Excel表格");
                            $this->success('数据导入数据成功！',url('portal/admin_page/operate',['enter_id'=>$enter_id])); // $enter_id
                        }
                    }
                } else {
                    if ($error != ''){
                        $this->error($error.'季度数据已存在，请直接编辑！','');// $enter_id
                    }else{
                        $this->error('数据导入失败!');// $enter_id
                    }
                }
            }else{
                $this->error('文件导入失败!','');
            }
        }
    }

    public function editoperate(){
        $data = $this->request->param();
        $list = Db::name('enter_operate')->where('id',$data['id'])->find();
        $this->assign('list',$list);
        $unitname = Db::name('enterprise')->where('id',$list['enter_id'])->value('unitname');
        $this->assign('unitname',$unitname);
        return $this->fetch();
    }
    public function editoperatePost(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
//            !isset($data['status']) && $data['status'] = 0;
//            !isset($data['ban_status']) && $data['ban_status'] = 0;
//            $check = $this->validate($data, 'Channel.edit');
//            if ($check !== true) {
//                $this->error($check);
//            }
            $profit = Db::name('enter_operate')->field('enter_id,profit')->where('id',$data['id'])->find();
            if ($profit['profit'] != trim($data['profit'])){
                $all_profit = Db::name('enterprise')->where('id',$profit['enter_id'])->value('profit');
                $new_profit = $all_profit-$profit['profit']+trim($data['profit']);
                $enterupdate['profit'] = $new_profit;
                Db::name('enterprise')->where('id',$profit['enter_id'])->update($enterupdate);
            }
            $res = Db::name('enter_operate')->where('id',$data['id'])->update($data);
            if ($res) {
                cmf_admin_log(cmf_get_current_admin_id(),"编辑了成功");  // 编辑了某公司某季度的运营报表
                $this->success('更新成功',url('AdminPage/operate'),['parent'=>1]);

            } else {
                $this->error("更新失败！");
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return null
     */
    public function down_excel(){      
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        $PHPSheet->setCellValue("A1", "序号")
            ->setCellValue("B1", "企业名称")
            ->setCellValue("C1", "统一社会信用代码")
            ->setCellValue("D1", "法定代表人")
            ->setCellValue("E1", "注册时间")
            ->setCellValue("F1", "注册资本(万元)")
            ->setCellValue("G1", "注册地")
            ->setCellValue("H1", "登记机关")
            ->setCellValue("I1", "核准日期")
            ->setCellValue("J1", "营业期限自")
            ->setCellValue("K1", "营业期限至")
            ->setCellValue("L1", "行业")
            ->setCellValue("M1", "经营内容")
            ->setCellValue("N1", "住所")
            ->setCellValue("O1", "营业执照");

        $PHPSheet
            ->setCellValueExplicit('A2','1',\PHPExcel_Cell_DataType::TYPE_STRING)
//            ->setCellValue("A2", "2019/4")
            ->setCellValue("B2", "东营某企业")
            ->setCellValue("C2", "ABCDE111")
            ->setCellValue("D2", "刘某某")
            ->setCellValue("E2", "2019/10/31")
            ->setCellValue("F2", "100")
            ->setCellValue("G2", "山东省东营市")
            ->setCellValue("H2", "市场监管局")
            ->setCellValue("I2", "2019/10/31")
            ->setCellValue("J2", "2019/1/31")
            ->setCellValue("K2", "2019/10/31")
            ->setCellValue("L2", "某行业")
            ->setCellValue("M2", "节能技术研发")
            ->setCellValue("N2", "山东省东营市")
            ->setCellValue("O2", "");

        $PHPSheet->getColumnDimension('C')->setWidth(35);//设置宽度
        $PHPSheet->getColumnDimension('F')->setWidth(30);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(20);
        $PHPSheet->getColumnDimension('L')->setWidth(20);

        $PHPSheet->getColumnDimension('M')->setWidth(35);
        $PHPSheet->getColumnDimension('N')->setWidth(35);
        $PHPSheet->getColumnDimension('O')->setWidth(35);
        $PHPSheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $PHPSheet->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean(); // Added by me
        ob_start(); // Added by me
        header('Content-Disposition: attachment;filename="企业模板.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save('php://output');
 
    }

    public function operate_down_excel(){
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        $PHPSheet->setCellValue("A1", "时间")
            ->setCellValue("B1", "资产总额（万元）")
            ->setCellValue("C1", "利润总额（万元）")
            ->setCellValue("D1", "营业总收入（万元）")
            ->setCellValue("E1", "净利润（万元）")
            ->setCellValue("F1", "营业总收入中主营业务收入（万元）")
            ->setCellValue("G1", "负债总额（万元）")
            ->setCellValue("H1", "纳税总额（万元）")
            ->setCellValue("I1", "所有者权益合计（万元）")
            ->setCellValue("J1", "城镇职工基本养老保险（人）")
            ->setCellValue("K1", "失业保险（人）")
            ->setCellValue("L1", "职工基本医疗保险（人）")
            ->setCellValue("M1", "工伤保险（人）")
            ->setCellValue("N1", "生育保险（人）")
            ->setCellValue("O1", "企业统一社会信用代码");
        $PHPSheet->getStyle('A')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $PHPSheet->getColumnDimension('B')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('C')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('D')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('E')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('F')->setWidth(35);
        $PHPSheet->getColumnDimension('G')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('H')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('I')->setWidth(30);//设置宽度
        $PHPSheet->getColumnDimension('J')->setWidth(34);//设置宽度
        $PHPSheet->getColumnDimension('K')->setWidth(20);//设置宽度
        $PHPSheet->getColumnDimension('L')->setWidth(30);//设置宽度
        $PHPSheet->getColumnDimension('M')->setWidth(20);
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(30);
        $PHPSheet->getStyle('O')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
//        $PHPSheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
//        $PHPSheet->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

        $PHPSheet
            ->setCellValueExplicit('A2','2019/4',\PHPExcel_Cell_DataType::TYPE_STRING)
//            ->setCellValue("A2", "2019/4")
            ->setCellValue("B2", "1000")
            ->setCellValue("C2", "200")
            ->setCellValue("D2", "400")
            ->setCellValue("E2", "150")
            ->setCellValue("F2", "100")
            ->setCellValue("G2", "0")
            ->setCellValue("H2", "60")
            ->setCellValue("I2", "30")
            ->setCellValue("J2", "15")
            ->setCellValue("K2", "15")
            ->setCellValue("L2", "15")
            ->setCellValue("M2", "10")
            ->setCellValue("N2", "7")
            ->setCellValue("O2", "91310114MA1GTMC58X");
        $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean(); // Added by me
        ob_start(); // Added by me
        header('Content-Disposition: attachment;filename="经营报备模板.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save('php://output');
    }

    /**
     * 读取excel文件.
     *
     * @param string $file excel文件路径 以点开头  ./app/Uploads/excel/
     *
     * @return array 返回excel文件内容数组
     */
    public function importExcel()
    {
        if($this->request->isPost()){
            $excel_name = $this->request->param('excel_name');
            $excel_url = $this->request->param('excel_url');
            if(!empty($excel_name) && !empty($excel_url)){

                Loader::import('PHPExcel.Classes.PHPExcel');
                Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');
                Loader::import('PHPExcel.Classes.PHPExcel.Reader.Excel5');
                Vendor('phpexcel.PHPExcel');
                ini_set("memory_limit","1024M");
                set_time_limit(60); //执行时间为60秒
                $filename = ROOT_PATH.'public/upload/'.$excel_url;  //文件路径
                $extension = cmf_get_file_extension($excel_url);    //文件扩展名
                if($extension == 'xlsx'){
                    $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
                }else if($extension == 'xls'){
                    $objReader =\PHPExcel_IOFactory::createReader('Excel5');
                }else{
                    $this->error('请上传Excel格式的文件！','');
                }
                $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array=$objPHPExcel->getsheet(0)->toArray();   //转换为数组格式
                /*++++++++++++++++++++++++++++++++++++++取图片 START+++++++++++++++++++++++++++++++++++++++++++*/
                $worksheet = $objPHPExcel->getActiveSheet();
                $imageInfo = $this->extractImageFromWorksheet($worksheet,$excel_url,'O');
                /*++++++++++++++++++++++++++++++++++++++++取图片 END++++++++++++++++++++++++++++++++++++++++++*/
                array_shift($excel_array);
                $data = [];
                $sj = [];
                $error = '';
                foreach($excel_array as $k=>$v) {
//                    $image_num = $k+2;
                    array_shift($v);

                    $v[0]==''?$this->error('企业名称不能为空！'):$data[$k]["unitname"]=$v[0];
                    /*if ($v[1] == ''){
                        $this->error($v[0].'-统一社会信用代码不能为空！');
                    }else{
                        if (Db::name('enterprise')->where('del_type',0)->where('credit_code',trim($v[1]))->find()){
                            $this->error('统一社会信用代码为'.$v[1].'的企业已存在！');
                        }
                        $data[$k]["credit_code"]=trim($v[1]);
                    }*/
                    $v[1]==''?$this->error($v[0].'-统一社会信用代码不能为空！'):$data[$k]["credit_code"]=trim($v[1]);
                    $v[2]==''?$this->error($v[0].'-法定代表人不能为空！'):$data[$k]["legalperson"]=$v[2];

                    $v[3]==''?$this->error($v[0].'-注册时间不能为空！'): ($this->chack_time(date('Y-m-d',strtotime($v[3])))===true?$data[$k]["regtime"]=strtotime($v[3]):$this->error($v[0].'-注册时间格式不正确！'));
                    $v[4]==''?$this->error($v[0].'-注册资本不能为空！'):(is_numeric($v[4])===true?$data[$k]["regmany"]=$v[4]:$this->error($v[0].'-注册资本格式有误！'));
                    $v[5]==''?$this->error($v[0].'-注册地不能为空！'):$data[$k]["regplace"]=$v[5];
                    $v[6]==''?$this->error($v[0].'-登记机关不能为空！'):$data[$k]["regauthority"]=$v[6];
                    //            $data[$k]["license"]               =     $v[7];
                    $v[7]==''?$this->error($v[0].'-核准日期不能为空！'):($this->chack_time(date('Y-m-d',strtotime($v[7])))===true?$data[$k]["approval_date"]=date('Y-m-d',strtotime($v[7])):$this->error($v[0].'-核准日期时间格式不正确！'));
                    $v[8]==''?$this->error($v[0].'-营业开始期限不能为空！'):($this->chack_time(date('Y-m-d',strtotime($v[8])))===true?$data[$k]["busterm_start"]=date('Y-m-d',strtotime($v[8])):$this->error($v[0].'-营业开始期限时间格式不正确！'));
//                    $this->chack_time($v[9])===true?$data["busterm_end"]=$v[9]:$this->error($v[0].'-营业截至期限时间格式不正确！');

                    $v[9]==''||$v[9]==null?$data[$k]["busterm_end"]='':($this->chack_time(date('Y-m-d',strtotime($v[9])))===true?$data[$k]["busterm_end"]=date('Y-m-d',strtotime($v[9])):$this->error($v[0].'-营业截至期限时间格式不正确！'));
                    $v[10]==''?$this->error($v[0].'-行业不能为空！'):$data[$k]["industry"]=$v[10];
                    $v[11]==''?$this->error($v[0].'-经营内容不能为空！'):$data[$k]["management"]=$v[11];
                    $v[12]==''?$this->error($v[0].'-住所不能为空！'):$data[$k]["place"]=$v[12];

                    if (isset($imageInfo['O'][$k])){
                        $image_str = strrpos($imageInfo['O'][$k],'upload/');
                        $image_sub = substr($imageInfo['O'][$k],$image_str+7);
                    }else{
//                        $this->error('请正确上传营业执照！');
                        $image_sub = '';
                    }
                    $data[$k]["license"]               =     $image_sub;

                    $data[$k]["add_time"]              =     time();
                    $credit_code[$k] = Db::name('enterprise')->where('del_type',0)->where('credit_code',$v[1])->value('id');
                }

                foreach($credit_code as $k => $v){
                    if (empty($v)){
//                        print_r('<pre />');
//                        print_r($data);
//                        echo '<br />';
                        $enter_save = Db::name('enterprise')->insert($data[$k]);
                    }else{
                        $enter_save = Db::name('enterprise')->where('id',$v)->update($data[$k]);
                    }
                }
                if($enter_save){
//                    Db::name('activity_enterprise')->insertAll($sj);
//                    Db::commit();
                    $adminId = cmf_get_current_admin_id();
                    cmf_admin_log($adminId,"导入了Excel表格");
                    $this->success('导入数据成功！',url('portal/admin_page/index'));
                } else {
                    $this->error('导入数据失败!','');
                }

            }else{
                $this->error('文件导入失败!','');
            }
        }
    }

    function chack_time($time){
        if(substr_count($time,'-')==2) list($y,$m,$d)=explode('-',$time); else return false;

        if (checkdate($m,$d,$y)===true){
            return true;
        }else{
            return false;
        }
    }

    function extractImageFromWorksheet($worksheet,$url,$x){
        $result = array();
        $imageFileName = "";
        $img_path = ROOT_PATH.'public/upload/'.substr($url,0,15).'/image/';
        if(!is_dir($img_path)){
            mkdir ($img_path,0777,true);
        }
        foreach ($worksheet->getDrawingCollection() as $k=>$drawing) {
//            $xy=$drawing->getCoordinates();
//            $x = substr($xy,0,1);
// for xlsx
            if ($drawing instanceof \PHPExcel_Worksheet_Drawing) {
                $filename = $drawing->getPath();
                $imageFileName = $drawing->getIndexedFilename();
                $path = $img_path . $drawing->getIndexedFilename();
                copy($filename, $path);
                $result[$x][$k] = $path;
// for xls
            } else if ($drawing instanceof \PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                call_user_func(
                    $renderingFunction = $drawing->getRenderingFunction(),
                    $image = $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();
                switch ($renderingFunction) {
                    case \PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG:
                        $imageFileName = $drawing->getIndexedFilename();
                        file_put_contents($img_path.$drawing->getIndexedFilename(),$imageContents);
                        ob_end_clean();
//                    imagejpeg($image, $path);
                        break;
                    case \PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF:
                        $imageFileName = $drawing->getIndexedFilename();
                        file_put_contents($img_path.$drawing->getIndexedFilename(),$imageContents);
//                    imagegif($image, $path);
                        ob_end_clean();
                        break;
                    case \PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG:
                        $imageFileName = $drawing->getIndexedFilename();
                        file_put_contents($img_path.$drawing->getIndexedFilename(),$imageContents);
//                    imagegif($image, $path);
                        ob_end_clean();
                        break;
                    case \PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT:
                        $imageFileName = $drawing->getIndexedFilename();
                        file_put_contents($img_path.$drawing->getIndexedFilename(),$imageContents);
//                    imagegif($image, $path);
                        ob_end_clean();
                        break;
                }
//                echo $x.'-'.$k;
//                echo '<br />';
                $result[$x][$k] = $imageFileName;
            }
        }
        return $result;
    }

}
    
     

