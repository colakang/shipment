<?php
namespace app\index\event;

use \think\Db;

class Warehouse
{


   public function getRow($uid = 1,$status = 3)
    {
	return Db::query('select a.id,a.customer_id,a.track_id,a.weight_g,a.status,(select count(*) from think_shipment_upload where wid=a.id) as pics from think_shipment_warehouse as a where status<'.$status);
    }

   public function getUpload($wid = false, $uid = 1)
    {
	return Db::query('select id,wid,upload_url from think_shipment_upload where wid='.$wid.';');
    }
 
   public function getNumbers($uid = 1)
    {
	return Db::query('select status,type,count(*) as number from think_shipment  where uid='.$uid.' group by status,type');
    }

    public function getBuying($uid = 1)
    {
	$buy = Db::query('select count(*) as buying from think_shipment  where uid='.$uid.' and status = 1 and buy=1 and weight>0 and weight_g>0');
	return $buy[0]['buying'];
    }
 
}
