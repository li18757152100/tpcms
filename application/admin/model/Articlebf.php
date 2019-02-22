<?php
namespace app\admin\model;
use think\Model;
class Article extends Model
{
	//钩子函数
	protected static function init()
    {
        Article::beforeInsert(function ($article) {
            //获取一下上传的文件对象
            if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'article');
                if($info){
                    //$image='/' . 'public'.'/'.'uploads' . DS . 'ads'.'/'.$info->getSaveName();
					$image=$info->getSaveName();
                    $article['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
        });
		Article::event('before_update',function($article){
          if(isset($_FILES['image']) && $_FILES['image']['tmp_name']){
			    $arts=Article::find($article->id);
          		$thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/article/'.$arts['image'];
                if(file_exists($thumbpath)){
                	@unlink($thumbpath);
                }
                $file = request()->file('image');
				$map = [
                'ext'=>'jpg,png,gif',
                'size'=> 3000000
                ];
                $info = $file->validate($map)->move(config('uploads_path.path').DS.'article');
                if($info){
					$image=$info->getSaveName();
                    $article['image']=$image;
                }else {
                       $this -> error('上传格式错误~~');
                       }
               }
      });
	  
	  Article::event('before_delete', function ($article) {
          
            
                $arts=Article::find($article->id);
                $thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/article/'.$arts['image'];
                if(file_exists($thumbpath)){
                    @unlink($thumbpath);
                }
            
        });
		
		
	  
    } 
}
