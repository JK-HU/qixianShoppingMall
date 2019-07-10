<?php
/**
 * 门店管理
 */
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
use think\Db;
use clt\Leftnav;
class Store extends Common
{
    /**
     * 门店列表
     * @return array|mixed
     */
    public function store_lists()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('shop_store')->alias('s')
                ->field('s.*,r1.name as province_title, r2.name as city_title, r3.name as district_title')
                ->join('region r1', 'r1.id=s.province', 'LEFT')
                ->join('region r2', 'r2.id=s.city', 'LEFT')
                ->join('region r3', 'r3.id=s.district', 'LEFT')
                ->where('s.title','like',"%".$key."%")
                ->order('s.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k => &$v) {
                $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch('lists');
    }
    /**
     * 增加门店
     * @return array|mixed
     */
    public function add_store()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            $data['create_time'] = time();
            DB::name('shop_store')->insert($data);
            return $result = ['code'=>1,'msg'=>'门店添加成功!','url'=>url('store_lists')];
        } else {
            $province = db('Region')->where ( array('pid'=>1) )->select ();
            $this->assign('province', $province);

            return $this->fetch('edit');
        }
    }
    /**
     * 编辑门店
     * @return array|mixed
     */
    public function edit_store()
    {
        if ( request()->isPost() )
        {
            $datas = input('post.');
            if( DB::name('shop_store')->update($datas) )
            {
                return json(['code' => 1, 'msg' => '保存成功!', 'url' => url('store_lists')]);
            } else {
                return json(['code' => 0, 'msg' =>'保存失败！']);
            }
        } else {
            $id = input('id');
            $store = DB::name('shop_store')->where('id', $id)->find();
            $this->assign('info',$store);

            $province = db('Region')->where ( array('pid'=>1) )->select ();
            $city = db('Region')->where ( array('pid'=>$store['province']) )->find();
            $district = db('Region')->where ( array('pid'=>$store['city']) )->find();
            $this->assign('province', $province);
            $this->assign('city', $city['name']);
            $this->assign('district', $district['name']);

            return $this->fetch('edit');
        }
    }
    /**
     * 删除门店
     * @return array|mixed
     */
    public function store_del()
    {
        db('shop_store')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    /**
     * 批量删除
     * @return array|mixed
     */
    public function store_all_del()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('shop_store')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('store_lists');
        return $result;
    }
}