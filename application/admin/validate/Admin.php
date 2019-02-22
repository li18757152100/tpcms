<?php
namespace app\admin\validate;
use think\Validate;
class Admin extends Validate
{

    protected $rule=[
        'name'=>'unique:admin|require|max:25',
        'password'=>'require|min:6',
		'email'=>'email',
    ];


    protected $message=[
        'name.require'=>'管理员名称不得为空！',
        'name.unique'=>'管理员名称不得重复！',
		'name.max'=> '管理员最多不能超过25个字符！',
        'password.require'=>'管理员密码不得为空！',
        'password.min'=>'密码不得少于6为！',
		'email'=>'邮箱格式不合法！',
    ];

    protected $scene=[
        'save'=>['name','password','email'],
        'edit'=>['name','email','password'=>'min:6'],
    ];





    

    




   

	












}
