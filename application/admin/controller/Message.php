<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Message as MessageModel;
class Message extends Base
{
	
	//留言列表
    public function lst()
    {
		//1.获取总条数
   		$count = MessageModel::count();
		//2.用模型获取分页数据
		//$admin = AdminModel::all();
        $message_list=MessageModel::order(['id'])->  paginate(4);
        //dump($admin);die;
		
        //3.将当前管理员的信息赋值给模板
        $this -> view -> assign('message_list', $message_list);
		$this->view->assign('count', $count);
		
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	//审核
	public function changestatus(){
        if(request()->isAjax()){
            $messageid=input('messageid');
            $status=db('message')->field('is_show')->where('id',$messageid)->find();
            //$status=$status['is_show'];
            if($status['is_show']==1){
            db('message')->where(array('id'=>$messageid))->update(['is_show'=>0]);
			$status = 0;
            $msg = '待审核';
        }else{
             db('message')->where(array('id'=>$messageid))->update(['is_show'=>1]);
			 $status = 1;
             $msg = '已审核';
        }
		return ['status'=>$status, 'msg'=>$msg]; 
        }else{
           return $this->error("非法操作！");
        }
    }
	
	//回复留言

	public function create(Request $request)
    {
        //1.查询要回复的记录
        $message = MessageModel::get($request->param('id'));

        //2.将查询结果赋值给模板
        $this -> view -> assign('message', $message);

        //3.渲染模板
        return $this->view->fetch('add');
    }
	
	//保存数据
	//执行更新操作
    public function save(Request $request)
    {
        if ($request -> isAjax(true)){

            //获取提交的数据,自动过滤一下空值
            //$data = $this->request->param(true);
			$data = array_filter($request->param());
	        $data['update_time'] = time();
            //设置更新条件
            $map = ['is_update' => $data['is_update']];
			
            //更新用户表
            $res = MessageModel::update($data, $map);

            //回复成功的提示信息
            $status = 1;
            $msg = '回复成功~~';

            //如果回复失败
            if (is_null($res)) {
                $status = 0;
                $msg = '回复留言失败~~';
            }
        }
        return ['status'=>$status, 'msg'=>$msg];
    }
	
	//删除留言
	public function delete($id)
    {
        //模型删除
        MessageModel::destroy($id);


    }
	//全选删除留言
	public function  delall()
   {
    $str = input ('str');
    //判断是否勾选数据
    if ($str!=="") {
        $id = explode ('&', $str);//将字符串转化为数组
        $user = MessageModel::destroy ($id);
        if ($user) {
            $result = array(
                'status' => '1',
                'msg' => '删除留言成功！'
            );
            return json ($result);
        } else {
            $result = array(
                'status' => '0',
                'msg' => '删除留言失败！'
            );
            return json ($result);
        }
    } else {
        $result = array(
            'status' => '2',
            'msg' => '请勾选数据！'
        );
        return json ($result);

    }
}
	
	
	
	
}
