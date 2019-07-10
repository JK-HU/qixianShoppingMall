<?php
/**
 * 商品管理
 */
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
use think\Db;
use clt\Leftnav;
class Goods extends Common
{
    /**
     * 分类列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goods_type()
    {

        if(request()->isPost()){
            $arr = DB::name('shop_goods_type')->order('id asc')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$arr,'is'=>true,'tip'=>'操作成功'];
        }
        return view();
    }

    /**
     * 增加/编辑分类
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit_goods_type(){
        if(request()->isPost()) {
            $data = input('post.');
            if(!empty($data['id'])){
                unset($data['file']);
                $res = DB::name('shop_goods_type')->update($data);
            }else{
                $data = input('post.');
                $data['create_time'] = time();
                unset($data['file']);
                $res = DB::name('shop_goods_type')->insert($data);
            }
            if($res) {
                return json(['code' => 1, 'msg' => '操作成功!', 'url' => url('goods_type')]);
            } else {
                return json(['code' => 0, 'msg' =>'操作失败！']);
            }
        }else{
            $id = input('id');
            $data = DB::name('shop_goods_type')->order('sort desc')->select();
            $goods_type = DB::name('shop_goods_type')->where('id', $id)->find();
            $nav = new Leftnav();
            $arr = $nav->menu($data);
            $this->assign('arr',$arr);
            $this->assign('goods_type',$goods_type);
            return $this->fetch();
        }
    }

    /**
     * 删除分类
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function type_del(){
        $id = input('param.id');
        //查看此分类下是否还有子分类，如果有不运行删除
        $count = DB::name('shop_goods_type')->where('pid', $id)->count();
        if($count >= 1){
            return $result = ['code'=>-1,'msg'=>'删除失败，请先删除分类下的子分类！'];
        }
        DB::name('shop_goods_type')->where('id', input('param.id'))->delete();
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    /**
     * 商品管理
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function goods_lists()
    {
        $list_type = db('shop_goods_type')->order('id desc')->select();
        $this->assign('list_type', $list_type);

        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $type_id = input('post.type_id');
            $where = [];
            if(!empty($type_id)){
                $where['g.gid'] = $type_id;
            }
            $list=db('shop_goods')->alias('g')
                ->field('g.*,gt.title as g_name')
                ->join('shop_goods_type gt', 'g.gid = gt.id', 'LEFT')
                ->where('g.title','like',"%".$key."%")
                ->where($where)
                ->order('g.sort desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();

            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 商品增加编辑
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit_goods(){
        if(request()->isPost()) {
            $data = input('post.');
            if($data['gg_open'] == 1 && empty($data['datas'])){
                return $result = ['code'=>-1,'msg'=>'请增加规格!'];
            }
            if(!empty($data['id'])){
                $guige = $data['data'];
                $guige_list = $data['datas'];
                unset($data['file']);
                unset($data['data']);
                unset($data['datas']);

                if(empty($data['gid'])){
                    return $result = ['code'=>-1,'msg'=>'分类不能为空!'];
                }
                if(empty($data['gg_open'])){
                    $data['gg_open'] = 0;
                }

                $res = DB::name('shop_goods')->update($data);
                DB::name('shop_goods_norms')->where('gid',$data['id'])->delete();
                DB::name('shop_goods_norms_type')->where('gid',$data['id'])->delete();
                DB::name('shop_goods_nt')->where('gid',$data['id'])->delete();
                if($data['gg_open'] == 1 && !empty($guige) && !empty($guige_list)){
                    foreach($guige as $key => $val){
                        $nid = DB::name('shop_goods_norms')->insertGetId(['gid' => $data['id'], 'title' => $val['title'],'create_time' => time()]);
                        foreach($val['type'] as $k => $v){
                            $tid = DB::name('shop_goods_norms_type')->insertGetId(['nid' => $nid,'gid' => $data['id'], 'title' => $v['title'],'create_time' => time()]);
                        }
                    }
                    foreach($guige_list as $key => $val){
                        $nt = '';
                        $data_list['num'] = $val['num'];
                        $data_list['money'] = $val['money'];
                        $data_list['member_price'] = $val['member_price'];
                        $data_list['gid'] = $data['id'];
                        foreach($val['name'] as $k => $v){
                            $name = explode('-', $v);
                            $xnid = DB::name('shop_goods_norms')->where(['gid' => $data['id'], 'title' => $name[0]])->find();
                            $xtid = DB::name('shop_goods_norms_type')->where(['gid' => $data['id'], 'title' => $name[1],'nid' => $xnid['id']])->find();
                            $nt .= $xtid['id'].'-';
                            $data_list['ntid'] = rtrim($nt, '-');
                        }
                        DB::name('shop_goods_nt')->insert($data_list);
                    }
                }
                return json(['code' => 1, 'msg' => '操作成功!', 'url' => url('goods_lists')]);
            }else{
                $guige = $data['data'];
                $guige_list = $data['datas'];
                unset($data['file']);
                unset($data['data']);
                unset($data['datas']);
                if(empty($data['gid'])){
                    return $result = ['code'=>-1,'msg'=>'分类不能为空!'];
                }
                $data['create_time'] = time();
                $res = DB::name('shop_goods')->insertGetId($data);
                if($data['gg_open'] == 1 && !empty($guige) && !empty($guige_list)){
                    foreach($guige as $key => $val){
                        $nid = DB::name('shop_goods_norms')->insertGetId(['gid' => $res, 'title' => $val['title'],'create_time' => time()]);
                        foreach($val['type'] as $k => $v){
                            $tid = DB::name('shop_goods_norms_type')->insertGetId(['nid' => $nid,'gid' => $res, 'title' => $v['title'],'create_time' => time()]);
                        }
                    }
                    foreach($guige_list as $key => $val){

                        $nt = '';
                        $data_list['num'] = $val['num'];
                        $data_list['money'] = $val['money'];
                        $data_list['member_price'] = $val['member_price'];
                        $data_list['gid'] = $res;
                        foreach($val['name'] as $k => $v){
                            $name = explode('-', $v);
                            $xnid = DB::name('shop_goods_norms')->where(['gid' => $res, 'title' => $name[0]])->find();
                            $xtid = DB::name('shop_goods_norms_type')->where(['gid' => $res, 'title' => $name[1],'nid' => $xnid['id']])->find();
                            $nt .= $xtid['id'].'-';
                            $data_list['ntid'] = rtrim($nt, '-');
                        }
                        DB::name('shop_goods_nt')->insert($data_list);
                    }
                }
                if($res) {
                    return json(['code' => 1, 'msg' => '操作成功!', 'url' => url('goods_lists')]);
                } else {
                    return json(['code' => 0, 'msg' =>'操作失败！']);
                }
            }
        }else{
            $id = input('id');
            $goods = DB::name('shop_goods')->where('id', $id)->find();
            $goods['norms'] = DB::name('shop_goods_norms')->where('gid', $goods['id'])->select();
            foreach($goods['norms'] as $key => &$val){
                $norms = DB::name('shop_goods_norms_type')->where('nid', $val['id'])->select();
                $val['type'] = $norms;
            }
            $count = count($goods['norms']);
            $nt_arr = DB::name('shop_goods_nt')->where('gid', $goods['id'])->order('id ace')->select();
            foreach($nt_arr as $key => &$val){
                $val['ntid'] = explode('-', $val['ntid']);
                $one = [];
                foreach($val['ntid'] as $k => &$v){
                    $name = DB::name('shop_goods_norms_type')->where('id', $v)->find();
                    $v = $name['title'];
                    $title = DB::name('shop_goods_norms')->where(['id' => $name['nid']])->find();
                    $one[$k]['title'] = $name['title'];
                    $one[$k]['g_name'] = $title['title'];
                }
                $val['ntid'] = $one;
            }
            $nav = new Leftnav();
            $arr = DB::name('shop_goods_type')->order('sort asc')->select();
            $arr = $nav->menu($arr);
            $this->assign('goods_type',$arr);//权限列表
            $this->assign('nt_arr',$nt_arr);
            $this->assign('count',$count);
            $this->assign('goods',$goods);
            return $this->fetch();
        }
    }

    /**
     * 商品删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function goods_del()
    {
        db('shop_goods')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    /**
     * 批量删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function goods_all_del()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('shop_goods')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('goods_lists');
        return $result;
    }
}