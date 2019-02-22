<?php
namespace app\index\controller;
use app\index\common\Base;
use think\Request; 
use think\Controller;
class Video extends Base
{
    public function index()
    {
		$request=Request::instance();
        $con=$request->controller();
        $action=$request->action();
        $name=$con.'/'.$action;	
		//dump($con);die;
		$this->assign('action',$action);
		$this->assign('name',$name);
		$this->assign('con',$con);
		//渲染模板
        return $this->view->fetch('index');
	}
	
	
	
}