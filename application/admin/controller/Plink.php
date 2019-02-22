<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Link as LinkModel;
use app\admin\model\Plink as PlinkModel;
class Plink extends Base
{
	
	//分类列表
    public function lst()
    {
		//获取到所有的数据记录
        //$postiontlst = PostionModel::all();
		$plinklst = PlinkModel::order('sort desc')->select();
        $count = PlinkModel::count();
		//更新排序
		if(request()->isPost()){
            $plink = new PlinkModel();
            $sorts=input('post.');
			//dump($sorts);die;
            foreach ($sorts as $k => $v) {
                $plink->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功~~',url('lst'));
            return;
        }
		//模板赋值
        $this->view->assign('plinklst', $plinklst);
        $this->view->assign('count', $count);
		
		
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加分类
	public function create()
    {
        //渲染模板
        return $this->view->fetch('add');

    }
	//保存数据
	public function save(Request $request)
    {
		//判断一下提交类型
        if ($request -> isAjax(true)) {
            $data = array_filter($request->param());
			//dump($data);die;
			$res = PlinkModel::create($data);
            //更新成功的提示信息
            $status = 1;
            $msg = '添加友情板块成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '添加友情板块失败~~';
            }
			
			}
		
	
        return ['status'=>$status, 'msg'=>$msg];
        
    }
	
	
	//删除幻灯分类
	public function delete($id)
    {
	  //$banner = new BannerModel();
     // $banner->where('postionid',$id)->delete();
	 $linkRes=db('link')->where('linkid',$id)->select();
	 foreach ($linkRes as $k => $v) {
				if($v['image']){
                    $artSrc=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/links/'.$v['image'];
                    if(file_exists($artSrc)){
                        @unlink($artSrc);
                    }
                }
				//删除记录
                db('link')->delete($v['id']);
				}
      //模型删除  
     PlinkModel::destroy($id);
    }
	
	
	
	//编辑分类
	public function edit(Request $request)
    {
       //1.读取单条分类信息
        $plink = PlinkModel::get($request->param('id'));

        //2.将当前分类的信息赋值给模板
        $this -> view -> assign('plink', $plink);

        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update(Request $request)
    {
       if ($request -> isAjax(true)){

            //获取提交的数据,自动过滤一下空值
            $data = array_filter($request->param());
            //更新数据
            /*$res = PostionModel::update([
            'typename' => $data['typename'],
			'description' => $data['description'],
            'sort' => $data['sort']
             ],['id'=> $data['id']]);*/
			 $plink = new PlinkModel();
             $res=$plink->allowField(true)->update($data);

            //更新成功的提示信息
            $status = 1;
            $msg = '更新友情板块成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新友情板块失败~~';
            }
        }
        return ['status'=>$status, 'msg'=>$msg]; 
    }
	
	
}
