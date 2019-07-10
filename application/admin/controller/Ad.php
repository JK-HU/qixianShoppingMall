<?php
/**
 * 广告管理
 */
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
class Ad extends Common
{
    protected function initialize(){
        parent::initialize();
    }

    /**
     * 广告列表
     * @return array|mixed
     */
    public function index()
    {
        $list_type = db('ad_type')->order('sort')->select();
        $this->assign('list_type', $list_type);

        if(Request::isAjax()) {
            $key = input('post.key');
            $this->assign('testkey', $key);
            $type_id = input('post.type_id');
            $where = [];
            if(!empty($type_id)){
                $where['a.type_id'] = $type_id;
            }
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::table(config('database.prefix') . 'ad')->alias('a')
                ->join(config('database.prefix') . 'ad_type at', 'a.type_id = at.type_id', 'left')
                ->field('a.*,at.name as typename')
                ->where('a.name', 'like', "%" . $key . "%")
                ->where($where)
                ->order('a.sort')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }

            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 添加广告
     * @return mixed
     */
    public function add()
    {
        if(Request::isAjax()) {
            //构建数组
            $data = Request::except('file');
            $data['addtime'] = time();
            db('ad')->insert($data);
            cache('adList', NULL);
            $result['code'] = 1;
            $result['msg'] = '广告添加成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $adtypeList=db('ad_type')->order('sort')->select();
            $this->assign('adtypeList',json_encode($adtypeList,true));

            $this->assign('title',lang('add').lang('ad'));
            $this->assign('info','null');
            $this->assign('selected', 'null');
            return $this->fetch('form');
        }
    }

    /**
     * 修改广告
     * @return mixed
     */
    public function edit()
    {
        if(Request::isAjax()) {
            $data = Request::except('file');
            db('ad')->update($data);
            cache('adList', NULL);
            $result['code'] = 1;
            $result['msg'] = '广告修改成功!';
            $result['url'] = url('index');
            return $result;
        }else{
            $adtypeList=db('ad_type')->order('sort')->select();
            $ad_id=input('ad_id');
            $adInfo=db('ad')->where(array('ad_id'=>$ad_id))->find();
            $this->assign('adtypeList',json_encode($adtypeList,true));

            $selected = db('ad_type')->where('type_id',$adInfo['type_id'])->find();
            $this->assign('selected',json_encode($selected,true));

            $this->assign('info',json_encode($adInfo,true));
            $this->assign('title',lang('edit').lang('ad'));
            return $this->fetch('form');
        }
    }

    /**
     * 设置广告状态
     * @return array
     */
    public function editState()
    {
        $id=input('post.id');
        $open=input('post.open');
        if(db('ad')->where('ad_id='.$id)->update(['open'=>$open])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }

    /**
     * 设置排序
     * @return array
     */
    public function adOrder()
    {
        $ad = db('ad');
        $data = input('post.');
        if($ad->update($data)!==false){
            cache('adList', NULL);
            return $result = ['msg' => '操作成功！','url'=>url('index'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }

    /**
     * 删除广告
     * @return array
     */
    public function del()
    {
        db('ad')->where(array('ad_id'=>input('ad_id')))->delete();
        cache('adList', NULL);
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    /**
     * 批量删除广告
     * @return mixed
     */
    public function delall()
    {
        $map[] =array('ad_id','in',input('param.ids/a'));
        db('ad')->where($map)->delete();
        cache('adList', NULL);
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    /*************************** 广告位 *****************************/

    /**
     * 广告位列表
     * @return array|mixed
     */
    public function type()
    {
        if(Request::isAjax()) {
            $key = input('key');
            $this->assign('testkey', $key);
            $list = db('ad_type')->where('name', 'like', "%" . $key . "%")->order('type_id ASC')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 广告位排序
     * @return array
     */
    public function typeOrder()
    {
        $ad_type=db('ad_type');
        $data = input('post.');
        if($ad_type->update($data)!==false){
            return $result = ['msg' => '操作成功！','url'=>url('type'), 'code' =>1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }

    /**
     * 添加广告位
     * @return mixed
     */
    public function addType()
    {
        if(Request::isAjax()) {
            db('ad_type')->insert(input('post.'));
            $result['code'] = 1;
            $result['msg'] = '广告位保存成功!';
            $result['url'] = url('type');
            return $result;
        }else{
            $this->assign('title',lang('add').lang('ad').'位');
            $this->assign('info','null');
            return $this->fetch('typeForm');
        }
    }

    /**
     * 修改广告位
     * @return mixed
     */
    public function editType()
    {
        if(Request::isAjax()) {
            db('ad_type')->update(input('post.'));
            $result['code'] = 1;
            $result['msg'] = '广告位修改成功!';
            $result['url'] = url('type');
            return $result;
        }else{
            $type_id=input('param.type_id');
            $info=db('ad_type')->where('type_id',$type_id)->find();
            $this->assign('title',lang('edit').lang('ad').'位');
            $this->assign('info',json_encode($info,true));
            return $this->fetch('typeForm');
        }
    }

    /**
     * 删除广告位
     * @return array
     */
    public function delType()
    {
        $map['type_id'] = input('param.type_id');
        db('ad_type')->where($map)->delete();//删除广告位
        db('ad')->where($map)->delete();//删除该广告位所有广告
        return ['code'=>1,'msg'=>'删除成功！'];
    }
}