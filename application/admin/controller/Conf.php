<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Conf as ConfModel;
class Conf extends Base
{
	
	//管理配置
    public function lst()
    {
		//查询配置
		$ConfLst = ConfModel::order('sort asc')->paginate(6);
        $count = ConfModel::count();
		$page = $ConfLst->render();
		//更新排序
		if(request()->isPost()){
            $conf = new ConfModel();
            $sorts=input('post.');
			//dump($sorts);die;
            foreach ($sorts as $k => $v) {
                $conf->update(['id'=>$k,'sort'=>$v]);
            }
            return $this->success('更新排序成功~~',url('lst'));
            return;
        }
		//模板赋值
        $this->view->assign('ConfLst', $ConfLst);
        $this->view->assign('count', $count);
		$this->assign('page',$page);
		//渲染模板
        return $this -> view -> fetch('lst');
    }
	
	
	//添加配置
	public function create()
    {
        //渲染模板
        return $this->view->fetch('add');

    }
	//保存数据
	public function save(Request $request)
    {
        //判断一下提交类型
        if ($request -> isAjax(true)) {
            $data = array_filter($request->param());
			//数据验证
			 $validate = \think\Loader::validate('Conf');
            if(!$validate->check($data)){
              return  $this->error($validate->getError());
            }
			//添加的中文逗号替换成英文逗号
			if(!empty($data['values'])){
				$data['values']=str_replace('，',',',$data['values']);
			}
			//dump($data);die;
			$res = ConfModel::create($data);
            //更新成功的提示信息
            $status = 1;
            $msg = '添加配置成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '添加配置失败~~';
            }
			
			}
		
	
        return ['status'=>$status, 'msg'=>$msg];
    }
	
	
	//删除配置
	public function delete($id)
    {
        //模型删除
        ConfModel::destroy($id);

    }
	
	
	
	//编辑配置
	public function edit(Request $request)
    {
       //1.读取单条分类信息
        $confs = ConfModel::get($request->param('id'));
		//dump($conf);die;

        //2.将当前分类的信息赋值给模板
        $this -> view -> assign('confs', $confs);


        //3.渲染模板
        return $this->view->fetch('edit');
    }
	//执行更新操作
    public function update(Request $request)
    {
        if ($request -> isAjax(true)){
			//获取提交的数据,自动过滤一下空值
            $data = array_filter($request->param());
			//数据验证
			 $validate = \think\Loader::validate('Conf');
            if(!$validate->scene('edit')->check($data)){
              return  $this->error($validate->getError());
            }
			if(!empty($data['values'])){
				$data['values']=str_replace('，',',',$data['values']);
			}
			//$conf = new ConfModel();
            //$res=$conf->allowField(true)->update($data);
			$res = ConfModel::update($data,['id'=>$data['id']]);
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
	//系统参数
	public function conf(){
		
		//1.查询配置项
		$confres=ConfModel::order('sort asc')->select();
		//2.将当前分类的信息赋值给模板
        $this -> view -> assign('confres', $confres);
		//渲染模板
        return $this->view->fetch('conf');
	}
	//修改系统参数
	public function upconf(Request $request){
		if ($request -> isAjax(true)){
			//获取提交的数据,自动过滤一下空值
            $data = array_filter($request->param());
			$_formarr=array();
			foreach($data as $k => $v){
				$formarr[]=$k;
			}
			//重新组建一个一维数组
			$_confarr=db('conf')->field('enname')->select();
			$confarr=array();
			foreach($_confarr as $k => $v){
				$confarr[]=$v['enname'];
			}
			//判断一个字符串是否在一个数组中
			foreach($confarr as $k => $v){
				if(!in_array($v,$formarr)){
					//不在的话在组建一个数组
					$checkboxarr[]=$v;
				}
				
			}

		   foreach($data as $k=>$v){
					$res = ConfModel::where('enname',$k)->update(['value'=>$v]);
			}
			//dump($data);die;
			//执行更新
			//$res = ConfModel::update($data,['id'=>$data['id']]);
			//更新成功的提示信息
            $status = 1;
            $msg = '修改配置成功~~';

            //如果更新失败
            if (is_null($res)) {
                $status = 0;
                $msg = '更新失败~~';
            }
		}
		return ['status'=>$status, 'msg'=>$msg]; 
		}
	
	
}
