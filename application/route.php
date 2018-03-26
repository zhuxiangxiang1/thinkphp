<?php
use think\Route;

//后台主页面
Route::any("/admin/index","admin/Index/index");

//后台登录页面
Route::get("/admin/login","admin/Login/index");

Route::post("/admin/login","admin/Login/login");

//登出
Route::any("/admin/logout","admin/Login/logout");

