<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\AuthGroup as AuthGroupModel;
use app\admin\model\AuthRule as AuthRuleModel;
class AuthGroup extends Base
{
	
	//列表
    public function lst()
    {
		//获取到所有的数据记录
		$authgrouplst = AuthGroupModel::paginate(6);
		$count = AuthGroupModel::count();
		//模板赋值
        $this->view->assign('authgrouplst', $authgrouplst);
        $this->view->assign('count', $count);
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加配置
	public function create()
    {
		//获取规则列表
		$authRule = new AuthRuleModel();
		$authRuleRes = $authRule->authRuleTree();
		//dump($authRuleRes);die;
		//模板赋值
        $this->view->assign('authRuleRes', $authRuleRes);
        //渲染模板
        return $this->view->fetch('add');

    }
	//保存数据
	public function save(Request $request)
    {
        //判断一下提交类型
        if ($request -> isAjax(true)) {
            $data = array_filter($request->param());
			if(!empty($data['rules'])){
				$data['rules']=implode(',',$data['rules']);
			}
			//dump($data);die;
			$res = AuthGroupModel::create($data);
            //更新成功的提示信息
            $status = 1;
            $msg = '添加成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '添加失败~~';
            }
			
			}
		
	
        return ['status'=>$status, 'msg'=>$msg];
    }
	
	
	//删除用户组
	public function delete($id)
    {
        //模型删除
        AuthGroupModel::destroy($id);

    }
	
	
	
	//编辑用户组
	public function edit(Request $request)
    {
		//获取规则列表
		$authRule = new AuthRuleModel();
		$authRuleRes = $authRule->authRuleTree();
		//dump($authRuleRes);die;
		//模板赋值
        $this->view->assign('authRuleRes', $authRuleRes);
		
       //1.读取单条分类信息
        $authgroups = AuthGroupModel::get($request->param('id'));
		//dump($conf);die;

        //2.将当前分类的信息赋值给模板
        $this -> view -> assign('authgroups', $authgroups);


        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update(Request $request)
    {
        if ($request -> isAjax(true)){
			//获取提交的数据,自动过滤一下空值
            $data = array_filter($request->param());
			if(!empty($data['rules'])){
				$data['rules']=implode(',',$data['rules']);
			}else{$data['rules']=null;}
			$_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;
            }
            if(!in_array('status', $_data)){
                $data['status']=0;
            }		
			
			$res = AuthGroupModel::update($data,['id'=>$data['id']]);
            //$res=$conf->allowField(true)->save($data,['id'=>$data['id']]);
            //更新成功的提示信息
            $status = 1;
            $msg = '更新成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新失败~~';
            }
		}
		return ['status'=>$status, 'msg'=>$msg]; 
    }
	//异步修改用户组状态
    public function changeStatus(){
        $id=input('id');
        $authGroups=db('authGroup')->field('status')->find($id);
        if($authGroups['status']==1){
            db('authGroup')->where(array('id'=>$id))->update(['status'=>0]);
			$status = 0;
            $msg = '禁用';
        }else{
            db('authGroup')->where(array('id'=>$id))->update(['status'=>1]);
			$status = 1;
            $msg = '启用';
        }
		return ['status'=>$status, 'msg'=>$msg];
    }
	
	
}
