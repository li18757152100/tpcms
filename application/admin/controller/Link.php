<?php
namespace app\admin\controller;
use app\admin\model\Link as LinkModel;
use app\admin\model\Plink as PlinkModel;
use app\admin\common\Base;
use think\Request;
class Link extends Base
{
	
	//链接列表
    public function lst()
    {
		//获取到所有的数据记录
		$count = LinkModel::count();
		$linkRes=db('link')->field('a.*,b.typename')->alias('a')->join('fly_plink b','a.linkid=b.id')->order('a.sort desc')->paginate(4);
		//更新排序
		if(request()->isPost()){
			$link = new LinkModel();
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $link->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功~~',url('lst'));
            return;
        }
		//模板赋值
        $this->view->assign('linkRes', $linkRes);
        $this->view->assign('count', $count);
		
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加链接
	public function create()
    {
		//获取分类
		$plink = PlinkModel::all();
	    $this->view->assign('plink', $plink);
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
         
            $res = LinkModel::create($data);

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
	
	
	//删除友情
	public function delete($id)
    {
        
      //模型删除
        LinkModel::destroy($id);
    }
	
	
	
	//编辑友情
	public function edit(Request $request)
    {
       //获取分类
		$plink = PlinkModel::all();
	    $this->view->assign('plink', $plink);
       //1.查询要编辑的记录
        $links = LinkModel::get($request->param('id'));

        //2.将查询结果赋值给模板
        $this -> view -> assign('links', $links);
        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update()
    {
		//1.获取所有提交过来的数据，包括文件
          $data = $this ->request -> param(true);

        //2.执行更新操作
		 $link = new LinkModel();
         $res=$link->allowField(true)->update($data);

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
	
	
}
