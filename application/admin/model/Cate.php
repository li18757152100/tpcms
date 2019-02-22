<?php
namespace app\admin\model;
use think\Model;
class Cate extends Model
{
	//模型层树形结构
	 public function catetree(){
		 //获取所有栏目
		 $cateres = $this->order('sort desc')->select();
		 //对所有栏目进行一个排序;
		 return $this->sort($cateres);
		 }
	 public function sort($data,$pid=0,$level=0){
		 //定义一个空数组
		 static $arr=array();
		 //循环一下栏目
		 foreach($data as $k => $v){
			 //判断是否为顶级
			 if($v['pid']==$pid){
				 //节省资源，把字段level追加到空数组中；
				 $v['level']=$level;
				 //存放到空数组；
				 $arr[]=$v;
				 //执行递归
				 $this->sort($data,$v['id'],$level+1);
			 }
		 }
		 return $arr;
	 }
	 
	 //获取了栏目id
	 public function getchilrenid($cateid){
		 $cateres=$this->select();
		 return $this->_getchilrenid($cateres,$cateid);
	 }
	 
	 public function _getchilrenid($cateres,$cateid){
		 //定义一个空数组
		 static $arr=array();
		 foreach($cateres as $k => $v){
			 if($v['pid']==$cateid){
				 $arr[]=$v['id'];
				 //再找子栏目的子栏目id
				 $this->_getchilrenid($cateres,$v['id']);
			 }
		 }
		 return $arr;
		 }
		 
		 //钩子函数
	protected static function init()
    {
        Cate::beforeInsert(function ($cate) {
            //获取一下上传的文件对象
            if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'articlecate');
                if($info){
                    //$image='/' . 'public'.'/'.'uploads' . DS . 'ads'.'/'.$info->getSaveName();
					$image=$info->getSaveName();
                    $cate['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
        });
		Cate::event('before_update',function($cate){
          if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
			    $arts=Cate::find($cate->id);
          		$thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/articlecate/'.$arts['image'];
                if(file_exists($thumbpath)){
                	@unlink($thumbpath);
                }
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'articlecate');
                if($info){
					$image=$info->getSaveName();
                    $cate['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
      });
	  
	  Cate::event('before_delete', function ($cate) {
          
            
                $arts=Cate::find($cate->id);
                $thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/articlecate/'.$arts['image'];
                if(file_exists($thumbpath)){
                    @unlink($thumbpath);
                }
            
        });
		
		
	  
    } 
	 
	 
}
