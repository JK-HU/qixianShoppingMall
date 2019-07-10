<?php
/**
 * 商品订单
 */
namespace app\common\model;
use think\Db;
use think\Model;

class ShopOrder extends Model
{
    protected $order = array(
        1 => array(1 => '待付款', 2 => '待服务', 3=> '待收货', 4 => '待评价', 5 => '已完成'),
        2 => array(1 => '待付款', 2 => '待发货', 3=> '待收货', 4 => '待评价', 5 => '已完成'),
    );

    /**
     * 获取商品销量
     * @param $goods_id
     * @return mixed
     */
    public static function get_sale_count($goods_id)
    {
        $sql = "select count(*) as c from ".config('database.prefix')."shop_order where find_in_set($goods_id, g_id)";
        $result = Db::query($sql);
        return $result[0]['c'];
    }

    /**
     * 查询订单信息列表
     * @param $status
     * @param $type_id
     * @param $start
     * @param $number
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_order_list($status, $type_id, $start, $number, $uid)
    {
        $where['o.uid'] = $uid;
        if (!empty($status)) {
            $where['o.status'] = $status;
        }
        if (empty($type_id)) {
            $order_list = $this->alias('o')
                ->field('o.*, a.address')
                ->join('user_address a', 'o.address_id = a.id', 'LEFT')
                ->where($where)
                ->limit($start, $number)->select();
        } else {
            $order_list = $this->alias('o')
                ->field('o.*, a.address')
                ->join('user_address a', 'o.address_id = a.id', 'LEFT')
                ->where($where)
                ->where('o.type_id', 'like', '%'.$type_id.'%')
                ->limit($start, $number)->select();
        }

        return $this->_status_link_handle($type_id, $order_list);
    }

    /**
     * 订单列表中显示状态和链接处理
     * @param $type_id
     * @param $order_list
     * @return mixed
     */
    private function _status_link_handle($type_id, $order_list)
    {
        foreach($order_list as $key => &$val)
        {
            if(empty($val['make_time'])){
                $val['make_time'] = '';
            }
            if(empty($val['address'])){
                $val['address'] = '';
            }
            $val['status_name'] = $this->order[$type_id][$val['status']];
            $val['action_url'] = '';
            switch ($val['status'])
            {
                case 1: // 待付款
                    $val['action_name'] = '去付款';
                    $val['action_url'] =  url('/home/order/choose', ['id'=>$val['id']]);
                    break;
                case 3: // 待收货
                    $val['action_name'] = '确认收货';
                    $val['action_url'] = url('user/serorder/confirmReceipt', ['id'=>$val['id']]);
                    break;
                case 4: // 待评价
                    $val['action_name'] = '去评价';
                    $val['action_url'] = url("/user/serorder/comment", ['id'=>$val['id']]);
                    break;
                default:
                    $val['action_name'] = $val['status_name'];
            }
        }

        return $order_list;
    }
}