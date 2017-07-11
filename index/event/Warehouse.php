<?php
namespace app\index\event;

use \think\Db;

class Warehouse
{


   public function getRow($uid = 1,$status = 3,$c = "=")
    {
	return Db::query('select a.id,a.customer_id,a.track_id,a.packing,a.amount,a.status,a.handling,from_unixtime(a.create_time,"%Y-%m-%d") as create_time,adddate(from_unixtime(create_time,"%Y-%m-%d"),56) as expiry,(select count(*) from think_shipment_upload where wid=a.id) as pics,(select sum(amount) from think_shipment_items where wid=a.id) as qty,(select title from think_auth_group where id=a.uid) as location from think_shipment_warehouse as a where uid in ('.$uid.') and status'.$c.$status);
    }

   public function getUpload($wid = false, $uid = 1)
    {
	return Db::query('select id,wid,upload_url from think_shipment_upload where wid='.$wid.';');
    }
 
   public function setWeight($cid = '80026')
    {
	return Db::query('update think_shipment dest,(select customer_id,product_info,amount,weight,weight_g from think_shipment where customer_id='.$cid.' and status = 3 and amount=1 group by product_info) src set dest.weight=src.weight,dest.weight_g=src.weight_g where dest.product_info=src.product_info and dest.status=1 and dest.customer_id='.$cid);
    }


}
