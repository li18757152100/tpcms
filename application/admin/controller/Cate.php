<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Cate as CateModel;
use app\admin\model\Article as ArticleModel;
class Cate extends Base
{
	//前置操作删除栏目先删除子栏目
	protected $beforeActionList = [
        //'first',
        //'second' =>  ['except'=>'hello'],
        'delsoncate'  =>  ['only'=>'delete'],
    ];
	
	//栏目列表
    public function lst()
    {
		$cate = new CateModel();
		
		$Catelst = $cate ->catetree();
		//更新排序
		if(request()->isPost()){
            $sorts=input('post.');
			//dump($sorts);die;
            foreach ($sorts as $k => $v) {
                $cate->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功！',url('lst'));
            return;
        }
		//dump($Catelst);die;
		$count = CateModel::count();	
		//模板赋值
        $this->view->assign('Catelst', $Catelst);
        $this->view->assign('count', $count);
		
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	public function ajaxlst(){
        if(request()->isAjax()){
        $cateid=input('cateid');
        $sonids=model('cate')->childrenids($cateid);
        echo json_encode($sonids);
        }else{
            $this->error('非法操作！');
        }
    }
	//添加栏目
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
		//判断一下提交类型
        if ($this->request->isPost()) {

            //获取一下提交的数据,包括上传文件
            $data = $this->request->param(true);
             //判断是否隐藏栏目
            $_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;
            }
            if(!in_array('is_show', $_data)){
                $data['is_show']=0;
            }
            
            $res = CateModel::create($data);

            //判断新增是否成功
            if (is_null($res)){
                $this->error('新增栏目失败');
            }

            $this->success('新增栏目成功~~',url('lst'));

        }else {
            $this -> error('请求类型错误~~');
               }
		
		
    }
	
	
	//删除栏目
	public function delete($id)
    {
        //模型删除
        CateModel::destroy($id);

    }
	public function delsoncate(){
		//要删除的当前栏目id
		$cateid = input('id');
		$cate = new CateModel();
		$sonids=$cate->getchilrenid($cateid);
		$allcateid=$sonids;
        $allcateid[]=$cateid;
        foreach ($allcateid as $k=>$v) {
		    $cates=db('cate')->find($v);
			$thumbpath=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/articlecate/'.$cates['image'];
                if(file_exists($thumbpath)){
                	@unlink($thumbpath);
                }
            //$article=new ArticleModel;
            //$article->where(array('cateid'=>$v))->delete();
			$artRes=db('article')->where(array('cateid'=>$v))->select();
			foreach ($artRes as $k1 => $v1) {
				if($v1['image']){
                    $artSrc=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/article/'.$v1['image'];
                    if(file_exists($artSrc)){
                        @unlink($artSrc);
                    }
                }
				//删除图集
				$fimgSrcRes=db('photos')->field('fimage')->where(array('article_id'=>$v1['id']))->select();
            foreach ($fimgSrcRes as $k2 => $v2) {
                $fimgSrc=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/photos/'.$v2['fimage'];
                if(file_exists($fimgSrc)){
                @unlink($fimgSrc);
                }
            }
            db('photos')->where(array('article_id'=>$v1['id']))->delete();
				//删除记录
                db('article')->delete($v1['id']);
				}
			
        }
		if($sonids){
			db('cate')->delete($sonids);
			}
		
		}
	
	
	//编辑栏目
	public function edit(Request $request)
    {
		//栏目展示
		$cate = new CateModel();
		$cateres = $cate -> catetree();
		$this -> view -> assign('cateres', $cateres);
        //1.查询要编辑的记录
        $cates = CateModel::get($request->param('id'));

        //2.将查询结果赋值给模板
        $this -> view -> assign('cates', $cates);

        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update()
    {
       //1.获取所有提交过来的数据，包括文件
       $data = $this ->request -> param(true);
       //判断是否隐藏栏目
            $_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;
            }
            if(!in_array('is_show', $_data)){
                $data['is_show']=0;
            }
        //2.执行更新操作
		 $cate = new CateModel();
         $res=$cate->allowField(true)->update($data);

        //更新成功的提示信息
            $status = 1;
            $msg = '更新栏目成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新栏目失败~~';
            }
	return ['status'=>$status, 'msg'=>$msg];		
    }
	//ajax异步修改栏目显示状态
	public function changeStatus(){
        $cateid=input('cateid');
        $cates=db('cate')->field('is_show')->find($cateid);
        if($cates['is_show']==1){
            db('cate')->where(array('id'=>$cateid))->update(['is_show'=>0]);
			$status = 0;
            $msg = '隐藏栏目';
        }else{
             db('cate')->where(array('id'=>$cateid))->update(['is_show'=>1]);
			 $status = 1;
             $msg = '显示栏目';
        }
		return ['status'=>$status, 'msg'=>$msg];
		    }

    /*public function changestatus(){
        if(request()->isAjax()){
            $cateid=input('cateid');
            $status=db('cate')->field('is_show')->where('id',$cateid)->find();
            $status=$status['is_show'];
            if($status==1){
                db('cate')->where('id',$cateid)->update(['is_show'=>0]);
                echo 1;//由显示改为隐藏
            }else{
                db('cate')->where('id',$cateid)->update(['is_show'=>1]);
                echo 2;//由隐藏改为显示
            }  
        }else{
            $this->error("非法操作！");
        }
    }*/
	
}
