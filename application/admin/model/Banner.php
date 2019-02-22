<?php
namespace app\admin\model;
use think\Model;
class Banner extends Model
{
	//钩子函数
	protected static function init()
    {
        Banner::beforeInsert(function ($banner) {
            //获取一下上传的文件对象
            if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'ads');
                if($info){
                    //$image='/' . 'public'.'/'.'uploads' . DS . 'ads'.'/'.$info->getSaveName();
					$image=$info->getSaveName();
                    $banner['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
        });
		Banner::event('before_update',function($banner){
          if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
			    $arts=Banner::find($banner->id);
          		$thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/ads/'.$arts['image'];
                if(file_exists($thumbpath)){
                	@unlink($thumbpath);
                }
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'ads');
                if($info){
					$image=$info->getSaveName();
                    $banner['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
      });
	  
	  Banner::event('before_delete', function ($banner) {
          
            
                $arts=Banner::find($banner->id);
                $thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/ads/'.$arts['image'];
                if(file_exists($thumbpath)){
                    @unlink($thumbpath);
                }
            
        });
		
		
	  
    } 

}
