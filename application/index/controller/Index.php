<?php
namespace app\index\controller;
use app\index\common\Base;
use think\Request; 
use think\Controller;
class Index extends Base
{
    public function index()
    {
		//echo request()->controller(); die;
	    $keywords=input('keywords');
		$res = db('article')->where("title like '%$keywords%'")->select();
		$this->assign(array(
            'res'=>$res,
            'keywords'=>$keywords,
            
            ));
		//查询留言
		$MessageLst = db('message')->where(array('is_show'=>1))->order('id desc')-> paginate(2);
		$page = $MessageLst->render();
		
		//首页轮播图
        $ads = db('banner')->where(array('postionid'=>1,'is_show'=>1))->limit(10)->order('sort asc')->select();
        foreach ($ads as $key => $value) {
            $ads[$key]['image'] = str_replace('\\', '/', $value['image']);
        }  
		//dump($ads);die; 
		
		//查询新闻
        $NewCate = db('article')->where('cateid in (4,5)')->select();
		$flag=array();
		foreach ($NewCate as $key => $value) {
            $flag[$key] = $value['attr'];
        }
        //$flag = implode(',', $flag);
		//dump($flag);die;
		//in_array(attr, $flag);
		$NewList = db('article')->where('cateid in (4,5)')->order('click desc')->select();
		//$NewList = db('article')->where('cateid=5 or cateid=6')->select();
		//$NewList = db('article')->where('cateid=2')->select();
		//dump($flag);die;
		
	    
		//模板赋值
		$this->assign('NewList', $NewList);
		$this->assign('ads', $ads);
        $this->assign('MessageLst',$MessageLst);
		$this->assign('page',$page);
		//渲染模板
        return $this->view->fetch('index');
    }
	//首页留言
	public function sendMessage(){
		$data = $_POST;
        $data['add_time'] = time();
		//dump($data);die;
        $res = db('message')->insertGetId($data);
        
        if($res > 0){
            return $this->success('发布留言成功');
        	// $this->ajaxReturn(['data'=>'提交成功!','code'=>0]);
        }else{
            return $this->error('提交失败');
        	// $this->ajaxReturn(['data'=>'提交失败!','code'=>1]);
        }
	}
	
	
	
}