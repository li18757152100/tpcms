<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\AuthRule as AuthRuleModel;
class AuthRule extends Base
{
	//前置操作删除权限先删除子权限
	protected $beforeActionList = [
        //'first',
        //'second' =>  ['except'=>'hello'],
        'delsoncate'  =>  ['only'=>'delete'],
    ];
	
	//列表
    public function lst()
    {
		//获取到所有的数据记录
		//$authrulelst = AuthRuleModel::paginate(6);
		$authRule = new AuthRuleModel();
		$authrulelst = $authRule->authRuleTree();
		$count = AuthRuleModel::count();
		//更新排序
		if(request()->isPost()){
            $sorts=input('post.');
			//dump($sorts);die;
            foreach ($sorts as $k => $v) {
                $authRule->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功！',url('lst'));
            return;
        }
		//模板赋值
        $this->view->assign('authrulelst', $authrulelst);
        $this->view->assign('count', $count);
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加权限
	public function create()
    {
		//$authruleres=AuthRuleModel::select();
		$authRule = new AuthRuleModel();
		$authruleres = $authRule->authRuleTree();
		//模板赋值
        $this->view->assign('authruleres', $authruleres);
        //渲染模板
        return $this->view->fetch('add');

    }
	//保存数据
	public function save(Request $request)
    {
        //判断一下提交类型
        if ($request -> isAjax(true)) {
            $data = $request->param();
			$plevel=db('auth_rule')->where('id',$data['pid'])->field('level')->find();
            if($plevel){
               $data['level']=$plevel['level']+1;
				
            }else{
               $data['level']=0; 
            }
			//dump($data);die;
			$res = AuthRuleModel::create($data);
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
	
	
	//删除权限
	public function delete($id)
    {
        //模型删除
        AuthRuleModel::destroy($id);

    }
	
	public function delsoncate(){
        $authGruleid=input('id'); //要删除的当前权限的id
        $authRule = new AuthRuleModel();
        $sonids=$authRule->getchilrenid($authGruleid);
        $allauthGruleid=$sonids;
        $allauthGruleid[]=$authGruleid;
       
        if($sonids){
        db('authRule')->delete($sonids);
        }
    }
	
	//编辑权限
	public function edit(Request $request)
    {
		$authRule = new AuthRuleModel();
		$authruleres = $authRule->authRuleTree();
       //1.读取单条分类信息
        $authrules = AuthRuleModel::get($request->param('id'));
		//dump($conf);die;

        //2.将当前分类的信息赋值给模板
        $this -> view -> assign('authrules', $authrules);
        $this->view->assign('authruleres', $authruleres);

        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update(Request $request)
    {
        if ($request -> isAjax(true)){
			//获取提交的数据,自动过滤一下空值
            $data = $request->param();
			$plevel=db('auth_rule')->where('id',$data['pid'])->field('level')->find();
            if($plevel){
                $data['level']=$plevel['level']+1;
            }else{
               $data['level']=0; 
            }
			
			$_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;
            }
            if(!in_array('status', $_data)){
                $data['status']=0;
            }		
			
			$res = AuthRuleModel::update($data,['id'=>$data['id']]);
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
	
	 public function ajaxlst(){
        if(request()->isAjax()){
        $ruleid=input('ruleid');
        $sonids=model('authRule')->getchilrenid($ruleid);
        echo json_encode($sonids);
        }else{
            return $this->error('非法操作！');
        }
    }
	
}
