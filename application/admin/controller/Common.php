<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\facade\Request;

class Common extends Controller
{
    protected $role;
    protected $system;
    protected $nav;
    protected $menudata;
    protected $cache_model;
    protected $adminRules;
    protected $HrefId;

    protected function initialize()
    {
        //判断管理员是否登录
        if (!session('aid')) {
            $this->redirect('admin/login/index');
        }
        define('MODULE_NAME',strtolower(request()->controller()));
        define('ACTION_NAME',strtolower(request()->action()));
        //权限管理
        //当前操作权限ID
        if(session('aid')!=1){
            $this->HrefId = db('auth_rule')->where('href',MODULE_NAME.'/'.ACTION_NAME)->value('id');
            //当前管理员权限
            $map['a.admin_id'] = session('aid');
            $rules=Db::table(config('database.prefix').'admin')->alias('a')
                ->join(config('database.prefix').'auth_group ag','a.group_id = ag.group_id','left')
                ->where($map)
                ->value('ag.rules');
            $this->adminRules = explode(',',$rules);
            if($this->HrefId){
                if(!in_array($this->HrefId,$this->adminRules)){
                    $this->error('您无此操作权限');
                }
            }
        }
        $this->cache_model=array('AuthRule','Posid','System','Config');
        foreach($this->cache_model as $r){
            if(!cache($r)){
                savecache($r);
            }
        }
        $this->system = cache('System');
        $this->rule = cache('AuthRule');
    }

    /**
     * 空操作
     */
    public function _empty()
    {
        return $this->error('空操作，返回上次访问页面中...');
    }

    /**
     * 查询单个数据
     * @param $table
     * @param $id
     * @param $assign_name
     * @param bool $field
     * @param bool $is_json
     * @param bool $is_return
     * @param string $id_name
     * @return array|\PDOStatement|string|\think\Model|\think\response\Json|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function select_info($table, $id, $assign_name, $field=false, $is_json=false, $is_return=false, $id_name='id')
    {
        if (!$id) {
            $id = input('id');
        }
        $map = [$id_name => $id];
        if ($field) {
            $info = db($table)->where($map)->field($field)->find();
        } else {
            $info = db($table)->where($map)->find($id);
        }
        if ($assign_name) {
            if ($is_json) {
                $this->assign($assign_name, json_encode($info,true));
            } else {
                $this->assign($assign_name, $info);
            }
        }
        if ($is_return) {
            return $info;
        }
    }

    /**
     * 查询数据表列表
     * @param $table
     * @param $assign_name
     * @param bool $field
     * @param bool $is_json
     * @param bool $is_return
     * @return array|\PDOStatement|string|\think\Collection|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function select_list($table, $assign_name, $field=false, $is_json=false, $is_return=false)
    {
        if ($field) {
            $list = db($table)->field($field)->select();
        } else {
            $list = db($table)->select();
        }
        if ($assign_name) {
            if ($is_json) {
                $this->assign($assign_name, json_encode($list,true));
            } else {
                $this->assign($assign_name, $list);
            }
        }
        if ($is_return) {
            return $list;
        }
    }

    /**
     * 直接添加内容
     * @param $table
     * @param array $data
     * @param string $text
     * @param string $create_time
     * @param bool $url
     * @return array
     */
    protected function insert_content($table, $data=[], $text='操作', $create_time='create_time', $url=false)
    {
        if (!$data) {
            $data = Request::except('file');
        }
        if (!$url) {
            $url = url('index');
        }
        $data[$create_time] = time();
        if( db($table)->insert($data) !== false ) {
            return ['code'=>1,'msg'=> $text.'成功!','url'=>$url];
        } else {
            return ['code'=>0,'msg'=> $text.'失败!'];
        }
    }

    /**
     * 直接修改内容
     * @param $table
     * @param array $data
     * @param string $text
     * @param bool $update_time
     * @param bool $url
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function update_content($table, $data=[], $text='操作', $update_time=false, $url=false)
    {
        if (!$data) {
            $data = Request::except('file');
        }
        if (!$url) {
            $url = url('index');
        }
        if ($update_time) {
            $data[$update_time] = time();
        }
        if ( db($table)->update($data) !== false ) {
            return ['code'=>1,'msg'=> $text.'成功!','url'=>$url];
        } else {
            return ['code'=>0,'msg'=> $text.'失败!'];
        }
    }

    /**
     * 单个删除
     * @param $table
     * @param $id
     * @param string $id_name
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function delete($table, $id, $id_name='id')
    {
        $map = [$id_name => $id];
        db($table)->where($map)->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    /**
     * 批量删除
     * @param $table
     * @param $ids
     * @param string $id_name
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function delete_all($table, $ids, $id_name='id')
    {
        $map[] = array($id_name, 'in', $ids);
        db($table)->where($map)->delete();
        return ['code'=>1,'msg'=>'删除成功!', 'url'=>url('index')];
    }

    /**
     * 获取地区
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRegion()
    {
        $Region = db("region");
        $pid = input("pid");
        $map['pid']=$pid;
        $list = $Region->where($map)->select();
        return $list;
    }
}
