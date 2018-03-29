<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\user;

class Login extends Controller
{
    /**
     * zxx 2018-3-26
     * 登录主页
     */
    public function index()
    {
//
        return view();
    }

    /***
     *
     * zxx 2018-3-26
     *
     * 登录
     */
    public function  login(Request $request)
    {

        $name=$request->post("username");
        $password=$request->post("password");

        if(empty($name) || empty($password)){
            $this->error("用户名或密码不能为空");
        }

        //查询数据表
        $password=md5($password);

        $user=new user();

//        查找数据库中是否存在该记录
        $res=$user->where(["user_name"=>$name,"user_password"=>$password])->find();


        if(empty($res)){
            $this->error("账号或者密码错误");
        }

//        登录成功 设置session
        session("user",$res);
        return $this->redirect("/index.php/admin/index");
    }

    /***
     *
     * zxx 2018-3-26
     * 退出登录
     */
    public function logout()
    {

        session("user",null);
        return $this->success("退出成功");
    }
}
