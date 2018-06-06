<?php
use think\Route;

//后台主页面
Route::any("","admin/Index/index");
Route::any("/","admin/Index/index");

//后台主页面
Route::any("/admin/index","admin/Index/index");

//后台登录页面
Route::get("/admin/login","admin/Login/index");

Route::post("/admin/login","admin/Login/login");

//登出
Route::any("/admin/logout","admin/Login/logout");
//运输 列表
Route::any("/admin/yunshu","admin/Index/yunshu");
//运输 添加页
Route::get("/admin/addyunshu","admin/Index/addyunshu");
//运输 添加
Route::post("/admin/addyunshu","admin/Index/saveyunshu");
//运输 删除
Route::post("/admin/deleteyunshu","admin/Index/deleteyunshu");


//补给 列表
Route::get("/admin/bujilist","admin/Index/bujiList");
//补给 添加页
Route::get("/admin/addbuji","admin/Index/addbuji");

//excel 导出
Route::any("admin/getExcel","admin/Index/getExcel");

