<?php
namespace app\admin\validate;
use think\Validate;
class article extends Validate
{
	protected $rule = [
        'title'=>'unique:article|require|max:60',
        'cateid'=>'require',
        'content'=>'require',

    ];
     protected $message = [
        'title.require'=>'文章标题必须填写!',
        'title.unique'=>'文章标题不能重复!',
        'title.max'=>'文章标题不得大于60个字符!',
        'cateid.require'=>'文章栏目必须填写!',
        'content.require'=>'内容不能为空!',
    ];

    protected $scene=[
        'add'=>['title','cateid','content'],
        'edit'=>['title','cateid'],
    ];

    }


