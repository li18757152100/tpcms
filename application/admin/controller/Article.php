<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use think\Db;
use app\admin\model\Cate as CateModel;
use app\admin\model\Article as ArticleModel;
class Article extends Base
{
	
	//管理员列表
    public function lst()
    {
		
		//获取到所有的数据记录
		$count = ArticleModel::count();
		$artres=db('article')->field('a.*,b.typename')->alias('a')->join('fly_cate b','a.cateid=b.id')->order('a.id desc')->paginate(4);
		//模板赋值
		$this->view->assign('count', $count);
		$this->assign('artres',$artres);
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加文章
	public function create()
    {
		//栏目展示
		$cate = new CateModel();
		$cateres = $cate -> catetree();
		$this -> view -> assign('cateres', $cateres);
        //渲染模板
        return $this->view->fetch('add');

    }
	//保存数据
	public function save()
    {
		if(request()->isPost()){
    		$data=input('post.');
			$data['time']=time();
			//dump($data);die;
			$columns=array();
            //获取前缀
            $prefix=config('database.prefix');
            $tbArchives=$prefix.'article';//主文章表名称
            $_columns=Db::query("SHOW COLUMNS FROM {$tbArchives}");
            foreach ($_columns as $v) {
                $columns[]=$v['Field'];
            }
			
			$archives=array();
			foreach ($data as $k => $v) {
                if(in_array($k, $columns)){
                    if(is_array($v)){
                        $v=implode(',', $v);
                    }
                    $archives[$k]=$v;
                }
            }
			//dump($archives);die;
 		   $res = ArticleModel::create($archives);
    		if (is_null($res)){
               $status = 0;
               $msg = '添加文章失败~~';
            }

            $status = 1;
            $msg = '添加文章成功~~';
    	}else {
            return $this -> error('请求类型错误~~');
               }
		
		return ['status'=>$status, 'msg'=>$msg];
    }
	
	
	//删除文章
	public function delete($id)
    {
        
       //模型删除
        ArticleModel::destroy($id);
    }
	
	
	
	//编辑文章
	public function edit($id)
    {
       //实例化
		$cate = new CateModel();
		
       //查询顶级栏目
		$cateres = $cate -> catetree();
		//查询图集
		$photos=db('photos')->where(array('article_id'=>$id))->select();
		//dump($photos);die;
        //1.查询要编辑的记录
        $data = ArticleModel::get($id);

        //2.将查询结果赋值给模板
		$this -> view -> assign('photos', $photos);
        $this -> view -> assign('data', $data);
		$this -> view -> assign('cateres', $cateres);

        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update()
    {
        //1.获取所有提交过来的数据，包括文件
       $data = $this ->request -> param(true);
	   
	      $columns=array();
			 //获取前缀
            $prefix=config('database.prefix');
            $tbArchives=$prefix.'article';//主文章表名称
            $_columns=Db::query("SHOW COLUMNS FROM {$tbArchives}");
            foreach ($_columns as $v) {
                $columns[]=$v['Field'];
            }
			$archives=array();
			foreach ($data as $k => $v) {
                if(in_array($k, $columns)){
                    if(is_array($v)){
                        $v=implode(',', $v);
                    }else{
						//清空复选框
						db('article')->where('id', input('id'))->update(['attr' => '']);

						}
                    $archives[$k]=$v;
                }
            }
	   //2.执行更新操作
		 $article = new ArticleModel();
         $res=$article->update($archives);
		 //更新成功的提示信息
            $status = 1;
            $msg = '更新文章成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新文章失败~~';
            }
	return ['status'=>$status, 'msg'=>$msg];	
    }
	//异步删除图集记录
    public function ajaxdel(){
        $id=input('id');
        $photos=db('photos')->find($id);
        $imgSrc=$photos['fimage'];
        $imgSrc=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/photos/'.$imgSrc;
		echo $imgSrc;
        if(file_exists($imgSrc)){
            @unlink($imgSrc);
			
        }
        $del=db('photos')->delete($id);
		if(!empty($del)){
			$status = 1;
            $msg = '删除图集成功~~';
			}else {
				$status = 0;
                $msg = '删除图集失败~~';
				}
		
 	   return ['status'=>$status, 'msg'=>$msg];		
    }
	
}
