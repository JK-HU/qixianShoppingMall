<?php
/**
 * 会员管理
 */
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
class Users extends Common
{
    /**
     * 会员列表
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(request()->isPost())
        {
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('users')->alias('u')
                ->join(config('database.prefix').'user_level ul','u.level = ul.level_id','left')
                ->join(config('database.prefix').'user_vip uv','u.id = uv.uid','left')
                ->field('u.*,ul.level_name,uv.expires_time')
                ->where('u.email|u.mobile|u.username','like',"%".$key."%")
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['reg_time'] = date('Y-m-d H:s',$v['reg_time']);
                (!empty($v['expires_time'])) && $list['data'][$k]['expires_time'] = date('Y-m-d',$v['expires_time']);
            }
            // var_export($list);die;
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 设置会员状态
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function usersState()
    {
        $id=input('post.id');
        $is_lock=input('post.is_lock');
        if(db('users')->where('id='.$id)->update(['is_lock'=>$is_lock])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function edit($id='')
    {
        if(request()->isPost()){
            $user = db('users');
            $data = input('post.');
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = md5($data['password']);
            }
            if ($user->update($data)!==false) {
                $result['msg'] = '会员修改成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            } else {
                $result['msg'] = '会员修改失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $province = db('Region')->where ( array('pid'=>1) )->select ();
            $user_level=db('user_level')->order('sort')->select();
            $info = UsersModel::get($id);
            $city = db('Region')->where ( array('pid'=>$info['province']) )->find();
            $district = db('Region')->where ( array('pid'=>$info['city']) )->find();

            $this->assign('info', $info);
            $this->assign('title',lang('edit').lang('user'));
            $this->assign('user_level', $user_level);
            $this->assign('province', $province);
            $this->assign('city', $city['name']);
            $this->assign('district', $district['name']);
            return $this->fetch();
        }
    }

    /**
     * 获取地区
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getRegion()
    {
        $Region=db("region");
        $pid = input("pid");
        $map['pid']=$pid;
        $list=$Region->where($map)->select();
        return $list;
    }

    public function usersDel()
    {
        db('users')->delete(['id'=>input('id')]);
        db('oauth')->delete(['uid'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    public function delall()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('users')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    /***********************************会员组***********************************/
    public function userGroup()
    {
        if(request()->isPost()){
            $userLevel=db('user_level');
            $list=$userLevel->order('sort')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }

    public function groupAdd()
    {
        if(request()->isPost()){
            $data = input('post.');
            db('user_level')->insert($data);
            $result['msg'] = '会员组添加成功!';
            $result['url'] = url('userGroup');
            $result['code'] = 1;
            return $result;
        }else{
            $this->assign('title',lang('add')."会员组");
            $this->assign('info','null');
            return $this->fetch('groupForm');
        }
    }
    public function groupEdit()
    {
        if(request()->isPost()) {
            $data = input('post.');
            db('user_level')->update($data);
            $result['msg'] = '会员组修改成功!';
            $result['url'] = url('userGroup');
            $result['code'] = 1;
            return $result;
        }else{
            $map['level_id'] = input('param.level_id');
            $info = db('user_level')->where($map)->find();
            $this->assign('title',lang('edit')."会员组");
            $this->assign('info',json_encode($info,true));
            return $this->fetch('groupForm');
        }
    }

    public function groupDel()
    {
        $level_id=input('level_id');
        if (empty($level_id)){
            return ['code'=>0,'msg'=>'会员组ID不存在！'];
        }
        db('user_level')->where(array('level_id'=>$level_id))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    public function groupOrder()
    {
        $userLevel=db('user_level');
        $data = input('post.');
        $userLevel->update($data);
        $result['msg'] = '排序更新成功!';
        $result['url'] = url('userGroup');
        $result['code'] = 1;
        return $result;
    }


    /**
     * VIP充值记录
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function vipList()
    {
        if(request()->isPost())
        {
            $key = input('post.key');
            $status = input('post.status');
            $condition = [];
            if ($status > 0) {
                $condition = ['uv.status' => $status];
            }
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('user_vip_order')->alias('uv')
                ->join(config('database.prefix').'users u','u.id = uv.uid','left')
                ->field('uv.*,u.username, u.mobile')
                ->where('u.mobile|u.username','like',"%".$key."%")
                ->where($condition)
                ->order('uv.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:s',$v['reg_time']);
                $list['data'][$k]['status'] = ($v['status'] == '1') ? '未支付' : '已支付';
            }
            // var_export($list);die;
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

}