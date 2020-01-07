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
use think\Validate;
use app\user\model\UserModel;
use think\Session;

class ExcelController extends HomeBaseController
{
    public function out()
        {
            
            //导出
            $path = dirname(__FILE__); //找到当前脚本所在路径
            vendor("PHPExcel.PHPExcel.PHPExcel");
            vendor("PHPExcel.PHPExcel.Writer.IWriter");
            vendor("PHPExcel.PHPExcel.Writer.Abstract");
            vendor("PHPExcel.PHPExcel.Writer.Excel5");
            vendor("PHPExcel.PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.PHPExcel.IOFactory");
            $objPHPExcel = new \PHPExcel();
            $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

$a=input('id');
$sql = json_decode(htmlspecialchars_decode($a),true);
// print_r('<pre />');
// print_r($sql);
// die;
            // 实例化完了之后就先把数据库里面的数据查出来
            //$sql = db('enterprise')->select();//10
           
            // 设置表头信息
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '公司名称')
            ->setCellValue('B1', '法人代表')
            ->setCellValue('C1', '注册资本')
            ->setCellValue('D1', '联系人')
            ->setCellValue('E1', '联系方式（电话）')
            ->setCellValue('F1', '入园情况')
            ->setCellValue('G1', '注册地')
            ->setCellValue('H1', '对接人')
            ->setCellValue('I1', '时间');

            

            /*--------------开始从数据库提取信息插入Excel表中------------------*/

            $i=2;  //定义一个i变量，目的是在循环输出数据是控制行数
            $count = count($sql);  //计算有多少条数据
            for ($i = 2; $i <= $count+1; $i++) {
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $sql[$i-2]['unitname']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $sql[$i-2]['legalperson']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $sql[$i-2]['registermany']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sql[$i-2]['contacts']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $sql[$i-2]['information']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $sql[$i-2]['admission']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $sql[$i-2]['registeredplace']);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $sql[$i-2]['pickpeople']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $sql[$i-2]['createtime']);







            }

            
            /*--------------下面是设置其他信息------------------*/

            $objPHPExcel->getActiveSheet()->setTitle('productaccess');      //设置sheet的名称
            $objPHPExcel->setActiveSheetIndex(0);                   //设置sheet的起始位置
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');   //通过PHPExcel_IOFactory的写函数将上面数据写出来
            
            $PHPWriter = \PHPExcel_IOFactory::createWriter( $objPHPExcel,"Excel2007");
                
            header('Content-Disposition: attachment;filename="公司详情.xlsx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            
            $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
            
        }
    }