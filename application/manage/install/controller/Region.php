<?php
/**
 * 地区管理
 */
namespace app\admin\controller;
use think\Db;

class Region extends Common
{
    private $_region_parent_data=[];
    private $_region_son_data=[];

    /**
     * 地区列表
     * @param int $pid
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists($pid=1)
    {
        if ( request()->isPost() )
        {
            $key = input('post.key');
            $page = input('page') ? input('page') : 1;
            $pageSize = input('limit') ? input('limit') : config('pageSize');
            $list = db('region')
                ->where(['pid'=>$pid])
                ->where('name','like',"%".$key."%")
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            // 计算下级区域数量
            foreach ($list['data'] as $k => &$v) {
                $v['son_number'] = db('region')->where(['pid'=>$v['id']])->count();
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'], 'rel'=>1];
        }
        // 当前所属区域
        $name = $this->_get_region_parent_data($pid);
        $this->assign('pid', $pid);
        $this->assign('name', $name);
        return $this->fetch('lists');
    }

    /**
     * 显示地区
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function show($id)
    {
        return $this->_s_h_do('show', $id);
    }

    /**
     * 隐藏地区
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function hide($id)
    {
        return $this->_s_h_do('hide', $id);
    }

    /**
     * 显示隐藏具体操作
     * @param $type
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    private function _s_h_do($type, $id)
    {
        if ($id>0) {
            $ids = $this->_get_region_son_data($id);
            if ($type == 'show') {
                db('region')->where('id','in', $ids)->update(['type'=>1]);
            }
            if ($type == 'hide') {
                db('region')->where('id','in', $ids)->update(['type'=>0]);
            }
            return $result = ['code'=>0,'msg'=>'设置成功!'];
        }
    }

    /**
     * 返回区域链以及子级
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _get_region_son_name($id)
    {
        $info = db('region')->find($id);
        array_push($this->_region_son_data, $info);
        $sub_info = db('region')->where(['pid'=>$id])->select();
        if ($sub_info) {
            foreach ($sub_info as $k => $v) {
                $this->_get_region_son_name($v['id']);
            }
        }
    }

    /**
     * 获取区域链保存的子级转换后结果
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _get_region_son_data($id)
    {
        $ids = [];
        $this->_get_region_son_name($id);
        foreach ($this->_region_son_data as $k => $v) {
            array_push($ids, $v['id']);
        }
        return $ids;
    }

    /**
     * 返回区域链以及父级
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _get_region_parent_name($id)
    {
        $info = db('region')->find($id);
        array_push($this->_region_parent_data, $info);
        if ($info['pid'] != 0) {
            $this->_get_region_parent_name($info['pid']);
        }
    }

    /**
     * 获取区域链保存的父级转换后结果
     * @param $id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _get_region_parent_data($id)
    {
        $name = '';
        $this->_get_region_parent_name($id);
        $region_data = array_reverse($this->_region_parent_data);
        foreach ($region_data as $k => $v) {
            if ($k > 0) {
                $name .= ' - '.$v['name'];
            } else {
                $name .= $v['name'];
            }
        }
        return $name;
    }

    /**
     * 增加地区
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add($pid=1)
    {
        if(request()->isPost())
        {
            $data = input('post.');
            DB::name('region')->insert($data);
            return $result = ['code'=>1,'msg'=>'区域添加成功!','url'=>url('lists')];
        } else {
            $parent = db('region')->find($pid);
            $this->assign('parent', $parent);

            return $this->fetch('edit');
        }
    }

    /**
     * 编辑地区
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        if ( request()->isPost() )
        {
            $datas = input('post.');
            if( DB::name('region')->update($datas) )
            {
                return json(['code' => 1, 'msg' => '保存成功!', 'url' => url('lists')]);
            } else {
                return json(['code' => 0, 'msg' =>'保存失败！']);
            }
        } else {
            $id = input('id');
            $info = DB::name('region')->where('id', $id)->find();
            $this->assign('info',$info);

            $parent = db('region')->find($info['pid']);
            $this->assign('parent', $parent);

            return $this->fetch('edit');
        }
    }

    /**
     * 删除地区
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        db('region')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    /**
     * 批量删除
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function all_del()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('region')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('lists');
        return $result;
    }
}