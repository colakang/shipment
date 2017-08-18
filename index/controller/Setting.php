<?php
namespace app\index\controller;

use \think\Session;
use \think\View;
use \think\Input;
use \think\Controller;
use \think\Config;
use \think\Db;


class Setting extends controller
{

    public function report()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');
	$condition = false;		
	if ($uid!=1)
		$condition = " and uid=$uid";
	switch(Input::Param('type'))
	{
		case ('detail'):
		{
			$startId = Input::param('id');
			$report = Db::query("select uid,type,amount,customer_id,track_id,from_unixtime(create_time,'%Y-%m-%d') as creteAt,(rate+0.03) as list_rate from think_shipment where rate!=0 and  id>".$startId);
			echo "uid,type,amount,customer_id,track_id,creteAt,list_rate</br>";
			foreach($report as $row=>$cell)
			{
				foreach($cell as $key=>$value)
				{
					if ($key == "list_rate")
						echo $value;
					else
						echo $value.",";
		
				}
				echo "</br>";
			}
			return false;
		}
		default:
		{
			$endTime = strtotime(date('Y-m'));
			$startTime = strtotime(date('Y-m',strtotime('-1 month')));
			$report = Db::query("select uid,track_service,type,count(id) as total,customer_id,format(sum(rate+0.03),2) as list_rate from think_shipment where rate!=0 ".$condition." and create_time>$startTime and create_time<$endTime GROUP BY customer_id,track_service,type");
			break;
		}

	}
	$other = controller('Other','event');
	$tableData = json_encode($report);
	$tableData = $other->replace($tableData);

	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->tableData = $tableData;
	return $view->fetch();
    }



    public function discount()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	if ($uid!=1)
		return $this->error('请使用管理员登录','/index/login');

	$discount = new \app\index\model\ShipmentDiscount;
	//$warehouse = controller('Warehouse','event');
	$data = urldecode(json_encode($discount::all()));
	$data = str_replace('"status":1','"status":"Raise"',$data);
	$data = str_replace('"status":2','"status":"Discount"',$data);
	$data = str_replace('"status":3','"status":"Final"',$data);
	$data = str_replace('"status":4','"status":"Free"',$data);
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
			$view->status = "已处理";
			break;
		case 4:
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
	if ($uid!=1)
		return $this->error('请使用管理员登录','/index/login');
	$discount = new \app\index\model\ShipmentDiscount;
	if (!empty($_GET['oper']))
		$_POST['oper'] = $_GET['oper'];
	switch(Input::param('oper'))
	{
		case "edit":
			$save = $discount::get(function($query) {
				$query->where('id',Input::post('id'));
			});
			if (Input::post('customer_id'))
				$save->customer_id = Input::post('customer_id');
			if (Input::post('track_service'))
				$save->track_service = Input::post('track_service');
			if (Input::post('discount'))
				$save->discount = Input::post('discount');
			if (Input::post('status'))
				$save->status = Input::post('status');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
			break;
		case "add":
			if (Input::post('customer_id'))
				$discount->customer_id = Input::post('customer_id');
			if (Input::post('track_service'))
				$discount->track_service = Input::post('track_service');
			if (Input::post('discount'))
				$discount->discount = Input::post('discount');
			if (Input::post('status'))
				$discount->status = Input::post('status');
			if (!$discount->save())
				$message = 'Add Error!! Pls Check Again';
			else 
				$message = 'Add Success';
			break;
		case "del":
			$save = $discount::get(function($query) {
				$query->where('id',Input::post('id'));
			});
			if ($save->id == Input::post('id'))
			{
				$save->delete();
				$message = '删除成功';
			} else {
				$message = '删除失败';
			}
			break;

		default:
			$message = "oper not match";
			break;
	}
	return $message;	

    }



}
