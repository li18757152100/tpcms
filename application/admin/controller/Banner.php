<?php
namespace app\admin\controller;
use app\admin\model\Banner as BannerModel;
use app\admin\model\Postion as PostionModel;
use app\admin\common\Base;
use think\Request;
class Banner extends Base
{
	
	//管理员列表
    public function lst()
    {
		//获取到所有的数据记录
		$count = BannerModel::count();
		$bannerRes=db('banner')->field('a.*,b.typename')->alias('a')->join('fly_postion b','a.postionid=b.id')->order('a.sort desc')->paginate(4);
		//更新排序
		if(request()->isPost()){
			$banner = new BannerModel();
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $banner->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功~~',url('lst'));
            return;
        }
		//模板赋值
        $this->view->assign('bannerRes', $bannerRes);
        $this->view->assign('count', $count);
		
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加幻灯
	public function create()
    {
		//获取分类
		$postion = PostionModel::all();
	    $this->view->assign('postion', $postion);
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
         
            $res = BannerModel::create($data);

            //判断新增是否成功
            if (is_null($res)){
               $status = 0;
                $msg = '添加失败~~';
            }

            $status = 1;
            $msg = '添加成功~~';

        }else {
            return $this -> error('请求类型错误~~');
               }
		
		return ['status'=>$status, 'msg'=>$msg];
    }
	
	
	//删除幻灯
	public function delete($id)
    {
        
      //模型删除
        BannerModel::destroy($id);
    }
	
	
	
	//编辑幻灯
	public function edit(Request $request)
    {
       //获取分类
		$postion = PostionModel::all();
	    $this->view->assign('postion', $postion);
       //1.查询要编辑的记录
        $ads = BannerModel::get($request->param('id'));

        //2.将查询结果赋值给模板
        $this -> view -> assign('ads', $ads);
        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update()
    {
		//1.获取所有提交过来的数据，包括文件
          $data = $this ->request -> param(true);
		  $_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;
            }
            if(!in_array('is_show', $_data)){
                $data['is_show']=0;
            }		

        //2.执行更新操作
		 $banner = new BannerModel();
         $res=$banner->allowField(true)->update($data);

        //更新成功的提示信息
            $status = 1;
            $msg = '更新成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新失败~~';
            }
	return ['status'=>$status, 'msg'=>$msg];	
        
    }
	//异步修改用户组状态
    public function changeStatus(){
        $id=input('id');
        $banners=db('banner')->field('is_show')->find($id);
        if($banners['is_show']==1){
            db('banner')->where(array('id'=>$id))->update(['is_show'=>0]);
			$status = 0;
            $msg = '隐藏';
        }else{
            db('banner')->where(array('id'=>$id))->update(['is_show'=>1]);
			$status = 1;
            $msg = '显示';
        }
		return ['status'=>$status, 'msg'=>$msg];
    }
	
}
