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
use app\portal\model\PortalCategoryModel;
use think\Db;
use think\Loader;
use app\admin\model\ThemeModel;
use app\portal\model\AdminCategoryModel;


class AdminCategoryController extends AdminBaseController
{
    /**
     * 企业财务管理
     * @adminMenu(
     *     'name'   => '企业管理',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '企业财务列表',
     *     'param'  => ''
     * )
     */
    public function people_finance()
    {
        $cwid = $this->request->param('cw_id');
        if ($cwid) {
            session('cwid', $cwid);
        } else {
            $cw_id = session('cwid');
            session('cwid', $cwid);
        }
        $fres = Db::name('asset_liability')
            ->field('id,time,sum_flow_asets,sum_ord_fixed,sum_assets,sum_flow_payment,sum_fixed_liabilities,sum_liabilities,sum_rights_interests,sum_liabilities_equity')
            ->where('qid', $cwid)
            ->order('time desc')
            ->select();
        $this->assign([
            'cwid'      =>      $cwid,
            'fres'      =>      $fres
        ]);
        return $this->fetch();
    }

    public function people_profit()
    {
        $lrid = $this->request->param('lr_id');
        if ($lrid) {
            session('lrid', $lrid);
        } else {
            $lrid = session('lrid');
            session('lrid', $lrid);
        }
        $lr_res = Db::name('profit')
            ->field('id,business_income,operating_cost,sales_tax_additional,selling_expenses,management_cost,income_investment,operating_profit,non_operating_income,non_operating_expenses,total_profit,income_tax,net_profit,time')
            ->where('qid', $lrid)
            ->order('time desc')
            ->select();

        $this->assign([
            'lrid'  =>    $lrid,
            'lrres' =>    $lr_res
        ]);
        return $this->fetch();
    }

