<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\User;
use app\index\controller\Authority;
class Index extends Controller{
	public function index(){
		return $this->fetch('index');
	}

	public function test(){
		var_dump(url('index/index'));
		return $this->fetch();
	}
}