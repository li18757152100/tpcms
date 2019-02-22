<?php
namespace app\index\controller;
use app\index\common\Base;
use think\Request; 
use app\index\model\Article;
use think\Controller;
class Cate extends Base
{
    public function index($cateid)
    {	//页面内容信息	
	    $article=new Article();    
        $cate_info = db("cate")->where('id='.$cateid)->find();
		$cates = db('cate')->where('pid=3')->select();
		$cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo($cateid);
		$list=$article->getAllArticles($cateid);
		//dump($list);die;	
        
		//图片展示
		//$list = db('article')->where('cateid='.$cateid)->paginate(2,false,array('query'=>array('cateid'=>$cateid)));
		$listpic = db('article')->where('cateid='.$cateid)->paginate(4,false,array('query'=>array('cateid'=>$cateid)));	
		//翻页
        $page = $list->render();
		//模板赋值
		$this -> view->assign('listpic',$listpic);
		$this -> view->assign('list',$list);
		$this -> view->assign('page',$page);
		$this -> view->assign('cate_info',$cate_info);	
		$this -> view->assign('cates',$cates);	
	
		
		//dump($list);die;
		//渲染模板
        return $this->view->fetch('index');
    }
	 public function show($cateid,$aid){
		 //获取栏目信息
		 $cate_info = db("cate")->where('id='.$cateid)->find();
		 //获取子栏目
		 $cate = db('cate')->where('id='.$cateid)->find();
		 $cates = db('cate')->where('pid',2)->select();
		 //获取图集
		 $photos1=db('photos')->where(array('article_id'=>$aid))->limit(1)->select();
		 foreach ($photos1 as $key => $value) {
            $photos1[$key]['fimage'] = str_replace('\\', '/', $value['fimage']);
        }   
		 $photos=db('photos')->where(array('article_id'=>$aid))->select();
		 foreach ($photos as $key => $value) {
            $photos[$key]['fimage'] = str_replace('\\', '/', $value['fimage']);
        }   
		 //dump($photos);die;
		 
		 //查看文章详情是加1   点击数
         db('article')->where('id='.$aid)->setInc('click');
		 //显示文章信息
		 $info = db('article')->where('id',$aid)->find();
		 
         //上一篇下一篇
         $prev=db('article')->where('cateid='.$cateid.' and id<'.$aid)->where('id','<',$aid)->limit(1)->find();//上一篇文章
    	 $next=db('article')->where('cateid='.$cateid.' and id>'.$aid)->where('id','>',$aid)->limit(1)->find();//下一篇文章
		
		//模板赋值
		 $this -> view->assign('cate_info',$cate_info);	
		 $this -> view->assign('info',$info);
		 $this -> view->assign('_cate',$cate);
		 $this -> view->assign('cates',$cates);	
		 $this -> view->assign('prev',$prev);
         $this -> view->assign('next',$next);
		 $this -> view -> assign('photos', $photos);
		 $this -> view -> assign('photos1', $photos1);

		   //渲染模板
        return $this->view->fetch('show');
	 }
	
	
}