    public function people_cash_flow()
    {
        $llid = $this->request->param('ll_id');
        if ($llid) {
            session('llid', $llid);
        } else {
            $llid = session('llid');
            session('llid', $llid);
        }
        $cf_res = Db::name('cash_flow')
            ->field('id,net_cash_flow,investment_flow,net_cash_activities,exchange_rate_effect,increment,initial_balance,ending_balance,time')
            ->where('qid', $llid)
            ->order('time desc')
            ->select();
        $this->assign([
            'llid'      =>      $llid,
            'cfres'     =>      $cf_res
        ]);
        return $this->fetch();
    }
    /**
     * 添加员工信息
     */
    public function addreport()
    {
        $qy_id = $this->request->param();
        if (request()->isPost()) {
            $add = $this->request->param();
            $add['statr_time'] = time();
            if (!preg_match("/^1[3456789]{1}\d{9}$/", $add["mobile"])) {
                return $this->error('手机号码格式有误');
            }
            if ($add['card_type'] == 0) {
                if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $add['card_number'])) {
                    return $this->error('身份证号格式有误');
                }
            }
            if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $add["email"])) {
                return $this->error('邮箱格式有误');
            }
            if (!is_int($add['work_time']) && strpos($add['work_time'], '.') !== false) {
                return $this->error('在职时长只能是整数');
            }
            $repeat = Db::name('enter_user')->where('card_number', $add['card_number'])->where('del_type', 0)->find();
            if ($repeat) {
                return $this->error('员工已经存在', url('adminCategory/report'), ['parent' => 1]);
            }
            $res = Db::name('enter_user')->insert($add);
            if ($res) {
                $this->success('添加成功', url('adminCategory/report'), ['parent' => 1]);
            } else {
                return $this->error('添加失败');
            }
        }
        $this->assign('qy_id', $qy_id['qy_id']);
        return $this->fetch();
    }
    /**
     * 编辑员工信息
     */
    public function editreport()
    {
        if (request()->isPost()) {
            $edit = $this->request->param();
            if (!preg_match("/^1[3456789]{1}\d{9}$/", $edit["mobile"])) {
                $this->error('手机号码格式有误');
            }
            if($edit['card_type']==0){
                if(!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/',$edit['card_number'])){
                   return $this->error('身份证号格式有误');
                }
            }

            $edit['enter_id'] = session('qy_id');
            $val = Db::name('enter_user')->where('id', $edit['id'])->update($edit);
            return $this->success('修改成功', url('AdminCategory/report'), ['parent' => 1]);
        }
        $edit_id = $this->request->param();
        $res = Db::name('enter_user')->where('id', $edit_id['id'])->find();
        $this->assign('res', $res);
        return $this->fetch();
    }
    /**
     * 删除员工信息
     */
    public function del()
    {
        $del_id = $this->request->param();
        $del_type = ['del_type' => 1];
        $del = Db::name('enter_user')->where('id', $del_id['id'])->update($del_type);
        if ($del) {
            $this->success('删除成功', url('AdminCategory/report'), ['parent' => 1]);
        } else {
            $this->error('删除失败');
            return false;
        }
    }
    /**
     * 下载模板
     */
    public function down_model()
    {
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        $PHPSheet->setCellValue("A1", "序号")
            ->setCellValue("B1", "员工姓名")
            ->setCellValue("C1", "证件类型")
            ->setCellValue("D1", "证件号码")
            ->setCellValue("E1", "行政职务")
            ->setCellValue("F1", "技术职务")
            ->setCellValue("G1", "在职时长（年）")
            ->setCellValue("H1", "联系电话")
            ->setCellValue("I1", "电子邮箱")
            ->setCellValue("J1", "是否股东")
            ->setCellValue("K1", "企业统一社会信用代码");

        $PHPSheet->setCellValue("A2", "01")
            ->setCellValue("B2", "张某某")
            ->setCellValue("C2", "身份证")
            ->setCellValue("D2", "3707************39")
            ->setCellValue("E2", "行政")
            ->setCellValue("F2", "前台")
            ->setCellValue("G2", "2")
            ->setCellValue("H2", "150*******9")
            ->setCellValue("I2", "1******@163.com")
            ->setCellValue("J2", "是/否")
            ->setCellValue("K2", "SHXY123456");

        $PHPSheet->getColumnDimension('B')->setWidth(20); //设置宽度
        $PHPSheet->getStyle('D')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $PHPSheet->getColumnDimension('I')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(25);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $PHPSheet->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean(); // Added by me
        ob_start(); // Added by me
        header('Content-Disposition: attachment;filename="企业员工模板.xlsx"');
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
        if ($this->request->isPost()) {
            $excel_name = $this->request->param('excel_name');
            $excel_url = $this->request->param('excel_url');
            if (!empty($excel_name) && !empty($excel_url)) {

                Loader::import('PHPExcel.Classes.PHPExcel');
                Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');
                Loader::import('PHPExcel.Classes.PHPExcel.Reader.Excel5');
                ini_set("memory_limit", "1024M");
                set_time_limit(60); //执行时间为60秒
                $filename = ROOT_PATH . 'public/upload/' . $excel_url;  //文件路径
                $extension = cmf_get_file_extension($excel_url);    //文件扩展名
                if ($extension == 'xlsx') {
                    $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');  //加载文件内容,编码utf-8
                } else if ($extension == 'xls') {
                    $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                    $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');  //加载文件内容,编码utf-8
                } else {
                    $this->error('请上传Excel格式的文件！', '');
                }

                $excel_array = $objPHPExcel->getsheet(0)->toArray();   //转换为数组格式              
                array_shift($excel_array);
                $credit_code = Db::name('enterprise')->where('id', session('qy_id'))->value('credit_code');
                if ($excel_array[0][10] != $credit_code) {
                    $this->error('员工与企业不匹配');
                }
                $data = [];
                $error = '';
                foreach ($excel_array as $k => $v) {
                    array_shift($v);

                    if (empty($v[0]) && empty($v[1]) && empty($v[2]) && empty($v[3]) && empty($v[4]) && empty($v[5]) && empty($v[6])) {

                        break;
                    }
                    $data[$k]["name"]                =     $v[0];
                    $data[$k]["card_type"]           =     $v[1];
                    $data[$k]["card_number"]         =     $v[2];
                    $data[$k]["technical"]           =     $v[3];
                    $data[$k]["administrate"]        =     $v[4];
                    $data[$k]["work_time"]           =     $v[5];
                    $data[$k]["mobile"]              =     $v[6];
                    $data[$k]["email"]               =     $v[7];
                    $data[$k]["is_shareholder"]      =     $v[8];
                    $data[$k]["enter_id"]            =     session('qy_id');
                    $data[$k]["statr_time"]          =     time();
                    //判断身份状态  
                    if ($data[$k]["card_type"]  == "身份证") {
                        $data[$k]["card_type"] = 0;
                    } else if ($data[$k]["card_type"]  == "驾驶证") {
                        $data[$k]["card_type"] = 1;
                        $this->error($v[0] . '的证件类型不正确');
                    } else {
                        $data[$k]["card_type"] = 2;
                        $this->error($v[0] . '的证件类型不正确');
                    }
                    //判断是否是股东
                    if ($data[$k]["is_shareholder"] == "是") {
                        $data[$k]["is_shareholder"] = 1;
                    } else {
                        $data[$k]["is_shareholder"] = 0;
                    }
                    //判断邮箱是否合法
                    if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $data[$k]["email"])) {
                        $this->error('邮箱格式有误');
                    }
                    //判断在职时长是否合法
                    if (!is_int($data[$k]["work_time"]) && strpos($data[$k]["work_time"], '.') !== false) {
                        $this->error('在职时长只能是整数');
                    }
                    //判断手机号是否合法

                    if (!preg_match("/^1[3456789]{1}\d{9}$/", $v[6])) {
                        $this->error('手机号码格式有误' . $k);
                    }
                    //判断员工是否存在
                    //                    $repeat = Db::name('enter_user')
                    //                        ->where('card_number',$data[$k]["card_number"])
                    //                        ->where('enter_id',session('qy_id'))
                    //                        ->where('del_type',0)
                    //                        ->value('mobile');
                    //
                    //                        if(!empty($repeat)){
                    //                            $error .= $repeat.',';
                    //                        }
                    //
                    $user_id[$k] = Db::name('enter_user')->alias('user')
                        ->join("__ENTERPRISE__ en", 'user.enter_id = en.id')
                        ->where('user.del_type', 0)
                        ->where('en.credit_code', $v[9])
                        ->where('user.card_number', $v[2])->value('user.id');
                }
                foreach ($user_id as $k => $v) {
                    if (empty($v)) {
                        $user_save = Db::name('enter_user')->insert($data[$k]);
                    } else {
                        $user_save = Db::name('enter_user')->where('id', $v)->update($data[$k]);
                    }
                }
                if ($user_save) {
                    $this->success('导入数据成功！', url('AdminCategory/report'));
                } else {
                    $this->error('导入数据失败!', '');
                }
                /*if($error!=''){
                   $this->error('手机号为：'.$error.'已存在');
                }

                 if(Db::name('enter_user')->insertAll($data)){
                     $this->success('导入数据成功！',url('AdminCategory/report'));
                 } else {
                     $this->error('导入数据失败!','');
                 }*/
            } else {
                $this->error('文件导入失败!', '');
            }
        }
    }

    /**
     * 添加文章分类
     * @adminMenu(
     *     'name'   => '添加文章分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章分类',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $parentId            = $this->request->param('parent', 0, 'intval');
        $portalCategoryModel = new PortalCategoryModel();
        $categoriesTree      = $portalCategoryModel->adminCategoryTree($parentId);

        $themeModel        = new ThemeModel();
        $listThemeFiles    = $themeModel->getActionThemeFiles('portal/List/index');
        $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');

        $this->assign('list_theme_files', $listThemeFiles);
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    /**
     * 编辑利润
     */
    public function editprofit()
    {
        if (request()->isPost()) {
            $editp = $this->request->param();

            $res = Db::name('profit')->where('id', $editp['id'])->update($editp);
            if ($res) {
                $this->success('修改利润成功', url('people/profit'), ['parent' => 1]);
            } else {
                $this->error('修改利润失败');
            }
        }
        $pf_id = $this->request->param('id');
        $pf_res = Db::name('profit')
            ->field('id,business_income,operating_cost,sales_tax_additional,selling_expenses,management_cost,income_investment,operating_profit,non_operating_income,non_operating_expenses,total_profit,income_tax,net_profit,time')
            ->where('id', $pf_id)
            ->find();

        return view()->assign('pf_res', $pf_res);
    }
    /**
     * 编辑现金流量
     */
    public function editcosh_flow()
    {
        if (request()->isPost()) {
            $data = $this->request->param();
            $res = Db::name('cash_flow')->where('id', $data['id'])->update($data);
            if ($res) {
                $this->success('修改现金流量成功', url('admin_category/people_cash_flow'), ['parent' => 1]);
            } else {
                $this->error('修改现金流量失败');
            }
        }
        $ecf_id = $this->request->param('id');
        $cfres = Db::name('cash_flow')
            ->field('id,net_cash_flow,investment_flow,net_cash_activities,exchange_rate_effect,increment,initial_balance,ending_balance,time')
            ->where('id', $ecf_id)
            ->find();

        $this->assign('cfres', $cfres);
        return $this->fetch();
    }
    /**
     * 编辑负债
    */
    public function editfinance()
    {
        if(request()->isPost()){
            $data = $this->request->param();
            $f_res = Db::name('asset_liability')->where('id',$data['id'])->update($data);
            if($f_res){
                $this->success('修改企业负债成功',url('admin_category/people_finance'),['parent'=>1]);
            }else{
                $this->error('修改企业负债失败');
            }
        }
        $fin_id = $this->request->param('id');
        $fres = Db::name('asset_liability')
            ->field('id,time,sum_flow_asets,sum_ord_fixed,sum_assets,sum_flow_payment,sum_fixed_liabilities,sum_liabilities,sum_rights_interests,sum_liabilities_equity')
            ->where('id', $fin_id)
            ->find();

        $this->assign('fres', $fres);
        return $this->fetch();
    }
    /**
     * 添加文章分类提交
     * @adminMenu(
     *     'name'   => '添加文章分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加文章分类提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $portalCategoryModel = new PortalCategoryModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'PortalCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $portalCategoryModel->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('AdminCategory/index'));
    }

    /**
     * 编辑文章分类
     * @adminMenu(
     *     'name'   => '编辑文章分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章分类',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $category = PortalCategoryModel::get($id)->toArray();

            $portalCategoryModel = new PortalCategoryModel();
            $categoriesTree      = $portalCategoryModel->adminCategoryTree($category['parent_id'], $id);

            $themeModel        = new ThemeModel();
            $listThemeFiles    = $themeModel->getActionThemeFiles('portal/List/index');
            $articleThemeFiles = $themeModel->getActionThemeFiles('portal/Article/index');

            $routeModel = new RouteModel();
            $alias      = $routeModel->getUrl('portal/List/index', ['id' => $id]);

            $category['alias'] = $alias;
            $this->assign($category);
            $this->assign('list_theme_files', $listThemeFiles);
            $this->assign('article_theme_files', $articleThemeFiles);
            $this->assign('categories_tree', $categoriesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }
    }

    /**
     * 编辑文章分类提交
     * @adminMenu(
     *     'name'   => '编辑文章分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑文章分类提交',
     *     'param'  => ''
     * )
     */
    public function report()
    {
       
       $where = [];
       if(request()->isPost()){
            $key = $this->request->param();
            $val = trim($key['keyword']);
            $where['name'] =['like',"%$val%"] ;
       }

        $qy_id = $this->request->param();
        if(!empty($qy_id['qy_id'])){
            
            $qy_name = Db::name('enterprise')->where('id',$qy_id['qy_id'])->value('unitname');
            session('qy_id',$qy_id['qy_id']);
            session('qy_name',$qy_name);
            $qy_id = $qy_id['qy_id'];
            $this->assign('qy_name',$qy_name);
            $this->assign('qy_id',$qy_id);
            
        }else{
            $qy_id = session('qy_id');
            $qy_name = session('qy_name');
            $this->assign('qy_name',$qy_name);
            $this->assign('qy_id',$qy_id);
        }

        $data = Db::name('enter_user')
            ->where($where)
            ->where('enter_id',$qy_id)
            ->where('del_type',0)
            ->order('id')
            ->paginate(10,false,['query'=>request()->param()]);

        $page = $data->render();
        $currentPage = $data->currentPage();
        $listRows = $data->listRows();
        $num = ($currentPage - 1) * $listRows + 1;

        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('num',$num);
        return $this->fetch('people_report');
    }
    //
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'PortalCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $portalCategoryModel = new PortalCategoryModel();

        $result = $portalCategoryModel->editCategory($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 文章分类选择对话框
     * @adminMenu(
     *     'name'   => '文章分类选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类选择对话框',
     *     'param'  => ''
     * )
     */
    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $portalCategoryModel = new PortalCategoryModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;

        $categoryTree = $portalCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

        $where      = ['delete_time' => 0];
        $categories = $portalCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 文章分类排序
     * @adminMenu(
     *     'name'   => '文章分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '文章分类排序',
     *     'param'  => ''
     * )
     */
    //查看利润
    public function see_profit()
    {
        $pid = $this->request->param('id');
        $profit = Db::name('profit')->where('id',$pid)->find();
        $this->assign('profit',$profit);
        return $this->fetch();
    }
    //查看现金流量
    public function see_cash_flow()
    {
        $cfid = $this->request->param('id');
        $cash = Db::name('cash_flow')->where('id',$cfid)->find();
        $this->assign('cash',$cash);
        return $this->fetch();
    }
    //查看负债
    public function see_finance()
    {
        $fid = $this->request->param('id');
        $finance = Db::name('asset_liability')->where('id',$fid)->find();
        $this->assign('finance',$finance);
        return $this->fetch();
    }

    public function listOrder()
    {
        parent::listOrders(Db::name('portal_category'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 删除文章分类
     * @adminMenu(
     *     'name'   => '删除文章分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除文章分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $portalCategoryModel = new PortalCategoryModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $portalCategoryModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
        //判断此分类有无子分类（不算被删除的子分类）
        $categoryChildrenCount = $portalCategoryModel->where(['parent_id' => $id, 'delete_time' => 0])->count();

        if ($categoryChildrenCount > 0) {
            $this->error('此分类有子类无法删除!');
        }

        $categoryPostCount = Db::name('portal_category_post')->where('category_id', $id)->count();

        if ($categoryPostCount > 0) {
            $this->error('此分类有文章无法删除!');
        }

        $data   = [
            'object_id'   => $findCategory['id'],
            'create_time' => time(),
            'table_name'  => 'portal_category',
            'name'        => $findCategory['name']
        ];
        $result = $portalCategoryModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            Db::name('recycleBin')->insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
