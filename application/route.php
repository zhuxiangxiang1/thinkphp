<?php
use think\Route;

//后台主页面
Route::any("/admin/index","admin/Index/index");

//后台登录页面
Route::any("/admin/login","admin/Login/index");