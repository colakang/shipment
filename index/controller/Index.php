<?php
namespace app\index\controller;

use \think\View;
use \think\Session;
use \think\Controller;
use \think\Model;
use \think\Input;

Session_start();

class Index extends controller
{


    public function _initialize()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	//此版本未生效;
    }

    public function index()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
 	$Event = controller('Shipment','event');
	$view->check =0;
	$view->edit = 0;
	$view->rsync = 0;
	foreach ($Event->getNumbers($uid) as $number)
	{
		switch (true)
		{
			case ($number['status']==1 and $number['type']==1):
				$view->check = $number['number'];
				break;
			case ($number['status']==1 and $number['type']!=1):
				$view->edit = $view->edit+$number['number'];
				break;
			case ($number['status']==2 or $number['status']==3):
				$view->rsync = $view->rsync+$number['number'];
				break;
		}	
	}
	$view->buying = $Event->getBuying($uid);
	return $view->fetch();



    }

    public function checkShipment()
    {
	//$rules = new \app\index\model\AuthRule;

	//$Event = controller('Shipment','event');
	//$data = $Event->buyLabel($from,$to,$weight);
	//return $data;	
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$shipment = controller('Shipment','event');
	$packingList = new \app\index\model\ShipmentPacking;
	$tableData = urldecode(json_encode($shipment->getRow($uid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$tableData = str_replace('"type":1','"type":"仓库运单"',$tableData);
	$tableData = str_replace('"type":2','"type":"退货运单"',$tableData);
	$tableData = str_replace('"type":3','"type":"自助运单"',$tableData);
	$tableData = str_replace('"buy":1','"buy":"是"',$tableData);
	$tableData = str_replace('"buy":2','"buy":"否"',$tableData);
	$pack = '{name:"packing",index:"packing", width:40,editable: true,edittype:"select",editoptions:{value:"';
	foreach ($packingList::all() as $packing)
	{
		$tableData = str_replace('"packing":'.$packing->id,'"packing":"'.$packing->packing.'"',$tableData);
		$pack = $pack.$packing->id.':'.$packing->packing.";";
	}
	$pack = $pack.'"}},';
	$view->tableData = $tableData;
	$view->pack = $pack;
	return $view->fetch();
    }

    public function editLabel()
    {
	//$rules = new \app\index\model\AuthRule;

	//$Event = controller('Shipment','event');
	//$data = $Event->buyLabel($from,$to,$weight);
	//return $data;	
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$shipment = controller('Shipment','event');
	$packingList = new \app\index\model\ShipmentPacking;
	$tableData = urldecode(json_encode($shipment->getRow($uid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$tableData = str_replace('"type":1','"type":"仓库运单"',$tableData);
	$tableData = str_replace('"type":2','"type":"退货运单"',$tableData);
	$tableData = str_replace('"type":3','"type":"自助运单"',$tableData);
	$tableData = str_replace('"buy":1','"buy":"是"',$tableData);
	$tableData = str_replace('"buy":2','"buy":"否"',$tableData);
	$pack = '{name:"packing",index:"packing", width:40,editable: true,edittype:"select",editoptions:{value:"';
	foreach ($packingList::all() as $packing)
	{
		$tableData = str_replace('"packing":'.$packing->id,'"packing":"'.$packing->packing.'"',$tableData);
		$pack = $pack.$packing->id.':'.$packing->packing.";";
	}
	$pack = $pack.'"}},';
	$view->tableData = $tableData;
	$view->pack = $pack;
	return $view->fetch();
    }


  public function rsync()
    {
	//$rules = new \app\index\model\AuthRule;

	//$Event = controller('Shipment','event');
	//$data = $Event->buyLabel($from,$to,$weight);
	//return $data;	
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$shipment = controller('Shipment','event');
	$packingList = new \app\index\model\ShipmentPacking;
	$tableData = urldecode(json_encode($shipment->getBuidLabel($uid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$tableData = str_replace('"type":1','"type":"仓库运单"',$tableData);
	$tableData = str_replace('"type":2','"type":"退货运单"',$tableData);
	$tableData = str_replace('"type":3','"type":"自助运单"',$tableData);
	$tableData = str_replace('"status":2','"status":"否"',$tableData);
	$tableData = str_replace('"status":3','"status":"是"',$tableData);
	$pack = '{name:"packing",index:"packing", width:40,editable: false,edittype:"select",editoptions:{value:"';
	foreach ($packingList::all() as $packing)
	{
		$tableData = str_replace('"packing":'.$packing->id,'"packing":"'.$packing->packing.'"',$tableData);
		$pack = $pack.$packing->id.':'.$packing->packing.";";
	}
	//{name:'packing',index:'packing', width:60,editable: true,edittype:"select",editoptions:{value:"1:是;2:否;"}},
	$pack = $pack.'"}},';
	$view->tableData = $tableData;
	$view->pack = $pack;
	return $view->fetch();
    }

    public function save()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$shipment = new \app\index\model\Shipment;
	$uid = Session::get('uid');		
	switch(Input::post('oper'))
	{
		case "edit":
			$save = $shipment::get(function($query) {
				$query->where('id',Input::post('id'))->where('uid',Session::get('uid'));
			});
			if (Input::post('weight'))
				$save->weight = Input::post('weight');
			if (Input::post('weight_g'))
				$save->weight_g = Input::post('weight_g');
			if (Input::post('track_service'))
				$save->track_service = Input::post('track_service');
			if (Input::post('buy'))
				$save->buy = Input::post('buy');
			if (Input::post('status'))
				$save->status = Input::post('status');
			if (Input::post('packing'))
				$save->packing = Input::post('packing');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
			break;
		case "editAddress":
			$a_id = Input::post('id');
			$shipment_id = Input::post('shipment_id');
			$address = array(
					"name"=>Input::post('name'),
					"address1"=>Input::post('address1'),
					"address2"=>Input::post('address2'),
					"city"=>Input::post('city'),
					"state"=>Input::post('state'),
					"zipcode"=>Input::post('zipcode'),
					"phone"=>Input::post('phone'),
					"uid"=>$uid,
					);
			$addressMd5 = md5(implode("-",$address)); 

			$shipmentAddress = new \app\index\model\ShipmentAddress;
			$md5 = $shipmentAddress::where('md5',$addressMd5)->value('id');
			if (empty($md5))
			{
				$address['md5'] = $addressMd5;
				$shipmentAddress = new \app\index\model\ShipmentAddress;
				$save = $shipmentAddress::get(function($query) {
					$query->where('id',Input::post('id'))->where('uid',Session::get('uid'));
					});
				$save->name = $address["name"];
				$save->address1 = $address["address1"];
				$save->address2 = $address["address2"];
				$save->city = $address["city"];
				$save->state = $address["state"];
				$save->zipcode = $address["zipcode"];
				$save->phone = $address["phone"];
				$save->md5 = $address["md5"];
				$save->save();
				$message = $save->id;
			} else {
				if ($md5 != $a_id)
				{
					$shipment = new \app\index\model\Shipment;
					$save = $shipment::where('id',$shipment_id)->where('uid',$uid)->update([Input::post('action')=>$md5]);	
				}	
				$message = $md5;
			}
			break;
		case "newAddress":
			$address = array(
					"name"=>Input::post('name'),
					"address1"=>Input::post('address1'),
					"address2"=>Input::post('address2'),
					"city"=>Input::post('city'),
					"state"=>Input::post('state'),
					"zipcode"=>Input::post('zipcode'),
					"phone"=>Input::post('phone'),
					"uid"=>$uid,
					);
			$addressMd5 = md5(implode("-",$address)); 

			$shipmentAddress = new \app\index\model\ShipmentAddress;
			$md5 = $shipmentAddress::where('md5',$addressMd5)->value('id');
			if (empty($md5))
			{
				$address['md5'] = $addressMd5;
				$shipmentAddress = new \app\index\model\ShipmentAddress;
				$shipmentAddress->data($address);
				$shipmentAddress->save();
				$message = $shipmentAddress->id;
			} else {
				$message = $md5;
			}
			break;
		case "newLabel":
			$shipment = new \app\index\model\Shipment;
			if (!Input::post('customer_id')) $_POST['customer_id'] = 90000+$uid;
			if (!Input::post('order_id')) $_POST['order_id'] = 'S'.$_POST['customer_id'].time();
			$order = array (
				'uid'=>$uid,
				'customer_id'=>Input::post('customer_id'),
				'product_info'=>Input::post('product_info'),
				'amount'=>Input::post('amount'),
				'order_id'=>Input::post('order_id'),
				'track_service'=>Input::post('track_service'),
				'send_from_id'=>Input::post('send_from_id'),
				'send_to_id'=>Input::post('send_to_id'),
				'type'=>Input::post('type'),
				'weight'=>Input::post('weight'),
				'weight_g'=>Input::post('weight_g'),
				'create_time'=>time(),
				);
			$shipment->data($order);
			$shipment->save();
			$message = "保存成功";
			break; 
		case "del":
			$save = $shipment::get(function($query) {
				$query->where('id',Input::post('id'))->where('uid',Session::get('uid'));
			});
			if ($save->id == Input::post('id'))
			{
				$save->status = 9;
				$save->save();
				$message = '删除成功';
			} else {
				$message = '删除失败';
			}
			break;
		default:
			$message = "Opertion Not Found!";
		
	}
	return $message;	
    }


    public function buyLabel()
    {
	//$rules = new \app\index\model\AuthRule;
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$shipment = new \app\index\model\Shipment;
	$shipmentAddress = new \app\index\model\ShipmentAddress;
	$packingList = new \app\index\model\ShipmentPacking;
	$result = array();
	if (empty(Input::post('id')))
	{
		$sends = $shipment::all(function($query) 
		{
			$query->where('uid',Session::get('uid'))->where('buy',1)->where('status',1)->where('weight','>',0);
		});
	} else {
		$sends = $shipment::all(function($query) 
		{
			$query->where('id',Input::post('id'))->where('uid',Session::get('uid'))->where('buy',1)->where('status',1)->where('weight','>',0);
		});
	
	}
	if (empty($sends))
		return "没有需要购买的LABEL！";
	foreach ($sends as $send)
	{
		$sendFrom = $shipmentAddress::get($send->send_from_id);
		$sendTo = $shipmentAddress::get($send->send_to_id);
	
		$from = array(
        		"name"    => $sendFrom->name,
		        "street1" => $sendFrom->address1,
			"street2" => $sendFrom->address2,
		        "city"    => $sendFrom->city,
		        "state"   => $sendFrom->state,
		        "zip"     => $sendFrom->zipcode,
		        "phone"   => $sendFrom->phone
		);
		$to = array(
         		"name"    => $sendTo->name,
		        "street1" => $sendTo->address1,
			"street2" => $sendTo->address2,
		        "city"    => $sendTo->city,
		        "state"   => $sendTo->state,
		        "zip"     => $sendTo->zipcode,
		        "phone"   => $sendTo->phone
		);
		$weight = $send->weight;
		$service = $send->track_service;
		$Event = controller('Shipment','event');
		$packing = $packingList::where('id',$send->packing)->value('packing');
		$productInfo = $send->product_info;
		$data = $Event->buyLabel($from,$to,$weight,$service,$packing,$productInfo);
		try {
			$send->list_rate = $data->selected_rate->list_rate; 
			$send->rate = $data->selected_rate->rate; 
			$send->track_id = $data->tracking_code;
			$send->track_url = $data->postage_label->label_pdf_url; 
			$send->zone = $data->usps_zone; 
			$send->fee = controller('Shipment','event')->getFee($send->weight_g,$send->amount,$send->type); 
			$send->status = 2;
		} catch (Exception $e) {
			var_dump($e->getMessage());
			exit();		
		}
 		$send->save();
		$shipmentLog = new \app\index\model\ShipmentLog;
		$shipmentLog->shipmentid = $send->id;
		$shipmentLog->log = serialize(array($data->rates,$data->selected_rate,$data->postage_label));
		$shipmentLog->save();
		$result[] = array('id'=> $send->id,"rate"=>$data->selected_rate->list_rate,'message'=>"Succes");
    	}
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->tableData = json_encode($result);
	return $view->fetch();
   }

   public function newLabel()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	return $view->fetch();
    }

    public function address()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$shipment = new \app\index\model\Shipment;
	$shipmentAddress = new \app\index\model\ShipmentAddress;
	$save = $shipment::get(function($query) 
	{
		$query->where('id',Input::post('shipment_id'))->where('uid',Session::get('uid'))->where('status',1);
	});
	switch (Input::post('action'))
	{
		case "send_from_id":
			$address = $shipmentAddress::get($save->send_from_id);
			break;
		case "send_to_id":
			$address = $shipmentAddress::get($save->send_to_id);
			break;
	}
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->name = $address->name;
	$view->address1 = $address->address1;
	$view->address2 = $address->address2;
	$view->city = $address->city;
	$view->state = $address->state;
	$view->zipcode = $address->zipcode;
	$view->phone = $address->phone;
	$view->shipment_id = Input::post('shipment_id');
	$view->action = Input::post('action');
	$view->id = $address->id;

	return $view->fetch();
    }
    public function getRates()
    {
	//$rules = new \app\index\model\AuthRule;
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$shipment = new \app\index\model\Shipment;
	$shipmentAddress = new \app\index\model\ShipmentAddress;
	$packingList = new \app\index\model\ShipmentPacking;
	$result = array();
	if (empty(Input::post('shipment_id')))
	{
		return "请检查参数";
	} else {
		$sends = $shipment::all(function($query) 
		{
			$query->where('id',Input::post('shipment_id'))->where('uid',Session::get('uid'))->where('weight','>',0);
		});
	
	}
	if (empty($sends))
		return "没有需要查询的LABEL！";
	foreach ($sends as $send)
	{
		$sendFrom = $shipmentAddress::get($send->send_from_id);
		$sendTo = $shipmentAddress::get($send->send_to_id);
	
		$from = array(
        		"name"    => $sendFrom->name,
		        "street1" => $sendFrom->address1,
			"street2" => $sendFrom->address2,
		        "city"    => $sendFrom->city,
		        "state"   => $sendFrom->state,
		        "zip"     => $sendFrom->zipcode,
		        "phone"   => $sendFrom->phone
		);
		$to = array(
         		"name"    => $sendTo->name,
		        "street1" => $sendTo->address1,
			"street2" => $sendTo->address2,
		        "city"    => $sendTo->city,
		        "state"   => $sendTo->state,
		        "zip"     => $sendTo->zipcode,
		        "phone"   => $sendTo->phone
		);
		$weight = $send->weight;
		$service = $send->track_service;
		$Event = controller('Shipment','event');
		$packing = $packingList::where('id',$send->packing)->value('packing');
		$data = $Event->getListRate($from,$to,$weight,$service,$packing);
		try {
			$send->list_rate = $data->list_rate; 
			$send->rate = $data->rate; 
			$send->zone = $data->usps_zone; 
			$send->fee = controller('Shipment','event')->getFee($send->weight_g,$send->amount,$send->type); 
		} catch (Exception $e) {
			var_dump($e->getMessage());
			exit();		
		}
		$result[] = array('id'=> $send->id,"service"=>$data->service,"rate"=>$data->list_rate,"rate1"=>$data->rate,"Fee"=>$send->fee,'message'=>"Succes");
    	}
	#$view = new View();
	#$view->systemTitle = "候鸟湾自助系统";
	#$view->description = "USPS 运单 LABEL 购买";
	#$view->tableData = json_encode($result);
	#return $view->fetch();
	return json_encode($result);
   }




}
