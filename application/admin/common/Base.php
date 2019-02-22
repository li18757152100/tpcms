<?php
namespace app\admin\common;
use think\Controller;
use think\Request;
use think\Session;
use think\Cache;
class Base extends Controller
{
   public function _initialize(){
	     if(!session('user_info')){
           $this->error('请先登录系统~~',url('login/index')); 
        }
		
		$adminRes = session("user_info");
	   //dump($adminRes); die;
		//模板赋值
     	$this->view->assign('adminRes', $adminRes);
		
		//网站配置项
    	$this->getConf();
		
		$auth= new \app\admin\Controller\Auth();
		//$auth=new Auth();
        $request=Request::instance();
        $con=$request->controller();
        $action=$request->action();
        $name=$con.'/'.$action;	
		//echo $name;
		//dump(session('id'));die;
        $notCheck=array('Index/index','Admin/lst','Admin/logout','Conf/conf');
         if(session('id')!=1){
        	if(!in_array($name, $notCheck)){
        		if(!$auth->check($name,session('id'))){
		    		$this->error('您无权操作~',url('index/index')); 
		    	}
        	}
        	
        }
        
	
	
    }
	
	//网站配置
	public function getConf(){
		$conf=new \app\index\model\Conf();
        $_confres=$conf->getAllConf();
        $confress=array();
        foreach ($_confres as $k => $v) {
            $confress[$v['enname']]=$v['value'];
        }
		//dump($confress);die;
        $this->assign('confress',$confress);
	}
	
	//退出登录
	public function logout(){
		session(null);
		$status = 1;
        $msg = '注销成功,正在返回...';
		return ['status'=>$status, 'msg'=>$msg];
	}
	//清除网站缓存
	public function clearCache(){
		//Cache::clear(); 
		if(!cache(NULL)){
			$status = 0;
            $msg = '缓存清除失败~~';
			}
			$status = 1;
            $msg = '缓存清除成功~~';
			return ['status'=>$status, 'msg'=>$msg];
	}
	
}
