<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Auth extends Controller
{
    /**
     * 权限控制
     *
     * @return \think\Response
     */
    public function index()
    {
        $id=session("user.user_id");

        if($id==null){
            return false;
        }
        return true;
    }


}
