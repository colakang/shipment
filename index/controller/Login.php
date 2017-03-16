<?php
namespace app\index\controller;

use \think\Session;
use \think\View;
use \think\Input;
use \think\Controller;
use \think\Config;

require_once("/data0/htdocs/shipment/extend/captcha/src/Captcha.php");

//session_start();

class Login extends controller
{
    public function index()
    {
	//$rules = new \app\index\model\AuthRule;

	//$Event = controller('Shipment','event');
	//$data = $Event->buyLabel($from,$to,$weight);
	//return $data;
	switch(true) {
	case (Session::has('isLogin')):
		return $this->success('登陆成功', '/');
		break;
	case (empty($_POST)):
		$captchaId = mt_rand(1,255);
		$view = new View();
		$view->captcha = '/captcha/index?id='.$captchaId;
		$view->captchaId = $captchaId;
		Session::set('captchaId',$captchaId);
		return $view->fetch();
		break;
	default:	
		$username = Input::post('user');
		$password = Input::post('password');
		$captchaCode = Input::post('verify');
		$captchaId = Input::post('vId');
		$captcha = new \think\captcha\Captcha((array)Config::get('captcha'));
        	$isCaptcha = $captcha->check($captchaCode,$captchaId);
		$user = new \app\index\model\AuthUser;
		$group = new \app\index\model\AuthGroup;
		$isUser = $user::where('username',$username)->where('password',md5($password))->value('id');
		$isUserName = $user::where('username',$username)->where('password',md5($password))->value('username');
		$groups = $group::where('rules','like','%'.$isUser.',%')->column('id');
		if (empty($isUser) or !$isCaptcha)
		{
			return $this->error('用户名或密码或验证码错误');
		} else {
			
			Session::set('isLogin',true);
			Session::set('uid',$isUser);
			Session::set('username',$isUserName);
			Session::set('groups',$groups);
			return $this->success('登陆成功', '/');
    		}
   	}
   }
}
