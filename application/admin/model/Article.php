<?php
namespace app\admin\model;
use think\Model;
class Article extends Model
{
	protected $field=true;
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
		 //后置钩子
        Article::afterInsert(function ($article) {
                $data=input('post.');
                foreach ($_FILES['fimage']['tmp_name'] as $k => $v) {
                    if(!$v){
                        unset($data['pictitle'][$k]);
                    }
             
                sort($data['pictitle']);
                // 获取表单上传文件
                $files = request()->file('fimage');
                foreach($files as $k=>$file){
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->move(config('uploads_path.path').DS.'photos');
                    if($info){
                        // echo $info->getSaveName().'----'.$data['flink'][$k].'<br>';
                        $arr=array();
                        $arr['article_id']=$article->id;
                        $arr['fimage']=$info->getSaveName();
                        $arr['pictitle']=$data['pictitle'][$k];
                        db('photos')->insert($arr);
                    }else{
                        // 上传失败获取错误信息
                        return $file->getError();
                    }    
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
			   //更新图集
			    $data=input('post.');
			   if(isset($_FILES['fimage']['tmp_name'])){
                    foreach ($_FILES['fimage']['tmp_name'] as $k => $v) {
                        if(!$v){
                            unset($data['pictitle'][$k]);
                        }
                    }
                    sort($data['pictitle']);
                    // 获取表单上传文件
                    $files = request()->file('fimage');
                    foreach($files as $k=>$file){
                        // 移动到框架应用根目录/public/uploads/ 目录下
                        $info = $file->move(config('uploads_path.path').DS.'photos');
                        if($info){
                            // echo $info->getSaveName().'----'.$data['flink'][$k].'<br>';
                            $arr=array();
                            $arr['article_id']=$data['id'];
                            $arr['fimage']=$info->getSaveName();
                            $arr['pictitle']=$data['pictitle'][$k];
                            db('photos')->insert($arr);
                        }else{
                            // 上传失败获取错误信息
                            echo $file->getError();
                        }    
                    }
                }
                //修改链接
                if(isset($data['old_pictitle'])){
                    foreach ($data['old_pictitle'] as $k => $v) {
                        db('photos')->where(array('id'=>$k))->update(['pictitle'=>$v]);
                    }
                }
                
     
			   //end更新图集
      });
	  
	  Article::event('before_delete', function ($article) {
          
                $aid=$article->data['id'];
                $arts=Article::find($article->id);
                $thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/article/'.$arts['image'];
                if(file_exists($thumbpath)){
                    @unlink($thumbpath);
                }
				
				$fimgSrcRes=db('photos')->field('fimage')->where(array('article_id'=>$aid))->select();
            foreach ($fimgSrcRes as $k => $v) {
                $fimgSrc=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/photos/'.$v['fimage'];
                if(file_exists($fimgSrc)){
                @unlink($fimgSrc);
                }
            }
            db('photos')->where(array('article_id'=>$aid))->delete();
            
        });
		
		
	  
    } 
}
