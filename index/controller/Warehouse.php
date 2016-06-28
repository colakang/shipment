<?php
namespace app\index\controller;

use \think\Session;
use \think\View;
use \think\Input;
use \think\Controller;
use \think\Config;

class Warehouse extends controller
{
    public function index()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$warehouse = controller('Warehouse','event');
	$data = urldecode(json_encode($warehouse->getRow()));
	$data = str_replace('"status":1','"status":"待处理"',$data);
	$data = str_replace('"status":2','"status":"已认领"',$data);
	$data = str_replace('"status":3','"status":"已处理"',$data);
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->tableData = $data;
	return $view->fetch();
    }

    public function add()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
/*
        switch ($this->_method){
        	case 'get': // get请求处理代码
			if (empty(Input::get('code')))
				return "参数错误";
			else
				$track_id = Input::get('code');
           		break;
        	case 'post': // post请求处理代码
			if (empty(Input::post('code')))
				return "Missing params!";
			else
				$track_id = Input::post('code');
        		break;
        	default:
        		return "Unkonw Method!!";
        		break;
        }
*/
	if (empty(Input::param('code')))
		return "Missing params";
	else 
		$track_id = Input::param('code');
	$warehouse = new \app\index\model\ShipmentWarehouse;
	$data = $warehouse::where('track_id',$track_id)->find();
	if (empty($data))
	{
		$data = $warehouse::create(['track_id'=>$track_id,'uid'=>$uid,'create_time'=>time()]);		
	}
	$wid = $data->id;
	$upload = controller('Warehouse','event');
	$photos = urldecode(json_encode($upload->getUpload($wid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->customer_id = $data->customer_id;
	$view->weight_g = $data->weight_g;
	$view->photos = $photos;
	return $view->fetch();
    }

    public function save()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$warehouse = new \app\index\model\ShipmentWarehouse;
	switch(Input::post('oper'))
	{
		case "edit":
			$save = $warehouse::get(function($query) {
				$query->where('id',Input::post('id'));
			});
			if (Input::post('customer_id'))
				$save->customer_id = Input::post('customer_id');
			if (Input::post('weight_g'))
				$save->weight_g = Input::post('weight_g');
			if (Input::post('status'))
				$save->status = Input::post('status');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
			break;
	}
	return $message;	

    }



}
