<?php
/**
 * 订单
 */
namespace app\user\controller;
use app\common\model\ShopOrder;
use think\Db;

class Serorder extends Common
{

    /**
     * 服务订单列表
     * @param int $tid
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($tid=0)
    {

        $uid = session('user.id');
        if (request()->isPost())
        {
            $status = input('post.status');
            $type_id = input('post.type_id');
            $start = input('post.swipernum'); // 开始id
            $number = input('post.onloadnum'); // 获取数量

            // 查询订单信息
            $shop_order = new ShopOrder();
            $order_list = $shop_order->get_order_list($status, $type_id, $start, $number, $uid);

            return array('code'=>0,'data' => $order_list);
        }

        $type_id = input('get.type_id');
        $this->assign('type_id', $type_id);
        $this->assign('tid', $tid);

        return $this->fetch();
    }

    /**
     * 商城订单列表
     * @param int $tid
     * @return mixed
     */
    public function mall_list($tid=0)
    {
        $this->assign('tid', $tid);
        return $this->fetch();
    }

    /**
     * 确认收货
     * @param $id
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function confirmReceipt($id)
    {
        if ($id) {
            $re = db('shop_order')->where(['id'=>$id])->update(['status'=>4]);
            if ($re) {
                js_alert('确认收货成功', url('user/index/index'));
            } else {
                js_alert('确认收货失败', url('user/index/index'));
            }
        }
    }

    /**
     * 评论
     * @param $id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function comment($id=0)
    {
        if (request()->isPost()){
            $data = input('post.');
            $data['uid'] = session('user.id');
            $data['create_time'] = time();
            $res = DB::name('shop_order_evaluate')->insert($data);
            if($res){
                db('shop_order')->where(['id'=>$data['order_id']])->update(['status'=>5]);
                return array('code'=>0,'msg' => '评论成功！');
            }else{
                return array('code'=>1,'msg' => '评论失败！');
            }
        }
        if ($id) {
            $data = DB::name('shop_order')->alias('o')
                ->field('o.id,o.order_id, g.id as goods_id, g.title, g.introduce, g.pic')
                ->join('shop_goods g', 'o.g_id = g.id')->where(['o.id' => $id])->find();
            $this->assign('data', $data);
            return $this->fetch();
        }
    }
}