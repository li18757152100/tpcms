<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Banner as BannerModel;
use app\admin\model\Postion as PostionModel;
class Postion extends Base
{
	
	//分类列表
    public function lst()
    {
		//获取到所有的数据记录
        //$postiontlst = PostionModel::all();
		$postiontlst = PostionModel::order('sort desc')->select();
        $count = PostionModel::count();
		//更新排序
		if(request()->isPost()){
            $postion = new PostionModel();
            $sorts=input('post.');
			//dump($sorts);die;
            foreach ($sorts as $k => $v) {
                $postion->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功~~',url('lst'));
            return;
        }
		//模板赋值
        $this->view->assign('postiontlst', $postiontlst);
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
			$res = PostionModel::create($data);
            //更新成功的提示信息
            $status = 1;
            $msg = '添加广告位成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '添加广告位失败~~';
            }
			
			}
		
	
        return ['status'=>$status, 'msg'=>$msg];
        
    }
	
	
	//删除幻灯分类
	public function delete($id)
    {
	  //$banner = new BannerModel();
     // $banner->where('postionid',$id)->delete();
	 $bannerRes=db('banner')->where('postionid',$id)->select();
	 foreach ($bannerRes as $k => $v) {
				if($v['image']){
                    $artSrc=$_SERVER['DOCUMENT_ROOT'].'/public/uploads/ads/'.$v['image'];
                    if(file_exists($artSrc)){
                        @unlink($artSrc);
                    }
                }
				//删除记录
                db('banner')->delete($v['id']);
				}
      //模型删除  
     PostionModel::destroy($id);
    }
	
	
	
	//编辑分类
	public function edit(Request $request)
    {
       //1.读取单条分类信息
        $postion = PostionModel::get($request->param('id'));

        //2.将当前分类的信息赋值给模板
        $this -> view -> assign('postion', $postion);

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
			 $postion = new PostionModel();
             $res=$postion->allowField(true)->update($data);

            //更新成功的提示信息
            $status = 1;
            $msg = '更新广告位成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新广告位失败~~';
            }
        }
        return ['status'=>$status, 'msg'=>$msg]; 
    }
	
	
}
