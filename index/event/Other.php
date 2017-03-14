<?php
namespace app\index\event;

use \think\Db;

class Other
{

   public function replace($tableData)
   {
	$tableData = str_replace('"type":1','"type":"仓库运单"',$tableData);
	$tableData = str_replace('"type":2','"type":"退货运单"',$tableData);
	$tableData = str_replace('"type":3','"type":"自助运单"',$tableData);
	$tableData = str_replace('"type":4','"type":"虚拟仓运单"',$tableData);
	$tableData = str_replace('"buy":1','"buy":"是"',$tableData);
	$tableData = str_replace('"buy":2','"buy":"否"',$tableData);
	$tableData = str_replace('"pickup":2','"pickup":"否"',$tableData);
	$tableData = str_replace('"pickup":1','"pickup":"是"',$tableData);
	$tableData = str_replace('"status":2','"status":"否"',$tableData);
	$tableData = str_replace('"status":3','"status":"是"',$tableData);
	return $tableData;
   }


}
