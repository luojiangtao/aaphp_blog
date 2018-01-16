<?php
    header('Content-type:text/html;charset=utf-8');
    use aaphp\AA;
var_dump($_SERVER);die;
	// 调试模式开启
	// define("DEBUG", false);
	define("DEBUG", true);
	// 引入框架入口文件
	require_once("./aaphp/AA.php");
//	执行框架入口
//    new \aaphp\AA();
//    \aaphp\AA::AA();
    AA::start();
?>