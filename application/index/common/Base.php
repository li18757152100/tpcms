<?php
namespace app\index\common;
use think\Controller;
use think\Request;
class Base extends Controller
{
   public function _initialize(){
	   //当前位置
        if(input('cateid')){
            $this->getPos(input('cateid'));
        }
        if(input('artid')){
            $articles=db('article')->field('cateid')->find(input('artid'));
            $cateid=$articles['cateid'];
			
            $this->getPos($cateid);
        }
	    //网站配置项
    	$this->getConf();
       //网站栏目导航
        $this->getNavCates();
		$this->curr();
    }
	//网站导航
	public function getNavCates(){
        $cate_list=db('cate')->where(array('pid'=>0,'is_show'=>1))->order('sort asc')->select();
		//dump($cate_list);die;
        foreach ($cate_list as $k => $v) {
            $children=db('cate')->where(array('pid'=>$v['id']))->select();
            if($children){
                $cate_list[$k]['children']=$children;
            }else{
                $cate_list[$k]['children']=0;
            }
        }
        $this->assign('cate_list',$cate_list);
    }
	public function curr(){
        $current=(input('cateid'));  //当前栏目的id
		$sonRes=db('cate')->where('id',$current)->find();
		$sonid = $sonRes['pid'];
		
		//dump($sonid);die;
        $this->assign('current',$current);
		$this->assign('sonid',$sonid);
    }
   //网站配置
	public function getConf(){
		$conf=new \app\index\model\Conf();
        $_confres=$conf->getAllConf();
        $confres=array();
        foreach ($_confres as $k => $v) {
            $confres[$v['enname']]=$v['value'];
        }
		//dump($confres);die;
        $this->assign('confres',$confres);
	}
	//当前位置
	public function getPos($cateid){
        $cate= new \app\index\model\Cate();
        $posArr=$cate->getparents($cateid);
        //dump($posArr); die;
        $this->assign('posArr',$posArr);
    }
	
}
