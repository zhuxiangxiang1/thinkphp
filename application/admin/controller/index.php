<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Index extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        检查是否存在登录信息
        if(empty(session("user.user_id")) || session("user.user_id")==null){
            $this->redirect('/admin/login');
        }
        return view();
    }


}
