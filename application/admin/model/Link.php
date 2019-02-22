<?php
namespace app\admin\model;
use think\Model;
class Link extends Model
{
	//钩子函数
	protected static function init()
    {
        Link::beforeInsert(function ($link) {
            //获取一下上传的文件对象
            if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'links');
                if($info){
                    //$image='/' . 'public'.'/'.'uploads' . DS . 'ads'.'/'.$info->getSaveName();
					$image=$info->getSaveName();
                    $link['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
        });
		Link::event('before_update',function($link){
          if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
			    $arts=Link::find($link->id);
          		$thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/links/'.$arts['image'];
                if(file_exists($thumbpath)){
                	@unlink($thumbpath);
                }
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'links');
                if($info){
					$image=$info->getSaveName();
                    $link['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
      });
	  
	  Link::event('before_delete', function ($link) {
          
            
                $arts=Link::find($link->id);
                $thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/links/'.$arts['image'];
                if(file_exists($thumbpath)){
                    @unlink($thumbpath);
                }
            
        });
		
		
	  
    } 

}
