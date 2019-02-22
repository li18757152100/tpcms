<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use think\Request;
use think\Session;
class Login extends controller
{
	//渲染登录界面
	public function index(){
		if(session('user_info')){
           return $this->error('您已经登录，请勿重复登录~~',url('index/index')); 
        }
		return $this -> view -> fetch('index');
	}
    //验证用户身份
	 public function check(Request $request){
		//设置status
		$status = 0;
		//获取一下表单提交的数据，并保存到变量中
		$data = $request -> param();
		//dump($data);die;
		$userName = $data['name'];
		$password = md5($data['password']);
		//在admin表中查询，以用户为条件
		$map = ['name'=> $userName];
		$admin = Admin::get($map);
		//验证码验证
		$this->checks(input('code'));
		//将用户名和密码分开验证
		//如果没有查找到该用户
		if(is_null($admin)){
			//设置返回信息
			$msg = '用户名不正确~~';
		}elseif($admin -> password != $password){
			//设置一下密码不正确的提示信息
			$msg = '输入密码有误~~';
		}elseif($admin['is_show']==0){
			//用户被禁用
			$status = 3;
			$msg = '用户被禁用~~';
		}else{
		
			//用户名和密码都验证通过，表示合法
			//修改一下返回信息
			$status = 1;
			$msg = '登录后台成功~~';
			//更新表中的登录次数以及最后登录时间
			$admin -> setInc('login_count');
            $admin -> save(['last_time'=> time()]);
			
			//将用户登录的信息保存到session中,供其它控制器进行登录判断
            Session::set('user_id', $userName);
			session('id', $admin['id']);
            //这条语句只能存储username和password,其它字段不能存储
//            Session::set('user_info',$data);

//            应该修改成:
            Session::set('user_info',$admin->toArray());

			
			}
			
		return ['status'=> $status, 'msg'=> $msg];	
	}
	
	// 验证码检测
    public function checks($code='')
    {
        if (!captcha_check($code)) {
            return $this->error('验证码错误');
        } else {
            return true;
        }
    }
	
}
