<?php
namespace app\admin\model;
use think\Model;
class AuthRule extends Model
{
	//模型层树形结构
	 public function authRuleTree(){
		 //获取所有栏目
		 $authRuleres = $this->order('sort desc')->select();
		 //对所有栏目进行一个排序;
		 return $this->sort($authRuleres);
		 }
	 public function sort($data,$pid=0){
		 //定义一个空数组
		 static $arr=array();
		 //循环一下栏目
		 foreach($data as $k => $v){
			 //判断是否为顶级
			 if($v['pid']==$pid){
				 //节省资源，把字段level追加到空数组中；
				 //$v['level']=$level;
				 //选择id
				 $v['dataid']=$this->getparentid($v['id']);
				 //存放到空数组；
				 $arr[]=$v;
				 //执行递归
				 $this->sort($data,$v['id']);
			 }
		 }
		 return $arr;
	 }
	 
	 //获取子权限id
	 public function getchilrenid($authGruleid){
		 $authRuleres=$this->select();
		 return $this->_getchilrenid($authRuleres,$authGruleid);
	 }
	 
	 public function _getchilrenid($authRuleres,$authGruleid){
		 //定义一个空数组
		 static $arr=array();
		 foreach($authRuleres as $k => $v){
			 if($v['pid']==$authGruleid){
				 $arr[]=$v['id'];
				 //再找子栏目的子栏目id
				 $this->_getchilrenid($authRuleres,$v['id']);
			 }
		 }
		 return $arr;
		 }
		 
		 
		 
		 //获取父级权限id
	 public function getparentid($authGruleid){
		 $authRuleres=$this->select();
		 return $this->_getparentid($authRuleres,$authGruleid,True);
	 }
	 
	 public function _getparentid($authRuleres,$authGruleid,$clear=False){
		 //定义一个空数组
		 static $arr=array();
		 if($clear){
			 $arr=array();
		 };
		 foreach($authRuleres as $k => $v){
			 if($v['id']==$authGruleid){
				 $arr[]=$v['id'];
				 //再找子栏目的子栏目id
				 $this->_getparentid($authRuleres,$v['pid'],False);
			 }
		 }
		 //排序数组
		 asort($arr);
		 //打散成字符串
		 $arrStr=implode('-',$arr);
		 return $arrStr;
		 }
	 
	
}
