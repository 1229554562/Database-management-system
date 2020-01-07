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
use DateTime;

class AdminNewupController extends AdminBaseController
{
    public function people_profit()
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
                if(empty($excel_array[32][1])||strtotime($excel_array[32][1])==false){
                    $this->error('文件导入失败');
                }
                $times = DateTime::createFromFormat('m-d-y', $excel_array[32][1]);
                if ($times) {
                    $times_array = get_object_vars($times);

                }

                $data = [
                    'business_income'           =>      $excel_array[0][1],
                    'operating_cost'            =>      $excel_array[1][1],
                    'sales_tax_additional'      =>      $excel_array[2][1],
                    'excise_tax'                =>      $excel_array[3][1],
                    'sales_tax'                 =>      $excel_array[4][1],
                    'construction_tax'          =>      $excel_array[5][1],
                    'resource_tax'              =>      $excel_array[6][1],
                    'land_tax'                  =>      $excel_array[7][1],
                    'order_tax'                 =>      $excel_array[8][1],
                    'order_cost'                =>      $excel_array[9][1],
                    'selling_expenses'          =>      $excel_array[10][1],
                    'maintenance_cost'          =>      $excel_array[11][1],
                    'publicity_expenses'        =>      $excel_array[12][1],
                    'management_cost'           =>      $excel_array[13][1],
                    'startup_cost'              =>      $excel_array[14][1],
                    'business_entertainment'    =>      $excel_array[15][1],
                    'research_cost'             =>      $excel_array[16][1],
                    'financial_cost'            =>      $excel_array[17][1],
                    'interest_expenses'         =>      $excel_array[18][1],
                    'income_investment'         =>      $excel_array[19][1],
                    'operating_profit'          =>      $excel_array[20][1],
                    'non_operating_income'      =>      $excel_array[21][1],
                    'government_grants'         =>      $excel_array[22][1],
                    'non_operating_expenses'    =>      $excel_array[23][1],
                    'bad_debt_loss'             =>      $excel_array[24][1],
                    'bond_losses'               =>      $excel_array[25][1],
                    'share_loss'                =>      $excel_array[26][1],
                    'other_losses'              =>      $excel_array[27][1],
                    'tax_overdue_fine'          =>      $excel_array[28][1],
                    'total_profit'              =>      $excel_array[29][1],
                    'income_tax'                =>      $excel_array[30][1],
                    'net_profit'                =>      $excel_array[31][1],
                    'qid'                       =>      session('lrid'),
                    'time'                      =>      $times == false ? strtotime(date('Y-m',strtotime($excel_array[32][1]))) : strtotime(date('Y-m',strtotime($times_array['date']))),
                ];
                $up = Db::name('profit')->where('time',$data['time'])->where('qid',$data['qid'])->find();
                if($up){
                    $updata = Db::name('profit')->where('time',$data['time'])->where('qid',$data['qid'])->update($data);
                    if($updata){
                        $this->success('更新成功');
                    }else{
                        $this->error('更新失败');
                    }
                }
                if (Db::name('profit')->insert($data)) {
                    $this->success('导入数据成功');
                } else {
                    $this->error('导入文件失败');
                }
            } else {
                $this->error('文件导入失败!', '');
            }
        }
    }
    public static function excelTime($date, $time = true)
    {
        var_dump($date);
        exit;
        $date = $date > 25568 ? $date + 1 : 25569;
        $ofs = (70 * 365 + 17 + 2) * 86400;
        $d1 = date("Y-m-d H:i:s", ($date * 86400) - $ofs);
        $d1 = strtotime("-8hours", strtotime($d1));
        $d1 = date('Y-m-d H:i:s', $d1);
        $date = $d1;
        var_dump($date);
        exit;
        return $date;
    }

    public function people_cash_flow()
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
                if(empty($excel_array[35][1])||strtotime($excel_array[35][1]==false)){
                    $this->error('文件导入失败');
                }
                $times = DateTime::createFromFormat('d-m-y', $excel_array[35][1]);
                if ($times) {
                    $times_array = get_object_vars($times);
                }
                
                $data = [
                    'selling_cash'               =>      $excel_array[0][1],
                    'tax_refund'                 =>      $excel_array[1][1],
                    'active_cash'                =>      $excel_array[2][1],
                    'subtotal_active_cash'       =>      $excel_array[3][1],
                    'cash_for_goods'             =>      $excel_array[4][1],
                    'cash_to_employees'          =>      $excel_array[5][1],
                    'payment_taxes'              =>      $excel_array[6][1],
                    'ord_active_cashs'           =>      $excel_array[7][1],
                    'sub_active_cash'            =>      $excel_array[8][1],
                    'net_cash_flow'              =>      $excel_array[9][1],
                    'recovery_investment'        =>      $excel_array[10][1],
                    'Income_investment'          =>      $excel_array[11][1],
                    'net_cash'                   =>      $excel_array[12][1],
                    'disposal_subsidiarie'       =>      $excel_array[13][1],
                    'net_cash_subsidiaries'      =>      $excel_array[14][1],
                    'sum_investment_activitie'   =>      $excel_array[15][1],
                    'long_term_payment'          =>      $excel_array[16][1],
                    'investing_cash'             =>      $excel_array[17][1],
                    'ord_payment_subsidiary'     =>      $excel_array[18][1],
                    'ord_investment_activities'  =>      $excel_array[19][1],
                    'sum_investment_activities'  =>      $excel_array[20][1],
                    'investment_flow'            =>      $excel_array[21][1],
                    'absorb_investment'          =>      $excel_array[22][1],
                    'obtain_loans'               =>      $excel_array[23][1],
                    'ord_active_cash'            =>      $excel_array[24][1],
                    'inflow_subtotal'            =>      $excel_array[25][1],
                    'debt_repayment'             =>      $excel_array[26][1],
                    'dividend_distribution'      =>      $excel_array[27][1],
                    'ord_interest_cash'          =>      $excel_array[28][1],
                    'sum_cash_outflow'           =>      $excel_array[29][1],
                    'net_cash_activities'        =>      $excel_array[30][1],
                    'exchange_rate_effect'       =>      $excel_array[31][1],
                    'increment'                  =>      $excel_array[32][1],
                    'initial_balance'            =>      $excel_array[33][1],
                    'ending_balance'             =>      $excel_array[34][1],
                    'qid'                        =>      session('llid'),
                    'time'                       =>      $times == false ? strtotime(date('Y-m',strtotime($excel_array[35][1]))) : strtotime(date('Y-m',strtotime($times_array['date'])))
                ];
                $up = Db::name('cash_flow')->where('time',$data['time'])->where('qid',$data['qid'])->find();
                if($up){
                    $updata = Db::name('cash_flow')->where('time',$data['time'])->where('qid',$data['qid'])->update($data);
                    if($updata){
                        $this->success('更新成功');
                    }else{
                        $this->error('更新失败');
                    }
                }
                if (Db::name('cash_flow')->insert($data)) {
                    $this->success('导入数据成功');
                } else {
                    $this->error('导入文件失败');
                }
            } else {
                $this->error('文件导入失败!', '');
            }
        }
    }
    /**
     * profit_upload    利润模板下载
     * cash_upload      流量模板下载
     * finance_upload          负债模板下载
     */
    public function finance_upload()
    {
        $finance_array = [
            ['A' =>   '货币资金',      'C' =>  '其他非流动资产'],
            ['A' =>   '短期投资',      'C' =>  '其他非流动资产合计'],
            ['A' =>   '应收票据',      'C' =>  '资产总计'],
            ['A' =>   '应收账款',      'C' =>  '短期借款'],
            ['A' =>   '预付账款',      'C' =>  '应付票据'],
            ['A' =>   '应收股利',      'C' =>  '应付账款'],
            ['A' =>   '应收利息',      'C' =>  '预收账款'],
            ['A' =>   '其他应收款',    'C' =>  '应付职工薪酬'],
            ['A' =>   '存货',          'C' =>  '应交税费'],
            ['A' =>   '其中：原材料',   'C' =>  '应付利息'],
            ['A' =>   '在产品',        'C' =>  '应付利润'],
            ['A' =>   '库存商品',      'C' =>  '其他应付款'],
            ['A' =>   '周转材料',      'C' =>  '其他流动负债'],
            ['A' =>   '其他流动资产',   'C' =>  '流动负债合计'],
            ['A' =>   '流动资产合计',   'C' =>  '长期借款'],
            ['A' =>   '长期债券投资',  'C' =>  '长期应付款'],
            ['A' =>   '长期股权投资',  'C' =>  '递延收益'],
            ['A' =>   '固定资产原价',  'C' =>  '其他非流动负债'],
            ['A' =>   '减：累计折扣',       'C' =>  '非流动负债合计'],
            ['A' =>   '固定资产账面价值',   'C' =>  '负债合计'],
            ['A' =>   '在建工程',          'C' =>  '实收资本（或股本）'],
            ['A' =>   '工程物资',       'C' =>  '资本公积'],
            ['A' =>   '固定资产清理',   'C' =>  '盈余公积'],
            ['A' =>   '生产性生物资产', 'C' =>  '未分配利润'],
            ['A' =>   '无形资产',       'C' =>  '所有者权益（或股东权益）合计'],
            ['A' =>   '开发支出',       'C' =>  '负债和所有者权益（或股东权益）合计'],
            ['A' =>   '长期待摊费用' ,  'C'=>''],
        ];
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        $PHPSheet->setCellValue("A1", "负债跟资产名称")
            ->setCellValue("B1", "总额（元）")
            ->setCellValue("C1", "负债跟资产名称")
            ->setCellValue("D1", "总额（元）");

        $PHPSheet->getStyle('A')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $PHPSheet->getColumnDimension('A')->setWidth(30); //设置宽度
        $PHPSheet->getColumnDimension('B')->setWidth(20); //设置宽度
        $PHPSheet->getColumnDimension('C')->setWidth(35); //设置宽度
        $PHPSheet->getColumnDimension('D')->setWidth(20); //设置宽度
        //        $PHPSheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        //        $PHPSheet->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        foreach ($finance_array as $k => $v) {
            $PHPSheet->setCellValueExplicit('A' . ($k + 2), $v['A'], \PHPExcel_Cell_DataType::TYPE_STRING)
                //            ->setCellValue("A2", "2019/4")
                ->setCellValue("B" . ($k + 2), "")
                ->setCellValue("C" . ($k + 2), $v['C'])
                ->setCellValue("D" . ($k + 2), "");
        }

        $PHPSheet->setCellValueExplicit('A29','时间', \PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue("B29", "2000/00/01(请填写次类格式，格式为日期)");
        $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean(); // Added by me
        ob_start(); // Added by me
        header('Content-Disposition: attachment;filename="企业负债模板.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save('php://output');
    }
    //profit_upload
    public function profit_upload()
    {
        $profit_array = [
            ['A' =>   '营业收入'],
            ['A' =>   '减：营业成本'],
            ['A' =>   '营业税金及附加'],
            ['A' =>   '其中：消费税'],
            ['A' =>   '营业税'],
            ['A' =>   '城市维护建设税'],
            ['A' =>   '资源税'],
            ['A' =>   '土地增值税'],
            ['A' =>   '城镇土地使用税、房产税、车船税、印花税'],
            ['A' =>   '教育费附加、矿产资源补偿费、排污费'],
            ['A' =>   '销售费用'],
            ['A' =>   '其中：商品维修费'],
            ['A' =>   '广告费和业务宣传费'],
            ['A' =>   '管理费用'],
            ['A' =>   '其中：开办费'],
            ['A' =>   '业务招待费'],
            ['A' =>   '研究费用'],
            ['A' =>   '财务费用'],
            ['A' =>   '其中：利息费用（收入以“-”号填列）'],
            ['A' =>   '加：投资收益（损益以“-”号填列）'],
            ['A' =>   '营业利润（亏损以“-”号填列）'],
            ['A' =>   '加：营业外收入 '],
            ['A' =>   '其中：政府补助'],
            ['A' =>   '减：营业外支出 '],
            ['A' =>   '其中：坏账损失'],
            ['A' =>   '无法收回的长期债券投资损失 '],
            ['A' =>   '无法收回的长期股权投资损失'],
            ['A' =>   '自然灾害等不可抗力因素造成的损失'],
            ['A' =>   '税收滞纳金'],
            ['A' =>   '利润总额（亏损总额以“-”号填列）'],
            ['A' =>   '所得税费用'],
            ['A' =>   '净利润（净亏损以“-”号填列） '],
        ];
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        $PHPSheet->setCellValue("A1", "项目")
            ->setCellValue("B1", "本月金额（元）");

        $PHPSheet->getStyle('A')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $PHPSheet->getColumnDimension('A')->setWidth(40); //设置宽度
        $PHPSheet->getColumnDimension('B')->setWidth(20); //设置宽度
        //        $PHPSheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        //        $PHPSheet->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        foreach ($profit_array as $k => $v) {
            $PHPSheet->setCellValueExplicit('A' . ($k + 2), $v['A'], \PHPExcel_Cell_DataType::TYPE_STRING)
                //            ->setCellValue("A2", "2019/4")
                ->setCellValue("B" . ($k + 2), "");
        }
        $PHPSheet->setCellValueExplicit('A34', '时间', \PHPExcel_Cell_DataType::TYPE_STRING)
                 ->setCellValue("B34", "2000/00/01(请填写次类格式，格式为日期)");

        $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean(); // Added by me
        ob_start(); // Added by me
        header('Content-Disposition: attachment;filename="企业利润模板.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save('php://output'); 
    }
    //cash_upload
    public function cash_upload()
    {
        $cash_array = [
            ['A' =>   '销售商品、提供劳务收到的现金'],
            ['A' =>   '收到的税费返还'],
            ['A' =>   '收到其他与经营活动有关的现金'],
            ['A' =>   '经营活动现金流入小计'],
            ['A' =>   '购买商品、接受劳务支付的现金'],
            ['A' =>   '支付给职工以及为职工支付的现金'],
            ['A' =>   '支付的各项税费'],
            ['A' =>   '支付其他与经营活动有关的现金'],
            ['A' =>   '经营活动现金流出小计'],
            ['A' =>   '经营活动产生的现金流量净额'],
            ['A' =>   '收回投资收到的现金'],
            ['A' =>   '取得投资收益收到的现金'],
            ['A' =>   '处置固定资产、无形资产和其他长期资产收回的现金净额'],
            ['A' =>   '处置子公司及其他营业单位收到的现金净额'],
            ['A' =>   '收到其他与投资活动有关的现金'],
            ['A' =>   '投资活动现金流入小计'],
            ['A' =>   '购建固定资产、无形资产和其他长期资产支付的现金'],
            ['A' =>   '投资支付的现金'],
            ['A' =>   '取得子公司及其他营业单位支付的现金净额'],
            ['A' =>   '支付其他与投资活动有关的现金'],
            ['A' =>   '投资活动现金流出小计'],
            ['A' =>   '投资活动产生的现金流量净额'],
            ['A' =>   '吸收投资收到的现金'],
            ['A' =>   '取得借款收到的现金'],
            ['A' =>   '收到其他与筹资活动有关的现金'],
            ['A' =>   '筹资活动现金流入小计'],
            ['A' =>   '偿还债务支付的现金'],
            ['A' =>   '分配股利、利润或偿付利息支付的现金'],
            ['A' =>   '支付其他与筹资活动有关的现金'],
            ['A' =>   '筹资活动现金流出小计'],
            ['A' =>   '筹资活动产生的现金流量净额'],
            ['A' =>   '汇率变动对现金及现金等价物的影响'],
            ['A' =>   '现金及现金等价物净增加额'],
            ['A' =>   '期初现金及现金等价物余额'],
            ['A' =>   '期末现金及现金等价物余额'],
        ];
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $PHPSheet = $objPHPExcel->getActiveSheet();
        $PHPSheet->setCellValue("A1", "项目")
            ->setCellValue("B1", "总额（元）");

        $PHPSheet->getStyle('A')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $PHPSheet->getColumnDimension('A')->setWidth(45); //设置宽度
        $PHPSheet->getColumnDimension('B')->setWidth(20); //设置宽度
        //        $PHPSheet->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        //        $PHPSheet->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        foreach ($cash_array as $k => $v) {
            $PHPSheet->setCellValueExplicit('A' . ($k + 2), $v['A'], \PHPExcel_Cell_DataType::TYPE_STRING)
                //            ->setCellValue("A2", "2019/4")
                ->setCellValue("B" . ($k + 2), "");
        }
         $PHPSheet->setCellValueExplicit('A37', '时间', \PHPExcel_Cell_DataType::TYPE_STRING)
                //            ->setCellValue("A2", "2019/4")
                ->setCellValue("B37", "2000/00/01(请填写次类格式，格式为日期)");


        $PHPWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        ob_end_clean(); // Added by me
        ob_start(); // Added by me
        header('Content-Disposition: attachment;filename="企业现金流量模板.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save('php://output');
    }
}
