<?php
namespace app\admin\validate;
use think\Validate;
class conf extends Validate
{
	protected $rule = [
        'cnname'=>'unique:conf|require|max:60',
        'enname'=>'unique:conf|require|max:60',
        'type'=>'require',

    ];
     protected $message = [
        'cnname.require'=>'中文名称不能为空!',
        'cnname.unique'=>'中文名称不得重复!',
        'cnname.max'=>'中文名称不得大于60个字符!',
        'enname.require'=>'英文名称不能为空!',
        'enname.unique'=>'英文名称不得重复!',
        'enname.max'=>'英文名称不得大于60个字符!',
		'type.require'=>'请选择配置类型!',
    ];

   protected $scene=[
        //'add'=>['cnname','enname','type'],
        'edit'=>['cnname','enname'],
    ];

    }


