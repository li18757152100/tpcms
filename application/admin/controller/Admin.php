<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Admin as AdminModel;
use app\admin\model\AuthGroup as AuthGroupModel;
class Admin extends Base
{
	
	//管理员列表
    public function lst()
    {
		//1.获取总条数
   		$count = AdminModel::count();
		//2.用模型获取分页数据
		//$admin = AdminModel::all();
        //$admin_list=AdminModel::order(['id'])->  paginate(4);
		$admin_list=db('admin')->alias('a')->field('a.*,g.title')->join('auth_group g','a.groupid=g.id')->paginate(4);
        //dump($admin);die;
		
        //3.将当前管理员的信息赋值给模板
        $this -> view -> assign('admin_list', $admin_list);
		$this->view->assign('count', $count);
		
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加管理员
	public function create()
    {
		//显示用户组
		$authgrouplst = AuthGroupModel::where('status', 1)->select();
		//模板赋值
        $this->view->assign('authgrouplst', $authgrouplst);
        //渲染模板
        return $this->view->fetch('add');

    }
	//保存数据
	public function save()
    {
        //判断一下提交类型
        if ($this->request->isPost()) {

            //1.获取一下提交的数据
			
            $data = $this->request->param(true);
			$data['password']=md5($data['password']);
			$data['last_time']=time();
			//数据验证
			 $validate = \think\Loader::validate('Admin');
            if(!$validate->scene('save')->check($data)){
                $this->error($validate->getError());
            }
            //dump($data);die;
            //$res = AdminModel::create($data);
			//nsertGetId 方法添加数据成功返回添加数据的自增主键
			$res=db('admin')->insertGetId($data);
            $_data=array();//对应管理员和用户组
            $_data['uid']=$res;
            $_data['group_id']=$data['groupid'];
            $addGroupAccess=db('authGroupAccess')->insert($_data);
            //2判断新增是否成功
            if (is_null($res && $addGroupAccess)){
               $status = 0;
               $msg = '添加用户失败~~';
            }

            $status = 1;
            $msg = '添加用户成功~~';

        }else {
            return $this -> error('请求类型错误~~');
        }
		return ['status'=>$status, 'msg'=>$msg];
    }
	
	
	//删除管理员
	public function delete($id)
    {
		//if($id==1){
//          return  $this->error('超级管理员不允许删除！');
//        }
        //模型删除
        AdminModel::destroy($id);

    }
	
	
	
	//编辑管理员
	public function edit(Request $request)
    {
		//显示用户组
		$authgrouplst = AuthGroupModel::where('status', 1)->select();
		//模板赋值
        $this->view->assign('authgrouplst', $authgrouplst);
        //1.查询要编辑的记录
        $admin = AdminModel::get($request->param('id'));

        //2.将查询结果赋值给模板
        $this -> view -> assign('admin', $admin);

        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update(Request $request)
    {
        if ($request -> isAjax(true)){

            //获取提交的数据,自动过滤一下空值
            //$data = $this->request->param(true);
			$data = array_filter($request->param());
            $validate = \think\Loader::validate('Admin');
            if(!$validate->scene('edit')->check($data)){
               $this->error($validate->getError());
               }   
            //设置更新条件
            $map = ['is_update' => $data['is_update']];
			
            //更新用户表
            $res = AdminModel::update($data, $map);
			//更新用户组
            db('authGroupAccess')->where(array('uid'=>$data['id']))->update(['group_id'=>$data['groupid']]);
            //更新成功的提示信息
            $status = 1;
            $msg = '更新用户成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新用户失败~~';
            }
        }
        return ['status'=>$status, 'msg'=>$msg];
    }
	//ajax修改管理员状态
    public function changestatus(){
        $id=input('id');
        $admins=db('admin')->field('is_show')->find($id);
        $status=$admins['is_show'];
        if($status==1){
            db('admin')->where(array('id'=>$id))->update(['is_show'=>0]);
			$status = 0;
            $msg = '禁用';
        }else{
            db('admin')->where(array('id'=>$id))->update(['is_show'=>1]);
			 $status = 1;
             $msg = '启用';
        }
		return ['status'=>$status, 'msg'=>$msg];
    }
	
}
