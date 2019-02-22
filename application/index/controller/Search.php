<?php
namespace app\index\controller;
use app\index\common\Base;
use think\Request; 
use think\Controller;
class Search extends Base
{
    public function search_news()
    {
        $keywords=input('keywords');
		$res = db('article')->where("title like '%$keywords%'")->count();
		//dump($res);die;
		$data = array();
		$data['code'] = 1;
		$data['status'] = 0;
		if($res > 0){
			$data['status'] = 1;
		}
		
		return json($data); 
    }
	
	public function search_list(){
		$keywords=input('keywords');
		$res = db('message')->where("name like '%$keywords%'")->select();
		$this->assign(array(
            'res'=>$res,
            'keywords'=>$keywords,
            
            ));
        return view('search');
		
	}
	
	public function search_index()
    {
        $keywords=input('name');
		$res = db('message')->where("name like '%$keywords%'")->count();
		//dump($res);die;
		$data = array();
		$data['code'] = 1;
		$data['status'] = 0;
		if($res > 0){
			$data['status'] = 1;
		}
		
		return json($data); 
    }
	
	public function search_lists(){
		$keywords=input('name');
		$res = db('message')->where("name like '%$keywords%'")->select();
		$this->assign(array(
            'res'=>$res,
            'keywords'=>$keywords,
            
            ));
        return view('search');
		
	}
	
	
}
