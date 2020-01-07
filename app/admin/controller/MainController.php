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
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\Menu;

class MainController extends AdminBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *  后台欢迎页
     */
    public function index()
    {
        $enter_sum = Db::name('enterprise')->where('del_type',0)->count();  // 企业总数
        $regmany = Db::name('enterprise')->where('del_type',0)->sum('regmany');  // 总注册资金
        $profit = Db::name('profit')->alias('p')
                ->join('__ENTERPRISE__ en','p.qid = en.id')
                ->where('en.del_type',0)->sum('total_profit');  // 总利润
        $enter_user = Db::name('enter_user')->alias('u')
            ->join('__ENTERPRISE__ en', 'u.enter_id = en.id')
            ->where('en.del_type',0)
            ->where('u.del_type',0)->count();  // 报备人数
        /*=====================================================================*/
       // $season = ceil(date('n')/3); //    '2019/4'
        $season = ceil(date('n'));

        $pre_season_start = date('Y-m-01',mktime(0,0,0,($season - 2) * 3 +1,1,date('Y')))."0:0:0";
        $pre_season_end = date('Y-m-t',mktime(0,0,0,($season - 1) * 3,1,date('Y')))." 23:59:59";
        $pre_add = Db::name('enterprise')->whereBetween('add_time',[strtotime($pre_season_start),strtotime($pre_season_end)])->where('del_type',0)->count();  // 上季度新增企业

        $now_reason = ceil((date('n')))-1;

        if ($now_reason == 0){
            $pre_season = (date('Y')-1).'/'.date('m');
        }else{
            $pre_season = date('Y').'/'.$now_reason;
        }  // 上个季度

        $pre_sum_sales = Db::name('profit')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.qid = en.id')
            ->where('en.del_type',0)
            ->where('op.time',strtotime($pre_season.'/'.'01'))
            ->sum('op.business_income');  // 上月营业总收入
        $pre_sum_profit = Db::name('profit')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.qid = en.id')
            ->where('en.del_type',0)
            ->where('op.time',strtotime($pre_season.'/'.'01'))
            ->sum('op.total_profit');  // 上月总利润
        $pre_report = Db::name('enter_operate')->alias("op")
            ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
            ->where('en.del_type',0)
            ->where('op.enter_time',$pre_season)
            ->where('op.is_report',1)
            ->count();  // 上季度已报备企业

        $t_reason = ceil((date('n')))-2;
        if ($t_reason == 0){
            $last_season = (date('Y')-1).'/'.date('m');
        }elseif($t_reason < 0){
            $last_season = (date('Y')-1).'/'.(date('m')-1);
        }else{
            $last_season = date('Y').'/'.$t_reason;
        }  // 上上个季度 



        $last_sum_sales = Db::name('profit')->alias("op")
            ->join('__ENTERPRISE__ en', 'op.qid = en.id')
            ->where('en.del_type',0)
            ->where('op.time',strtotime($last_season.'/'.'01'))
            ->sum('op.business_income');  // 上上季度营业总收入

        if ($last_sum_sales == 0){
            $sales_rate = 0;
        }else{
            $sales_rate = round(($pre_sum_sales-$last_sum_sales)/$last_sum_sales,2)*100;
        } // 营业总收入较上季度同比增长（上个季度-上上个季度）/上上个季度

        $last_sum_profit = Db::name('profit')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.qid = en.id')
            ->where('en.del_type',0)
            ->where('op.time',strtotime($last_season.'/'.'01'))
            ->sum('op.total_profit');  // 上上季度总利润

        if ($last_sum_profit == 0){
            $profit_rate = 0; 
        }else{
            $profit_rate = round(($pre_sum_profit-$last_sum_profit)/$last_sum_profit,2)*100;
        }  // 总利润较上季度同比增长

        $this->assign('enter_sum',$enter_sum);
        $this->assign('regmany',$regmany);
        $this->assign('profit',$profit);
        $this->assign('enter_user',$enter_user);
        $this->assign('pre_add',$pre_add);
        $this->assign('pre_sum_salse',$pre_sum_sales);
        $this->assign('pre_sum_profit',$pre_sum_profit);
        $this->assign('pre_report',$pre_report);
        $this->assign('sales_rate',$sales_rate);
        $this->assign('profit_rate',$profit_rate);

        /***********************************************************/



        $pre_sum_assets = Db::name('enter_operate')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
            ->where('en.del_type',0)
            ->where('op.enter_time',$pre_season)
            ->sum('op.assets');  // 上个季度资产总额
        $pre_sum_tax = Db::name('enter_operate')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
            ->where('en.del_type',0)
            ->where('op.enter_time',$pre_season)
            ->sum('op.tax');  // 上个季度资产总额

        $this->assign('pre_sum_assets',$pre_sum_assets);
        $this->assign('pre_sum_tax',$pre_sum_tax);  // 上个季度 柱状图

        /*$last_sum_assets = Db::name('enter_operate')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
            ->where('en.del_type',0)
            ->where('op.enter_time',$pre_season)
            ->sum('op.assets');  // 上上个季度资产总额
        $last_sum_tax = Db::name('enter_operate')
            ->alias("op")
            ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
            ->where('en.del_type',0)
            ->where('op.enter_time',$pre_season)
            ->sum('op.tax');  // 上上个季度资产总额

        $this->assign('last_sum_assets',$last_sum_assets); // 资产
        $this->assign('last_sum_sales',$last_sum_sales);  // 营业
        $this->assign('last_sum_profit',$last_sum_profit);  // 利润
        $this->assign('last_sum_tax',$last_sum_tax);  // 纳税 // 上上个季度 柱状图


        $enter_time = Db::name('enter_operate')->field('enter_time')->order('enter_time desc')->limit('8')->group('enter_time')->select();
        if (isset($enter_time[2])){
            $last_sum_assets1 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[2]['enter_time'])
                ->sum('op.assets');  // 上上个季度资产总额
            $last_sum_tax1 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[2]['enter_time'])
                ->sum('op.tax');  // 上上个季度资产总额
            $last_sum_sales1 = Db::name('enter_operate')->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[2]['enter_time'])
                ->sum('op.sales');  // 上上季度营业总收入
            $last_sum_profit1 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[2]['enter_time'])
                ->sum('op.profit');  // 上上季度总利润
        }else{
            $last_sum_assets1 = 0;
            $last_sum_sales1 = 0;
            $last_sum_profit1 = 0;
            $last_sum_tax1 = 0;
        }

        $this->assign('last_sum_assets1',$last_sum_assets1); // 资产
        $this->assign('last_sum_sales1',$last_sum_sales1);  // 营业
        $this->assign('last_sum_profit1',$last_sum_profit1);  // 利润
        $this->assign('last_sum_tax1',$last_sum_tax1);  // 纳税 // 上上上个季度 柱状图

        if (isset($enter_time[3])){
            $last_sum_assets2 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[3]['enter_time'])
                ->sum('op.assets');  // 上上个季度资产总额
            $last_sum_tax2 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[3]['enter_time'])
                ->sum('op.tax');  // 上上个季度资产总额
            $last_sum_sales2 = Db::name('enter_operate')->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[3]['enter_time'])
                ->sum('op.sales');  // 上上季度营业总收入
            $last_sum_profit2 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[3]['enter_time'])
                ->sum('op.profit');  // 上上季度总利润
        }else{
            $last_sum_assets2 = 0;
            $last_sum_sales2 = 0;
            $last_sum_profit2 = 0;
            $last_sum_tax2 = 0;
        }

        $this->assign('last_sum_assets2',$last_sum_assets2); // 资产
        $this->assign('last_sum_sales2',$last_sum_sales2);  // 营业
        $this->assign('last_sum_profit2',$last_sum_profit2);  // 利润
        $this->assign('last_sum_tax2',$last_sum_tax2);  // 纳税 // 上上上上个季度 柱状图

        if (isset($enter_time[4])){
            $last_sum_assets3 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[4]['enter_time'])
                ->sum('op.assets');  // 上上个季度资产总额
            $last_sum_tax3 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[4]['enter_time'])
                ->sum('op.tax');  // 上上个季度资产总额
            $last_sum_sales3 = Db::name('enter_operate')->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[4]['enter_time'])
                ->sum('op.sales');  // 上上季度营业总收入
            $last_sum_profit3 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[4]['enter_time'])
                ->sum('op.profit');  // 上上季度总利润
        }else{
            $last_sum_assets3 = 0;
            $last_sum_sales3 = 0;
            $last_sum_profit3 = 0;
            $last_sum_tax3 = 0;
        }

        $this->assign('last_sum_assets3',$last_sum_assets3); // 资产
        $this->assign('last_sum_sales3',$last_sum_sales3);  // 营业
        $this->assign('last_sum_profit3',$last_sum_profit3);  // 利润
        $this->assign('last_sum_tax3',$last_sum_tax3);  // 纳税 // 上上上上上个季度 柱状图

        if (isset($enter_time[5])){
            $last_sum_assets4 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[5]['enter_time'])
                ->sum('op.assets');  // 上上个季度资产总额
            $last_sum_tax4 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[5]['enter_time'])
                ->sum('op.tax');  // 上上个季度资产总额
            $last_sum_sales4 = Db::name('enter_operate')->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[5]['enter_time'])
                ->sum('op.sales');  // 上上季度营业总收入
            $last_sum_profit4 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[5]['enter_time'])
                ->sum('op.profit');  // 上上季度总利润
        }else{
            $last_sum_assets4 = 0;
            $last_sum_sales4 = 0;
            $last_sum_profit4 = 0;
            $last_sum_tax4 = 0;
        }

        $this->assign('last_sum_assets4',$last_sum_assets4); // 资产
        $this->assign('last_sum_sales4',$last_sum_sales4);  // 营业
        $this->assign('last_sum_profit4',$last_sum_profit4);  // 利润
        $this->assign('last_sum_tax4',$last_sum_tax4);  // 纳税 // 上上上上上个季度 柱状图

        if (isset($enter_time[6])){
            $last_sum_assets5 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[6]['enter_time'])
                ->sum('op.assets');  // 上上个季度资产总额
            $last_sum_tax5 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[6]['enter_time'])
                ->sum('op.tax');  // 上上个季度资产总额
            $last_sum_sales5 = Db::name('enter_operate')->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[6]['enter_time'])
                ->sum('op.sales');  // 上上季度营业总收入
            $last_sum_profit5 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[6]['enter_time'])
                ->sum('op.profit');  // 上上季度总利润
        }else{
            $last_sum_assets5 = 0;
            $last_sum_sales5 = 0;
            $last_sum_profit5 = 0;
            $last_sum_tax5 = 0;
        }

        $this->assign('last_sum_assets5',$last_sum_assets5); // 资产
        $this->assign('last_sum_sales5',$last_sum_sales5);  // 营业
        $this->assign('last_sum_profit5',$last_sum_profit5);  // 利润
        $this->assign('last_sum_tax5',$last_sum_tax5);  // 纳税 // 上上上上上个季度 柱状图

        if (isset($enter_time[7])){
            $last_sum_assets6 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[7]['enter_time'])
                ->sum('op.assets');  // 上上个季度资产总额
            $last_sum_tax6 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[7]['enter_time'])
                ->sum('op.tax');  // 上上个季度资产总额
            $last_sum_sales6 = Db::name('enter_operate')->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[7]['enter_time'])
                ->sum('op.sales');  // 上上季度营业总收入
            $last_sum_profit6 = Db::name('enter_operate')
                ->alias("op")
                ->join('__ENTERPRISE__ en', 'op.enter_id = en.id')
                ->where('en.del_type',0)
                ->where('op.enter_time',$enter_time[7]['enter_time'])
                ->sum('op.profit');  // 上上季度总利润
        }else{
            $last_sum_assets6 = 0;
            $last_sum_sales6 = 0;
            $last_sum_profit6 = 0;
            $last_sum_tax6 = 0;
        }

        $this->assign('last_sum_assets6',$last_sum_assets6); // 资产
        $this->assign('last_sum_sales6',$last_sum_sales6);  // 营业
        $this->assign('last_sum_profit6',$last_sum_profit6);  // 利润
        $this->assign('last_sum_tax6',$last_sum_tax6);  // 纳税 // 上上上上上个季度 柱状图*/

        $sea = '';
        for ($i=$season;$i>0;$i--){

            $sea .= date('Y').'/'.$i.',';
            if ($i == 1){

                for($j=12;$j>0;$j--){
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
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('business_income');
               
            $money[$k]['operating_cost'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('operating_cost');

            $money[$k]['total_profit'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('total_profit');

            $money[$k]['income_tax'] = Db::name('profit')
                ->alias("p")
                ->join('__ENTERPRISE__ en', 'p.qid = en.id')
                ->where('en.del_type',0)
                ->where('p.time',strtotime($v.'/'.'01'))
                ->sum('income_tax');
            //=====================================================================流量
            $cashf[$k]['net_cash_flow'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('net_cash_flow');
            
            $cashf[$k]['investment_flow'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('investment_flow');
            
            $cashf[$k]['net_cash_activities'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('net_cash_activities');

            $cashf[$k]['exchange_rate_effect'] = Db::name('cash_flow')
                ->alias("cf")
                ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
                ->where('en.del_type',0)
                ->where('cf.time',strtotime($v.'/'.'01'))
                ->sum('exchange_rate_effect');
            //======================================================
            $asset[$k]['sum_assets'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_assets');

            $asset[$k]['sum_liabilities'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_liabilities');
            
            $asset[$k]['sum_rights_interests'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_rights_interests');

            $asset[$k]['sum_liabilities_equity'] = Db::name('asset_liability')
                ->alias("a")
                ->join('__ENTERPRISE__ en', 'a.qid = en.id')
                ->where('en.del_type',0)
                ->where('a.time',strtotime($v.'/'.'01'))
                ->sum('sum_liabilities_equity');           
         
        }
        // $mtime = strtotime(date("Y-m", strtotime("-8 month")));
        // $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));//获取本月月末的时间戳
        // $time = strtotime(date('Y/m',time()));//月初的
        // $where = [];
        // $where['p.time'] = ['between time',[$mtime,$endThismonth]];
        
        // $wherecf['cf.time']  = ['between time',[$mtime,$endThismonth]];
        // $wherea['a.time']  = ['between time',[$mtime,$endThismonth]];
        //=====================利润
        // $profit = Db::name('profit')
        //     ->alias("p")
        //     ->field('p.business_income,p.operating_cost,p.total_profit,p.income_tax,p.time')
        //     ->join('__ENTERPRISE__ en', 'p.qid = en.id')
        //     ->where('en.del_type',0)
        //     ->where($where)
        //     ->order("p.time desc")
        //     ->select();
        //     var_dump($profit);exit;
        //=====================流量
        // $cashf = Db::name('cash_flow')
        //     ->alias("cf")
        //     ->field('cf.net_cash_flow,cf.investment_flow,cf.net_cash_activities,cf.exchange_rate_effect')
        //     ->join('__ENTERPRISE__ en', 'cf.qid = en.id')
        //     ->where('en.del_type',0)
        //     ->where($wherecf)
        //     ->select();
        // //======================负债
        // $asset = Db::name('asset_liability')
        // ->alias("a")
        // ->field('a.sum_assets,a.sum_liabilities,a.sum_rights_interests,a.sum_liabilities_equity')
        // ->join('__ENTERPRISE__ en', 'a.qid = en.id')
        // ->where('en.del_type',0)
        // ->where($wherea)
        // ->select();  

        $this->assign('money',json_encode($money));
        $this->assign('cashf',json_encode($cashf));
        $this->assign('asset',json_encode($asset));
        $this->assign('eight',json_encode($eight_sea));
        $this->assign('season',$season);
        return $this->fetch();
    }

    public function dashboardWidget()
    {
        $dashboardWidgets = [];
        $widgets          = $this->request->param('widgets/a');
        if (!empty($widgets)) {
            foreach ($widgets as $widget) {
                if ($widget['is_system']) {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 1]);
                } else {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 0]);
                }
            }
        }

        cmf_set_option('admin_dashboard_widgets', $dashboardWidgets, true);

        $this->success('更新成功!');

    }

}
