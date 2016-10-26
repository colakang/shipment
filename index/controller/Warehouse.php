<?php
namespace app\index\controller;

use \think\Session;
use \think\View;
use \think\Input;
use \think\Controller;
use \think\Config;
use \think\Db;

class Warehouse extends controller
{
    public function index()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$warehouse = controller('Warehouse','event');
	$data = urldecode(json_encode($warehouse->getRow(1,1)));
	$data = str_replace('"status":1','"status":"待处理"',$data);
	$data = str_replace('"status":2','"status":"已认领"',$data);
	$data = str_replace('"status":3','"status":"已上架"',$data);
	$data = str_replace('"status":4','"status":"已质检"',$data);
	$data = str_replace('"status":5','"status":"已处理"',$data);
	$data = str_replace('"status":9','"status":"已丢弃"',$data);
	$dataC = urldecode(json_encode($warehouse->getRow(1,2,">=")));
	$dataC = str_replace('"status":1','"status":"待处理"',$dataC);
	$dataC = str_replace('"status":2','"status":"已认领"',$dataC);
	$dataC = str_replace('"status":3','"status":"已上架"',$dataC);
	$dataC = str_replace('"status":4','"status":"已质检"',$dataC);
	$dataC = str_replace('"status":5','"status":"已处理"',$dataC);
	$dataC = str_replace('"status":9','"status":"已丢弃"',$dataC);
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->tableData = $data;
	$view->tableDataC = $dataC;
	return $view->fetch();
    }

    public function items()
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
	if (empty(Input::param('wid')))
		return "Missing params";
	else 
		$wid = Input::param('wid');
	$items = new \app\index\model\ShipmentItems;
	$data = $items::where('wid',$wid)->select();
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->assign('items',urldecode(json_encode($data)));
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
	//$photos = urldecode(json_encode($upload->getUpload($wid)));
	$photos = $upload->getUpload($wid);
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->customer_id = $data->customer_id;
	switch($data->status)
	{
		case 1:
			$view->status = "待处理";
			break;
		case 2:
			$view->status = "已认领";
			break;
		case 3:
			$view->status = "已上架";
			break;
		case 4:
			$view->status = "已质检";
			break;
		case 5:
			$view->status = "已处理";
			break;
		case 9:
			$view->status = "已丢弃";
			break;

		default:
			$view->status = "Unkonw";
			break;

	}
	$view->wid = $wid;
	$view->code = $data->track_id;
	$view->assign('photos',$photos);
	return $view->fetch();
    }

    public function save()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$warehouse = new \app\index\model\ShipmentWarehouse;
	if (!empty($_GET['oper']))
		$_POST['oper'] = $_GET['oper'];
	switch(Input::param('oper'))
	{
		case "edit":
			$save = $warehouse::get(function($query) {
				$query->where('id',Input::post('id'));
			});
			if (Input::post('customer_id'))
				$save->customer_id = Input::post('customer_id');
			if (Input::post('amount'))
				$save->amount = Input::post('amount');
			if (Input::post('weight_g'))
				$save->weight_g = Input::post('weight_g');
			if (Input::post('packing'))
				$save->packing = Input::post('packing');
			if (Input::post('status'))
				$save->status = Input::post('status');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
			break;
		case "del":
			$save = $warehouse::get(function($query) {
				$query->where('id',Input::post('id'))->where('uid',Session::get('uid'));
			});
			var_dump($save);
			if ($save->id == Input::post('id'))
			{
				$save->status = 9;
				$save->save();
				$message = '删除成功';
			} else {
				$message = '删除失败';
			}
			break;
		case "delpic":
			$wid = Input::get('wid');
			$upload = new \app\index\model\ShipmentUpload;
			$save = $upload::destroy(function($query) {
				$query->where('id',Input::get('id'))->where('uid',Session::get('uid')->where('wid',$wid));
			});
			$message = '删除成功';
			break;
		case "pics":
   			$file = $_FILES['file']['tmp_name'];
			$wid = Input::get('wid');
			$track_id = Input::get('code');
			$check = $warehouse::where('track_id',$track_id)->where('uid',$uid)->where('id',$wid)->find();
			if (empty($check))
				return "Data Not Found!!";			
			$uploads = controller('Upload','event');
			$dstpath = ROOT_PATH.'public/uploads/';
			$resizeimage=$uploads->start($file,1920,1080,0,$dstpath);
			if ($resizeimage)
			{
				$upload = new \app\index\model\ShipmentUpload;
				$url = str_replace(ROOT_PATH.'public/','/',$resizeimage);
				$data = $upload::create(['upload_url'=>$url,'uid'=>$uid,'create_time'=>time(),'wid'=>$wid]);
				$message = "Image Save!!";
			} else {
				$message = "Resize Error!!";
			}
			break;
		case "takeing":
			$ids = $_POST['ids'];
			$result = Db::table('think_shipment_warehouse')
    					->where('id','in', $ids)
    					->where('uid',$uid)
    					->update(['status' => 3]);
			if ($result==0)
				$message = 'Update Status Error!! Pls Check Again';
			else 
				$message = 'Update Status Success';
			break;

		default:
			$message = "oper not match";
			break;
	}
	return $message;	

    }

    public function updateItem()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$items = new \app\index\model\ShipmentItems;
	if (!empty($_GET['oper']))
		$_POST['oper'] = $_GET['oper'];
	switch(Input::param('oper'))
	{
		case "edit":
			$save = $items::get(function($query) {
				$query->where('id',Input::post('id'));
			});
			if (Input::post('weight_o'))
				$save->weight_o = Input::post('weight_o');
			if (Input::post('weight_g'))
				$save->weight_g = Input::post('weight_g');
			if (Input::post('qty'))
				$save->qty = Input::post('qty');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
			break;
		default:
			$message = "oper not match";
			break;
	}
	return $message;	

    }


}
