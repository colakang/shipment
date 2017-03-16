<?php
namespace app\index\controller;

use \think\Session;
use \think\View;
use \think\Input;
use \think\Controller;


class Logout extends controller
{
    public function index()
    {
	Session::delete('isLogin');
	Session::delete('uid');
	Session::delete('username');
	Session::delete('groups');
	return $this->success('退出成功', '/index/login');
    }
}
