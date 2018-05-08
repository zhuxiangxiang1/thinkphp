<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;

class Index extends Controller
{

    public $uuu="";

    /***
     *  检查权限
     *  zxx 2018-3-28
     */
    public function __construct(Request $request)
    {   
        if($_SERVER["HTTP_HOST"] == "tp.com"){
            $this->uuu="/index.php";
        }

        $auth=new Auth();
        $islogin=$auth->index();
        if(!$islogin){
            $this->redirect($this->uuu."/admin/login");
        }
 
    }


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        检查是否存在登录信息
//        if(empty(session("user.user_id")) || session("user.user_id")==null){
//            $this->redirect('/index.php/admin/login');
//        }
        return view();
    }

    /***
     * 运输项 展示
     * zxx 2018-3-26
     */
    public function yunshu()
    {

        //查出所有数据
        $data=Db::table("tp_yunshu")
            ->alias(['tp_yunshu'=>'y','tp_drive'=>'d','tp_carnumber'=>'c'])
            ->join('tp_drive',"d.id=y.drive")
            ->join('tp_carnumber',"c.id=y.carnumber")
            ->field("y.*,d.drive,c.carnumber")
            ->where(['status'=>0,'type'=>0])->select();
        if(!empty($data)){
            foreach ($data as $k=>$v){
                if($v['tixiang_place']==1){
                    $data[$k]['tixiang_place']="海门";
                }elseif ($v['tixiang_place']==2){
                    $data[$k]['tixiang_place']="龙门";
                }elseif ($v['tixiang_place']==3){
                    $data[$k]['tixiang_place']="大麦屿";
                }elseif ($v['tixiang_place']==4){
                    $data[$k]['tixiang_place']="温州";
                }

                if($v['huanxiang_place']==1){
                    $data[$k]['huanxiang_place']="海门";
                }elseif ($v['huanxiang_place']==2){
                    $data[$k]['huanxiang_place']="龙门";
                }elseif ($v['huanxiang_place']==3){
                    $data[$k]['huanxiang_place']="大麦屿";
                }elseif ($v['huanxiang_place']==4){
                    $data[$k]['huanxiang_place']="温州";
                }

                if($v['mudi']==1){
                    $data[$k]['mudi']="重出";
                }elseif ($v['mudi']==2){
                    $data[$k]['mudi']="重进";
                }elseif ($v['mudi']==3){
                    $data[$k]['mudi']="修箱";
                }elseif ($v['mudi']==4){
                    $data[$k]['mudi']="特殊";
                }


                if($v['size']==1){
                    $data[$k]['size']="20GP";
                }elseif ($v['size']==2){
                    $data[$k]['size']="40HC";
                }elseif ($v['size']==3){
                    $data[$k]['size']="40GP";
                }elseif ($v['size']==4){
                    $data[$k]['size']="45GP";
                }
            }
        }

        return view('',['data'=>$data]);
    }

    /***
     * 添加运输页
     * zxx 2018-3-27
     */
    public function addyunshu()
    {

        $id=session('yunshu_id');
        $res=array();

        //判断是否存在运输id 如果存在则为修改
        if(!empty($id)){
            $res=DB::table("tp_yunshu")->where("id",$id)->find();
        }


        //驾驶人信息
        $drive=DB::table("tp_drive")->select();

        //车牌信息
        $carnumber=DB::table("tp_carnumber")->select();
        //当前时间
        $now=date("Y-m-d H:i",time());
        return view("",['res'=>$res,'now'=>$now,'drive'=>$drive,'carnumber'=>$carnumber]);
    }

    /***
     * 保存 运输信息
     * zxx 2018-3-27
     */
    public function saveyunshu(Request $request)
    {    

        $yunshu_id = session("yunshu_id");
        if(empty($yunshu_id) || $yunshu_id==null){
            //接受参数并保存(新增)
            $res=DB::table("tp_yunshu")->insert($request->post());
        }else{
            //保存
            $id=session("yunshu_id");
            $res=DB::table("tp_yunshu")->where('id',$id)->update($request->post());
        }

        if($res==1){
            //清除修改的缓存
            session("yunshu_id",null);
            //插入成功 判断修改／新增的是运输 还是 补给
            if($request->post("type") == 0){
                $this->success("保存成功",$this->uuu."/admin/yunshu");
            }else{
                $this->success("保存成功",$this->uuu."/admin/bujilist");
            }

        }else{
            $this->success("保存失败，请检查参数");
        }
    }

    /***
     * 删除记录
     * zxx 2018-3-27
     */
    public function deleteyunshu(Request $request)
    {

        //接受参数并保存
        if($request->post('type')==0){
            //删除
            $res=DB::table("tp_yunshu")->where('id',$request->post('id'))->update(['status'=>1]);
        }else{
            //设置session

           $res= session("yunshu_id",$request->post('id'));

        }


        if($res==1){
            //插入成功
            $this->success("删除成功",$this->uuu."/admin/yunshu");
        }else{
            $this->success("删除失败，请检查参数");
        }
    }


    /***
     * 补给list
     * zxx 2018-3-28
     */
    public function bujiList(){


        $data=DB::table("tp_yunshu")
            ->alias(["tp_yunshu"=>"y","tp_drive"=>"d","tp_carnumber"=>"c"])
            ->join("tp_drive","d.id=y.drive")
            ->join("tp_carnumber","c.id=y.carnumber")
            ->field('y.*,d.drive,c.carnumber')
            ->where(['status'=>0,'type'=>1])
            ->select();


        if(!empty($data)){
            foreach ($data as $k=>$v){
                if($v['buji_mudi']==1){
                    $data[$k]['buji_mudi']="加油";
                }elseif ($v['buji_mudi']==2){
                    $data[$k]['buji_mudi']="过路费";
                }elseif ($v['buji_mudi']==3){
                    $data[$k]['buji_mudi']="其他报销";
                }

            }
        }


        return view('',['data'=>$data]);
    }

    /***
     * 添加补给页
     * zxx 2018-3-28
     */
    public function addbuji(){
        $res=array();
        $now=date("Y-m-d H:i",time());
        $id=session('yunshu_id');

        //驾驶人信息
        $drive=DB::table("tp_drive")->select();

        //车牌信息
        $carnumber=DB::table("tp_carnumber")->select();

        //判断是否存在运输id 如果存在则为修改
        if(!empty($id)){
            $res=DB::table("tp_yunshu")->where("id",$id)->find();
        }

        return view("",['res'=>$res,'now'=>$now,'drive'=>$drive,'carnumber'=>$carnumber]);
    }

    /***
     * 导出 信息
     * zxx 2018-3-29
     */
    public function getExcel(){


        $objPHPExcel=new \PHPExcel();

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(35);
        // $objPHPExcel->getActiveSheet()->getDefaultColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension("A")->setWidth(18);

        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('P')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('R')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('S')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('T')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('U')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('V')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('W')->getAlignment()->setHorizontal
        (\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth('1');//自动填充到页面的宽度

        //表格信息
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "运输/补给")

            ->setCellValue('B1', "车号")
            ->setCellValue('C1', "驾驶员及电话")
            ->setCellValue('D1', "日期")
            ->setCellValue('E1', "出场时间")
            ->setCellValue('F1', "进场时间")
            ->setCellValue('G1', "提箱地点")
            ->setCellValue('H1', "还箱地点")
            ->setCellValue('I1', "船名／航次")
            ->setCellValue('J1', "箱号")
            ->setCellValue('K1', "尺寸")
            ->setCellValue('L1', "营运人")
            ->setCellValue('N1', "提单号")
            ->setCellValue('M1', "铅封号")
            ->setCellValue('O1', "目的")
            ->setCellValue('P1', "货代")
            ->setCellValue('Q1', "地址")
            ->setCellValue('R1', "对接方")
            ->setCellValue('S1', "联系方式")
            ->setCellValue('T1', "补给时间")
            ->setCellValue('U1', "补给目的")
            ->setCellValue('V1', "补给详细")
            ->setCellValue('W1', "备注")
        ;

        //获取所有信息
        $data=DB::table("tp_yunshu")
            ->alias(["tp_yunshu"=>"y","tp_drive"=>"d","tp_carnumber"=>"c"])
            ->join("tp_drive","d.id=y.drive")
            ->join("tp_carnumber","c.id=y.carnumber")
            ->where("status",0)
            ->field("y.*,d.drive,c.carnumber")
            ->select();

        if(empty($data)){
            $this->error("暂无数据导出",$this->uuu."/admin/index");
        }

        //整合数据
        foreach ($data as $k=>$v){
            if($v['tixiang_place']==1){
                $data[$k]['tixiang_place']="海门";
            }elseif ($v['tixiang_place']==2){
                $data[$k]['tixiang_place']="龙门";
            }elseif ($v['tixiang_place']==3){
                $data[$k]['tixiang_place']="大麦屿";
            }elseif ($v['tixiang_place']==4){
                $data[$k]['tixiang_place']="温州";
            }

            if($v['huanxiang_place']==1){
                $data[$k]['huanxiang_place']="海门";
            }elseif ($v['huanxiang_place']==2){
                $data[$k]['huanxiang_place']="龙门";
            }elseif ($v['huanxiang_place']==3){
                $data[$k]['huanxiang_place']="大麦屿";
            }elseif ($v['huanxiang_place']==4){
                $data[$k]['huanxiang_place']="温州";
            }

            if($v['mudi']==1){
                $data[$k]['mudi']="重出";
            }elseif ($v['mudi']==2){
                $data[$k]['mudi']="重进";
            }elseif ($v['mudi']==3){
                $data[$k]['mudi']="修箱";
            }elseif ($v['mudi']==4){
                $data[$k]['mudi']="特殊";
            }


            if($v['size']==1){
                $data[$k]['size']="20GP";
            }elseif ($v['size']==2){
                $data[$k]['size']="40HC";
            }elseif ($v['size']==3){
                $data[$k]['size']="40GP";
            }elseif ($v['size']==4){
                $data[$k]['size']="45GP";
            }


            if($v['buji_mudi']==1){
                $data[$k]['buji_mudi']="加油";
            }elseif ($v['buji_mudi']==2){
                $data[$k]['buji_mudi']="过路费";
            }elseif ($v['buji_mudi']==3){
                $data[$k]['buji_mudi']="其他报销";
            }

            if($v['type']==0){
                $data[$k]['type']="运输";
            }elseif ($v['type']==1){
                $data[$k]['type']="补给";
            }
        }

        $num=2;
        //循环插入
        foreach ($data  as $k=>$v){
            $objPHPExcel->setActiveSheetIndex(0)
                //Excel的第A列，uid是你查出数组的键值，下面以此类推
                ->setCellValue('A'.$num, $v['type'])
                ->setCellValue('B'.$num, $v['carnumber'])
                ->setCellValue('C'.$num, $v['drive'])
                ->setCellValue('D'.$num, $v['nowdate'])
                ->setCellValue('E'.$num, $v['date_in'])
                ->setCellValue('F'.$num, $v['date_out'])
                ->setCellValue('G'.$num, $v['tixiang_place'])
                ->setCellValue('H'.$num, $v['huanxiang_place'])
                ->setCellValue('I'.$num, $v['ship_name'])
                ->setCellValue('J'.$num, $v['jizhuang_code'])
                ->setCellValue('K'.$num, $v['size'])
                ->setCellValue('L'.$num, $v['yingyunren'])
                ->setCellValue('N'.$num, $v['tidan_code'])
                ->setCellValue('M'.$num, $v['qianfen_code'])
                ->setCellValue('O'.$num, $v['mudi'])
                ->setCellValue('P'.$num, $v['huodai'])
                ->setCellValue('Q'.$num, $v['place'])
                ->setCellValue('R'.$num, $v['duijiefang'])
                ->setCellValue('S'.$num, $v['lianxifangshi'])
                ->setCellValue('T'.$num, $v['buji_time'])
                ->setCellValue('U'.$num, $v['buji_mudi'])
                ->setCellValue('V'.$num, $v['buji_desc'])
                ->setCellValue('W'.$num, $v['comment']);

            $num++;
        }


        ob_end_clean();//清除缓冲区,避免乱码
        @$objPHPExcel->getActiveSheet()->setTitle("的兑换码");
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.date("Y-m-d",time()).'_统计.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        $this->redirect($this->uuu."/admin/index");
    }
}
