<?php
#namespace app\index\event;

#use \think\Db;

class Muprocess extends \Thread 
{
	public $ob;  
	public $fname;  
      	public $data;  
 /* 
      	public function __construct($ob,$fname,$data)  
      	{  
          	$this->ob = $ob;  
          	$this->fname = $fname;  
          	$this->data = $data;  
      	}  
  */
      	public function run()  
      	{  
          	if(($data = $this->data))  
          	{  
              		$this->result = $this->ob->{$fname}($this->data);  
          	}  
      	}
	
	public function getResults() { return $this->rows; }
	public function setOB($ob) { $this->ob = $ob; }
	public function setFname($fname) { $this->fname = $fname; }
	public function setData($data) { $this->data = $data; }


}
