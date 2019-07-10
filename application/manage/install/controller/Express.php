<?php
/**
 * 订单管理
 */
namespace app\admin\controller;
class Express extends Common
{
    /**
     * 物流列表
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('express')->where('name', 'like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 物流增加编辑
     * @param string $id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit($id='')
    {
        if(request()->isPost()){
            $data = input('post.');
            if(!empty($id)){
                db('express')->update($data);
            }else{
                $data['create_time'] = time();
                db('express')->insert($data);
            }
            return json(['code' => 1, 'msg' => '操作成功!', 'url' => url('index')]);

        }
        if(!empty($id)){
            $data = db('express')->where (array('id'=>$id) )->find();
            $this->assign('data', $data);
        }
        return $this->fetch();
    }

    /**
     * 物流删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function orderDel()
    {
        db('express')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    /**
     * 物流删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delall()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('express')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
}