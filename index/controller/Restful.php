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
					"country"=>Input::post('toCountry'),
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
					'hscode'=>Input::post('hs_code'),
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
    public function status()
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
	
			//var_dump($_POST);

			$shipment = new \app\index\model\Shipment;
			$orderId = $shipment::where('order_id',Input::post('OrderId'))->where('customer_id',Input::post('CusCode'))->where('status',2)->find();
			if (empty($orderId) or empty($orderId->track_id))
			{
				return $this->response(Input::post('OrderId'),'json',201);		
			} else {
				$data = ['track_id'=>$orderId->track_id,'order_id'=>$orderId->order_id,'weight_g'=>$orderId->weight_g,'rate'=>$orderId->list_rate,'fees'=>$orderId->fee];
				return $this->response($data,'json',200);		
			}
            		break;
		default:
			return $this->response("Bad Request!",$type='json',$code=203);
			break;
	}

//	print_r ($result);
        exit();
    }

    public function change()
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
	
			//var_dump($_POST);

			$shipment = new \app\index\model\Shipment;
			$orderId = $shipment::where('order_id',Input::post('OrderId'))->where('customer_id',Input::post('CusCode'))->where('status',2)->find();
			if (empty($orderId))
			{
				return $this->response(Input::post('OrderId'),'json',201);		
			} else {
				$orderId->status = 3;
				$orderId->save();
				return $this->response($orderId,'json',200);		
			}
            		break;
		default:
			return $this->response("Bad Request!",$type='json',$code=203);
			break;
	}

//	print_r ($result);
        exit();
    }

    public function warehouse()
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
			$trackId = Input::get('trackId');
			$return = new \app\index\model\ShipmentWarehouse;
			$result = $return::where('track_id',$trackId)->select();
			return $this->response($result,'json',200);		
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
	
			//var_dump($_POST);

			$return = new \app\index\model\ShipmentWarehouse;
			$returnId = $return::where('track_id','like','%'.Input::post('trackId'))->find();
			if (empty($returnId))
			{
				return $this->response(Input::post('trackId'),'json',404);		
			} else {
				if (!empty($returnId->customer_id) and ($returnId->customer_id!=1))
					return $this->response($returnId,'json',201);		
				else
				{
					$returnId->customer_id = Input::post('CusCode');
					$returnId->asn_id = Input::post('AsnId');
					$returnId->save();
					return $this->response($returnId,'json',200);
				}		
			}
            		break;
		default:
			return $this->response("Bad Request!",$type='json',$code=203);
			break;
	}

//	print_r ($result);
        exit();
    }
    public function items()
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
			$wid = Input::get('wid');
			$return = new \app\index\model\ShipmentItems;
			$result = $return::where('wid',$wid)->select();
			return $this->response($result,'json',200);		
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
	
			//var_dump($_POST);

			$items = new \app\index\model\ShipmentItems;
			$itemId = $items::where('sku',Input::post('sku'))->where('asn',Input::post('asn'))->where('wid',Input::post('wid'))->find();
			if (!empty($itemId))
			{
				return $this->response($itemId,'json',201);		
			} else {
				$item = array (
					'wid'=>Input::post('wid'),
					'sku'=>Input::post('sku'),
					'name'=>Input::post('CnName'),
					'asn'=>Input::post('asn'),
					'amount'=>Input::post('amount'),
					'qty'=>Input::post('amount'),
					);
				$items->data($item);
				$items->save();
				return $this->response($items,'json',200);
			}
            		break;
		default:
			return $this->response("Bad Request!",$type='json',$code=203);
			break;
	}

//	print_r ($result);
        exit();
    }

    public function upItems()
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
			
			$return = new \app\index\model\ShipmentWarehouse;
			$returns = $return::where('status','2')->column('id');
			$items = new \app\index\model\ShipmentItems;
			$temp = array();
			if(!empty($_GET['oper']))
			{
				$temp = $items::where('wid','in',$returns)->field('wid,asn')->group('asn')->select();
			} else {
				$result = $items::where('wid','in',$returns)->field('wid,asn,sku,qty')->select();
				foreach($result as $item)
				{
					$temp[$item['wid']][] = $item; 
				}
			}
			return $this->response($temp,'json',200);		
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
			$ids = implode(',',$_POST['ids']);
			$uid = 1;
			//var_dump($ids);
			$return = new \app\index\model\ShipmentWarehouse;
			$returns = $return::where('status','2')->where('id','in',$ids)->update(['status'=>3]);
			return $this->response($returns,'json',200);
            		break;
		default:
			return $this->response("Bad Request!",$type='json',$code=203);
			break;
	}

//	print_r ($result);
        exit();
    }


}
