<?php
namespace app\index\controller;

use \think\View;
use \think\Session;
use \think\Controller;
use \think\Model;
use \think\Input;
use \think\Db;

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
	$other = controller('Other','event');
	$packingList = new \app\index\model\ShipmentPacking;
	$tableData = urldecode(json_encode($shipment->getRow($uid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$tableData = $other->replace($tableData);
	$pack = '{name:"packing",index:"packing", width:40,editable: true,edittype:"select",editoptions:{value:"';
	foreach ($packingList::all() as $packing)
	{
		$tableData = str_replace('"packing":'.$packing->id.",",'"packing":"'.$packing->name.'",',$tableData);
		$pack = $pack.$packing->id.':'.$packing->name.";";
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
	$other = controller('Other','event');
	$packingList = new \app\index\model\ShipmentPacking;
	$tableData = urldecode(json_encode($shipment->getRow($uid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$tableData = $other->replace($tableData);
	$pack = '{name:"packing",index:"packing", width:40,editable: true,edittype:"select",editoptions:{value:"';
	foreach ($packingList::all() as $packing)
	{
		$tableData = str_replace('"packing":'.$packing->id.",",'"packing":"'.$packing->name.'",',$tableData);
		$pack = $pack.$packing->id.':'.$packing->name.";";
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
	$other = controller('Other','event');
	$packingList = new \app\index\model\ShipmentPacking;
	$tableData = urldecode(json_encode($shipment->getBuidLabel($uid)));
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$tableData = $other->replace($tableData);
	$pack = '{name:"packing",index:"packing", width:40,editable: false,edittype:"select",editoptions:{value:"';
	foreach ($packingList::all() as $packing)
	{
		$tableData = str_replace('"packing":'.$packing->id.",",'"packing":"'.$packing->name.'",',$tableData);
		$pack = $pack.$packing->id.':'.$packing->name.";";
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
			if (Input::post('hscode'))
				$save->hscode = Input::post('hscode');
			if (Input::post('pickup'))
				$save->pickup = Input::post('pickup');
			if (Input::post('type'))
				$save->type = Input::post('type');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
			break;
		case "print":
			$Event = controller('Shipment','event');
			$batch = new \app\index\model\ShipmentBatch;
			$ids = $_POST['ids'];
			if (count($ids)>1)
			{
				$shipmentList = Db::table('think_shipment')
		                                        ->where('id','in', $ids)
        		                                ->where('uid',$uid)
        		                                ->where('easypost_shipment_id','not null')
                        		                ->field('easypost_shipment_id as id')
							->select();
				$addBatch = $Event->createBatch($shipmentList);
				$batch->easypost_batch_id = $addBatch->id;
				$batch->uid = $uid;	
				$batch->label_amount = count($ids);
				$batch->label_list = implode(',',$ids);
				$batch->create_time = time();
				$batch->save();
				$message = "Success! BatchId:".$batch->id." Qty:".count($shipmentList);
			} else {
				$message = "No Shipment Add To Print";
			}

			break;
		case "rsync":
			$ids = $_POST['ids'];
			$result = Db::table('think_shipment')
    					->where('id','in', $ids)
    					->where('uid',$uid)
    					->update(['status' => 3]);
			if ($result==0)
				$message = 'Update Status Error!! Pls Check Again';
			else 
				$message = 'Update Status Success';
			break;
		case "resend":
			$ids = $_POST['ids'];
			$result = Db::table('think_shipment')
    					->where('id','in', $ids)
    					->where('uid',$uid)
    					->update(['track_id' => '','track_url' => '','list_rate' => '','rate' => '','fee' => '','easypost_shipment_id' => '','status' => 1]);
			if ($result==0)
				$message = 'Set Shipment Resend  Error!! Pls Check Again';
			else 
				$message = 'Set Shipment Resend Success';
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
	$batch = new \app\index\model\ShipmentBatch;
	$Event = controller('Shipment','event');
	$uid = Session::get('uid');		
	$result = array();
	$shipmentList = array();
	$sendId = array();
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
		        "country"   => $sendTo->country,
		        "zip"     => $sendTo->zipcode,
		        "phone"   => $sendTo->phone
		);
		$weight = $send->weight;
		if ($weight==16)
			$weight = 15.9;		//设置first class 16oz的计费重量
		$service = $send->track_service;
		$packing = $packingList::where('id',$send->packing)->value('packing');
		$productInfo = $send->product_info;
		$amount = $send->amount;
		$hscode = $send->hscode;
		$pickup = $send->pickup;
		$pickupFees = 3;		//设置上门提货费用;
		$data = $Event->buyLabel($from,$to,$weight,$service,$packing,$productInfo,$amount,$hscode);
		try {
			//$send->list_rate = $data->selected_rate->list_rate; 
			$send->list_rate = $Event->discount($send->customer_id,$service,$data->selected_rate->list_rate,$data->selected_rate->rate,$packing);
			$send->rate = $data->selected_rate->rate; 
			$send->track_id = $data->tracking_code;
			$send->track_url = $data->postage_label->label_pdf_url; 
			$send->zone = $data->usps_zone; 
			$send->fee = $Event->getFee($send->weight_g,$send->amount,$send->type); 
			$send->status = 2;
			$send->easypost_shipment_id = $data->id;
		} catch (Exception $e) {
			//var_dump($e->getMessage());
			$result[] = array('id'=> $send->id,'message'=>"Faile");
			break;
			//exit();		
		}
		switch($service)
		{
			case ('FEDEX_GROUND'):
				if ($pickup==1)
					$send->list_rate = $send->list_rate+$pickupFees;
				break;
		}	
 		$send->save();
		$shipmentLog = new \app\index\model\ShipmentLog;
		$shipmentLog->shipmentid = $send->id;
		$shipmentLog->log = serialize(array($data->rates,$data->selected_rate,$data->postage_label));
		$shipmentLog->save();
		$result[] = array('id'=> $send->id,"rate"=>$send->list_rate,'message'=>"Succes");
		$shipmentList[] = array('id'=>$data->id);
		$sendId[] = $send->id;
    	}
	if (count($sendId)>1)
	{
		$addBatch = $Event->createBatch($shipmentList);
		$batch->easypost_batch_id = $addBatch->id;
		$batch->uid = $uid;	
		$batch->label_amount = count($sendId);
		$batch->label_list = implode(',',$sendId);
		$batch->create_time = time();
		$batch->save();
		$result[] = array('id'=> $batch->id,"batch_id"=>$batch->easypost_batch_id,'message'=>"Save");
	}
	return json_encode($result);
	//$view = new View();
	//$view->systemTitle = "候鸟湾自助系统";
	//$view->description = "USPS 运单 LABEL 购买";
	//$view->tableData = json_encode($result);
	//return $view->fetch();
   }

   public function viewBatch()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$shipment = controller('Shipment','event');
	$tableData = json_encode($shipment->getBatch($uid));
	$tableData = str_replace('"status":1','"status":"创建"',$tableData);
	$tableData = str_replace('"status":2','"status":"完成"',$tableData);
	$tableData = str_replace('"status":3','"status":"打印"',$tableData);
	$view = new View();
	$view->systemTitle = "候鸟湾自助系统";
	$view->description = "USPS 运单 LABEL 购买";
	$view->tableData = $tableData;
	return $view->fetch();
    }

   public function checkBatch()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$uid = Session::get('uid');		
	$batchId = Input::post('id');
	if (empty($batchId))
		return "Missing Paramm";
	$shipment = controller('Shipment','event');
	$batch = new \app\index\model\ShipmentBatch;
	$save = $batch::get($batchId);
	$info = $shipment->checkBatch($save->easypost_batch_id);
	if (empty($info))
		return "标签未创建，请稍后再尝试";
	$save->label_url = $info;
	$save->status = 2;
	$save->save();
	return "<a href='$info'>下载地址</a>";
    }

    public function saveBatch()
    {
	if (!Session::has('isLogin'))
		return $this->error('请登陆','/index/login');
	$batch = new \app\index\model\ShipmentBatch;
	$uid = Session::get('uid');		
	switch(Input::post('oper'))
	{
		case "edit":
			$save = $batch::get(function($query) {
				$query->where('id',Input::post('id'))->where('uid',Session::get('uid'));
			});
			if (Input::post('status'))
				$save->status = Input::post('status');
			if (!$save->save())
				$message = 'Update Error!! Pls Check Again';
			else 
				$message = 'Update Success';
				break;
		default:
			$message = "Opertion Not Found!";
		
	}
	return $message;
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
	$Event = controller('Shipment','event');
	$result = array();
	$uid = Session::get('uid');
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
		        "country"   => $sendTo->country,
		        "zip"     => $sendTo->zipcode,
		        "phone"   => $sendTo->phone
		);
		$weight = $send->weight;
		//if ($weight==16)
		//	$weight = 15.9;		//设置first class 16oz的计费重量
		$service = $send->track_service;
		$productInfo = $send->product_info;
		$amount = $send->amount;
		$hscode = $send->hscode;
		$pickup = $send->pickup;
		$pickupFees = 3;		//设置上门提货费用;
		$packing = $packingList::where('id',$send->packing)->value('packing');
		$data = $Event->getListRate($from,$to,$weight,$service,$packing,$productInfo,$amount,$hscode);
		try {
			$send->list_rate = $Event->discount($send->customer_id,$service,$data->list_rate,$data->rate,$packing); 
			$send->rate = $data->rate; 
			$send->zone = $data->usps_zone; 
			$send->easypost_shipment_id = $data->shipment_id; 
			$send->fee = $Event->getFee($send->weight_g,$send->amount,$send->type);
		} catch (\Exception $e) {
			var_dump($e->getMessage());
			exit();		
		}
		switch($service)
		{
			case ('FEDEX_GROUND'):
				if ($pickup==1)
					$send->list_rate = $send->list_rate+$pickupFees;
				break;
		}
		switch($uid)
		{
			case 1:
				$result[] = array('id'=> $send->id,"service"=>$data->service,"rate"=>$send->list_rate,"rate1"=>$data->rate,"Fee"=>$send->fee,'message'=>"Succes");
				break;
			default:
				$result[] = array('id'=> $send->id,"service"=>$data->service,"rate"=>$send->list_rate,"Fee"=>$send->fee,'message'=>"Succes");
				break;
		}
    	}
	return json_encode($result);
   }

	public function oneClickFill()
	{
		if (!Session::has('isLogin'))
			return $this->error('请登陆','/index/login');
		$Event = controller('Shipment','event');
		$Event->setWeight();
		return "Success";
	}


}
