<?php

namespace app\index\controller;

use think\controller\Rest;
use think\Model;
use think\Input;

class RESTful extends Rest
{
    public function rest()
    {
        switch ($this->_method){
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
			$orderId = Input::get('orderId');
			$shipment = new \app\index\model\Shipment;
			$result = $shipment::where('order_id',$orderId)->select();
			$this->response($result,'json',200);		
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
			$_POST['create_time'] = time();
			$uId = 1;
			$_POST['uid'] = $uId;
			$toAddress = array(
					"name"=>Input::post('toName'),
					"address1"=>Input::post('toAddress1'),
					"address2"=>Input::post('toAddress2'),
					"city"=>Input::post('toCity'),
					"state"=>Input::post('toState'),
					"zipcode"=>Input::post('toZipcode'),
					"phone"=>Input::post('toPhone'),
					"uid"=>$uId,
					);
			$toAddressMd5 = md5(implode("-",$toAddress)); 

			$fromAddress = array(
					"name"=>Input::post('fromName'),
					"address1"=>Input::post('fromAddress1'),
					"address2"=>Input::post('fromAddress2'),
					"city"=>Input::post('fromCity'),
					"state"=>Input::post('fromState'),
					"zipcode"=>Input::post('fromZipcode'),
					"phone"=>Input::post('fromPhone'),
					"uid"=>$uId,
					);
			$fromAddressMd5 = md5(implode("-",$fromAddress)); 

			$shipmentAddress = new \app\index\model\ShipmentAddress;
			$toMd5 = $shipmentAddress::where('md5',$toAddressMd5)->value('id');
			$fromMd5 = $shipmentAddress::where('md5',$fromAddressMd5)->value('id');
			if (empty($toMd5))
			{
				$toAddress['md5'] = $toAddressMd5;
				$shipmentAddress = new \app\index\model\ShipmentAddress;
				$shipmentAddress->data($toAddress);
				$shipmentAddress->save();
				$_POST['sendToId'] = $shipmentAddress->id;
			} else {
				$_POST['sendToId'] = $toMd5;
			}

			if (empty($fromMd5))
			{
				$fromAddress['md5'] = $fromAddressMd5;
				$shipmentAddress = new \app\index\model\ShipmentAddress;
				$shipmentAddress->data($fromAddress);
				$shipmentAddress->save();
				$_POST['sendFromId'] = $shipmentAddress->id;
			} else {
				$_POST['sendFromId'] = $fromMd5;
			}
	
			//var_dump($_POST);

			$shipment = new \app\index\model\Shipment;
			$orderId = $shipment::where('order_id',Input::post('order_id'))->value('id');
			if (empty($orderId))
			{
				$order = array (
					'uid'=>$uId,
					'customer_id'=>Input::post('customer_id'),
					'product_info'=>Input::post('product_info'),
					'amount'=>Input::post('amount'),
					'order_id'=>Input::post('order_id'),
					'track_service'=>Input::post('track_service'),
					'send_from_id'=>Input::post('sendFromId'),
					'send_to_id'=>Input::post('sendToId'),
					'create_time'=>time(),
					);
				$shipment->data($order);
				$shipment->save();
				$this->response($shipment->id,'json',200);		
			} else {
				$this->response($shipment->id,'json',202);		
			}
            		break;
		default:
			response("Bad Request!",$type='json',$code=203);
			break;
	}

//	print_r ($result);
        exit();
    }
}
