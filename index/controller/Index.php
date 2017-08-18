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
			if (Input::post('residential'))
				$save->residential = Input::post('residential');

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
				$format = 'pdf';
				if ($uid==1)
					$format = 'zpl';
				$addBatch = $Event->createBatch($shipmentList,$format);
				try 
				{
					$batch->easypost_batch_id = $addBatch->id;
					$batch->uid = $uid;	
					$batch->label_amount = count($ids);
					$batch->label_list = implode(',',$ids);
					$batch->create_time = time();
					$batch->save();
					$message = "Success! BatchId:".$batch->id." Qty:".count($shipmentList);
				} catch (Exception $e) {
					$message = 'Faile';
				}
			} else {
				$message = "No Shipment Add To Print";
			}

			break;
		case "scanForm":
			$Event = controller('Shipment','event');
			#$batch = new \app\index\model\ShipmentBatch;
			$ids = $_POST['ids'];
			if (count($ids)>1)
			{
				$shipmentList = Db::table('think_shipment')
		                                        ->where('id','in', $ids)
        		                                ->where('uid',$uid)
        		                                ->where('easypost_shipment_id','not null')
                        		                ->field('easypost_shipment_id as id')
							->select();
				$addScanForm = $Event->createScanForm($shipmentList);
				if (!empty($addScanForm))
					$message = "Success! ScanFormId:".$addScanForm->id." Qty:".count($shipmentList)." Url:".$addScanForm->form_url;
				else
					$message = false;
			} else {
				$message = "No Shipment Add To SacnForm";
			}

			break;
		case "getTrackId":
			$message = [];
			$Event = controller('Shipment','event');
			$ids = $_POST['ids'];
			$tArray = array();
			$result = false;
			$dbQuery = false;
			switch (true)
			{
				case (count($ids)>1):
				{
					$shipmentList = Db::table('think_shipment')
			                                        ->where('id','in', $ids)
	        		                                ->where('uid',$uid)
	        		                                ->where('easypost_shipment_id','not null')
	        		                                ->where('track_id','')
	                        		                ->field('easypost_shipment_id as id')
								->select();
					$type =1;
					foreach ($shipmentList as $key=>$sh_id)
					{
						
						$data = compact('sh_id','type');
						$t = new Muprocess($Event,'getTrackId',$data);
						if ($t->start()) {
						    /* synchronize in order to call wait */
						    $t->synchronized(function($me){
					                //$me->notify();
						        $me->wait();
						    }, $t);
						}
						$tArray[] = $t;
					}
					foreach ($tArray as $tKey=>$tValue)
					{
						while($tArray[$tKey]->isRunning())
						{
							usleep(10);
						}
						if($tArray[$tKey]->join())
						{
							$result = $tArray[$tKey]->getResults();
							$getTrackID = $result["track_id"];
							$dbQuery = $result["query"];
							if (!empty($getTrackID))
							{
								$dbResult = Db::query($dbQuery);
								$message[] = array('id'=> $tKey,'message'=>$getTrackID,'query'=>$dbResult);
							}
							else
								$message[] = array('id'=> $tKey,'message'=>"false");
						}
					}
					break;
				}
				case (count($ids)==1):
				{
					$shipmentList = Db::table('think_shipment')
			                                        ->where('id', $ids[0])
	        		                                ->where('uid',$uid)
	        		                                ->where('easypost_shipment_id','not null')
	        		                                ->where('track_id','')
	                        		                ->field('easypost_shipment_id as id')
								->select();
					$type =1;
					if(!$shipmentList)
					{
						$shipmentList = Db::table('think_shipment')
			                                        ->where('id', $ids[0])
	        		                                ->where('uid',$uid)
	        		                                ->where('easypost_shipment_id','null')
	        		                                ->where('track_id','')
	                        		                ->field('order_id as id')
								->select();
						$type =2;
					}
					foreach ($shipmentList as $key=>$sh_id)
					{
						$data = compact('sh_id','type');
						$result = $Event->getTrackId($data);
						$getTrackID = $result["track_id"];
						$dbQuery = $result["query"];
						if (!empty($getTrackID))
						{
							$dbResult = Db::query($dbQuery);
							if($type = 2)
								$calculate = $Event->setFees($ids[0]);
							$message[] = array('id'=> $ids[$key],'message'=>$getTrackID,'query'=>$dbResult,'calculate'=>$calculate);
						}
						else
							$message[] = array('id'=> $ids[$key],'message'=>"false");
					}
					break;
				}

				default:
					$message[] = "No Shipments need To update";
					break;
			}

			$message = json_encode($message);
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
		case "freelabel":
			$message = [];
			$Event = controller('Shipment','event');
			$lists = $shipment::all(function($query) {
				$query->where('id','in',$_POST['ids'])
					->where('uid',Session::get('uid'))
					->where('status',1)
					->where('weight_g','>',0)
					->where('track_id','<>','')
					->field('id,weight_g,amount,type,track_id');
			});
			foreach($lists as $send)
			{		
				$fees = $Event->getFee($send->weight_g,$send->amount,$send->type); 
				$result = Db::table('think_shipment')
    					->where('id', $send->id)
    					->where('uid',$uid)
    					->update(['list_rate' => '0','rate' => '0','fee' => $fees,'status' => 2]);
				if ($result==0)
					$message[] = array('id'=> $send->id,'message'=>"Faile");
				else 
					$message[] = array('id'=> $send->id,'message'=>"Success");
			}
			$message = json_encode($message);
			break;
		case "fbalabel":
			$message = [];
			$Event = controller('Shipment','event');
			$lists = $shipment::all(function($query) {
				$query->where('id','in',$_POST['ids'])
					->where('uid',Session::get('uid'))
					->where('status',1)
					->where('weight_g','>',0)
					->where('track_id','<>','')
					->field('id,weight_g,amount,type,track_id');
			});
			foreach($lists as $send)
			{		
				$result = Db::table('think_shipment')
    					->where('id', $send->id)
    					->where('uid',$uid)
    					->update(['list_rate' => '0','rate' => '0','fee' => '0','status' => 2]);
				if ($result==0)
					$message[] = array('id'=> $send->id,'message'=>"Faile");
				else 
					$message[] = array('id'=> $send->id,'message'=>"Success");
			}
			$message = json_encode($message);
			break;

		default:
			$message = "Opertion Not Found!";
		
	}
	return $message;	
    }

    public function muBuyLabel()
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
	$tArray = array();
	$pickupFees = 3;		//设置上门提货费用;
	$residentialFees = 3.85;		//设置住宅派送费;

	if (empty(Input::post('id')))
	{
		$sends = $shipment::all(function($query) 
		{
			//$query->where('uid',Session::get('uid'))->where('buy',1)->where('status',1)->where('weight','>',0);
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
		        "phone"   => $sendTo->phone,
			"residential" => 0
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
		$residential = $send->residential;
		$orderId = $send->order_id;		//订单号`;
/*		设置住宅派送费
		if ($residential==1)
		{
			$to['residential'] = 1;
		}
*/
		#$data = $Event->buyLabel($from,$to,$weight,$service,$packing,$productInfo,$amount,$hscode);
		$buyData = compact('from','to','weight','service','packing','productInfo','amount','hscode','orderId');
		$t = new Muprocess($Event,'buyLabel',$buyData);
		//$t->start();
		if ($t->start()) {
		    /* synchronize in order to call wait */
		    $t->synchronized(function($me){
	                //$me->notify();
		        $me->wait();
		    }, $t);
		}
		$tArray[] = $t;
	}
	foreach ($tArray as $tKey=>$tValue)
	{
		while($tArray[$tKey]->isRunning())
		{
			usleep(10);
		}
		if($tArray[$tKey]->join())
		{

			$data = $tValue->getResults();

		}
///*
		if(!is_array($data))
		{
				$result[] = array('id'=> $send->id,'data'=>$data,'message'=>"Read Error");
		} else {

			$send = $sends[$tKey];
			$service = $send->track_service;
			$pickup = $send->pickup;
			$residential = $send->residential;
			//$packing = $packingList::where('id',$send->packing)->value('packing');
			try {
				$send->list_rate = $Event->discount($send->customer_id,$service,$data["selected_list_rate"],$data["selected_rate"],$packing);
				$send->rate = $data["selected_rate"]; 
				$send->track_id = $data["tracking_code"];
				$send->track_url = $data["postage_label_url"]; 
				$send->zone = $data["usps_zone"]; 
				$send->fee = $Event->getFee($send->weight_g,$send->amount,$send->type); 
				$send->easypost_shipment_id = $data["id"];
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
					if ($residential==1)
						$send->list_rate = $send->list_rate+$residentialFees;
					break;
			}
			$result[] = array('id'=> $send->id,"rate"=>$send->list_rate,'message'=>"Succes");
			$shipmentList[] = array('id'=>$data["id"]);
		}	
			$send->status = 2;
	 		$send->save();
			$shipmentLog = new \app\index\model\ShipmentLog;
			$shipmentLog->shipmentid = $send->id;
			$shipmentLog->log = serialize($data);
			$shipmentLog->save();
			$sendId[] = $send->id;
//*/
	}
	if (count($sendId)>1)
	{
		$format = 'pdf';
		if ($uid==1)
			$format = 'zpl';
		$addBatch = $Event->createBatch($shipmentList,$format);
		$batch->easypost_batch_id = $addBatch->id;
		$batch->uid = $uid;	
		$batch->label_amount = count($shipmentList);
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
   public function checkScanForm()
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
	$info = $shipment->checkScanForm($save->easypost_batch_id);
	if (empty($info))
		return "ScanForm未创建，请稍后再尝试";
	if ($info['status']=='failed')
		return $info['message'];
	return "<a href=".$info["url"].">ScanForm下载地址</a>";
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
		        "phone"   => $sendTo->phone,
			"residential" => 0
		);
		$weight = $send->weight;
		if ($weight==16)
			$weight = 15.9;		//设置first class 16oz的计费重量
		$service = $send->track_service;
		$productInfo = $send->product_info;
		$amount = $send->amount;
		$hscode = $send->hscode;
		$pickup = $send->pickup;
		$residential = $send->residential;
		$pickupFees = 3;		//设置上门提货费用;
		$residentialFees = 3.85;		//设置住宅派送费;
		$packing = $packingList::where('id',$send->packing)->value('packing');
/*		设置住宅派送费
		if ($residential==1)
		{
			$to['residential'] = 1;
		}
*/
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
				if ($residential==1)
					$send->list_rate = $send->list_rate+$residentialFees;
				break;
			case ('Ground'):
				if ($pickup==1)
					$send->list_rate = $send->list_rate+$pickupFees;
				if ($residential==1)
					$send->list_rate = $send->list_rate+$residentialFees;
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
		$uid = Session::get('uid');
		$Event = controller('Shipment','event');
		$Event->setWeight($uid);
		return "Success";
	}

    public function compareRates()
    {
	$method = strtolower($_SERVER['REQUEST_METHOD']);
        switch ($method){
        	case 'get': // get请求处理代码
			//echo "Get Requtest";
			if (empty($_GET))
			{
				$put=file_get_contents('php://input');
				$put=json_decode($put,1);
				if (is_array($put))
				{
					foreach ($put as $key => $value) {
            					$_GET[$key]=$value; 
					}
				}
			}
			$view = new View();
			$view->systemTitle = "候鸟湾自助系统";
			$view->description = "USPS 运单 LABEL 购买";
			$view->tableData = false;
			return $view->fetch();
           		break;
        	case 'put': // put请求处理代码
			echo "Put Requtest";
            		break;
        	case 'post': // put请求处理代码
			//echo "Post Requtest";
			if (empty($_POST))
			{
				$put=file_get_contents('php://input');
				$put=json_decode($put,1);
				if (is_array($put))
				{	foreach ($put as $key => $value) {
            					$_POST[$key]=$value; 
					}
				}
			}
			//$shipmentAddress = new \app\index\model\ShipmentAddress;
			$Event = controller('Shipment','event');
			$result = array();
			$sendFrom = Input::post('sendFrom');
			switch($sendFrom)
			{
				case('94536'):
					$sendFromCity = 'Fremont';
					$sendFromState = 'CA';
					break;
				case('79925'):
					$sendFromCity = "El Paso";
					$sendFromState = 'TX';
					break;
			}
			$sendTo = Input::post('toZipcode')?Input::post('toZipcode'):Input::post('sendTo');
			$sendToCity = Input::post('toCity');
			$sendToState = Input::post('toState');
			$from = array(
				"city"    => $sendFromCity,
				"state"   => $sendFromState,
				"zip"     => $sendFrom,
				"country"   => 'US',
				//"phone"   => $sendFrom->phone
			);
			$to = array(
				"city"    => $sendToCity,
				"state"   => $sendToState,
				"country"   => 'US',
				"zip"     => $sendTo,
				"residential" => 0
			);
			$weightLbs = (Input::post('weightLbs')*16);
			$weightOz = (Input::post('weightOz'));
			$weight = $weightLbs+$weightOz;
			if ($weight==16)
				$weight = 15.9;		//设置first class 16oz的计费重量
			//$hscode = Input::post('hscode');
			//$residentialFees = 3.85;		//设置住宅派送费;
			$packing = Input::post('length').'*'.Input::post('width').'*'.Input::post('height');
			$data = $Event->compareListRate($from,$to,$weight,$packing);
			$to['residential'] = 1;
			$data2 = $Event->compareListRate($from,$to,$weight,$packing);
			foreach ($data as $key=>$value)
			{
				switch(true)
				{
					case( ($key=='First') or ($key=='Ground') or ($key=='FEDEX_GROUND') ):
						$service = $key;
						break;
					default:
						$service = 'Priority';
						break;
				}
				try {
					$temp = array(
							'birdsbay' => $Event->discount(Input::post('customer_id'),$service,$value->list_rate,$value->rate,$packing),
							'services' => $service,
						);
				} catch (\Exception $e) {
					var_dump($e->getMessage());
					exit();		
				}
				$temp2 = $temp;
				switch($key)
				{
					case ('FEDEX_GROUND'):
						$temp['provider'] = 'Fedex';
						$temp['type'] = '商业';
						break;
					case ('Ground'):
						$temp['provider'] = 'UPS';
						$temp['type'] = '商业';
						break;
					default:
						$temp['provider'] = 'USPS';
						$temp['type'] = '商业/住宅';
						break;
				}
				$result[] = $temp;
			}
			foreach ($data2 as $key=>$value)
			{
				switch(true)
				{
					case( ($key=='First') or ($key=='Ground') or ($key=='FEDEX_GROUND') ):
						$service = $key;
						break;
					default:
						$service = 'Priority';
						break;
				}
				try {
					$temp = array(
							'birdsbay' => $Event->discount(Input::post('customer_id'),$service,$value->list_rate,$value->rate,$packing),
							'services' => $service,
						);
				} catch (\Exception $e) {
					var_dump($e->getMessage());
					exit();		
				}
				switch($key)
				{
					case ('FEDEX_GROUND'):
						$temp['provider'] = 'Fedex';
						$temp['type'] = '住宅';
						$result[] = $temp;
						break;
					case ('Ground'):
						$temp['provider'] = 'UPS';
						$temp['type'] = '住宅';
						$result[] = $temp;
						break;
				}
			}

			$view = new View();
			$view->systemTitle = "候鸟湾自助系统";
			$view->description = "USPS 运单 LABEL 购买";
			$view->tableData = json_encode($result);
			$view->sendToCity = $sendToCity;
			$view->sendToState = $sendToState;
			$view->sendTo = $sendTo;
			$view->weight = $weight;
			$view->length = Input::post('length');
			$view->width = Input::post('width');
			$view->height = Input::post('height');
			$view->weightLbs = Input::post('weightLbs');
			$view->weightOz = Input::post('weightOz');
			$view->customer_id = Input::post('customer_id');
			return $view->fetch();

			//return $this->response($result,'json',200);
            		break;
		default:
			return $this->response("Bad Request!",$type='json',$code=203);
			break;
		}
   }

}

class Muprocess extends \Thread 
{
	public $ob;  
	public $fname;  
      	public $data;  
	public $result;
      	public function __construct($ob,$fname,$data)  
      	{  
          	$this->ob = $ob;  
          	$this->fname = $fname;
          	$this->data = $data;  
      	}  

    	public function run()
	{
	        $this->synchronized(function($me){
	            /* there's no harm in notifying when no one is waiting */
	            /* better that you notify no one than deadlock in any case */
	            $me->notify();
		    //$me->wait();
	        }, $this);
            	$this->result = $this->ob->{$this->fname}($this->data);
	}
	public function getResults() 
	{ 
		return $this->result; 
	}
	public function getData() { return $this->data; }
	public function setOB($ob) { $this->ob = $ob; }
	public function setFname($fname) { $this->fname = $fname; }
	public function setData($data) { $this->data = $data; }


}


