<?php
/**
 * 商品
 */
namespace app\common\model;

use think\Db;
use think\Model;

class ShopGoods extends Model
{
    private $_goods_table;
    private $_order_table;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->_goods_table = config('database.prefix').'shop_goods';
        $this->_order_table = config('database.prefix').'shop_order';
    }

    /**
     * 获取当前及下级分类内容
     * @param $type_id
     * @param $number
     * @param int $offset
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_list($type_id, $number, $offset=0)
    {
        $shop_goods_type = new ShopGoodsType();
        $sub_ids = $shop_goods_type->get_sub_type_ids($type_id);
        $type_ids = array_merge([$type_id], $sub_ids);
        $list = $this->where('gid', 'in', $type_ids)
            ->limit($offset, $number)
            ->order('sort desc')
            ->select();
        foreach ($list as $k => &$v) {
            $v['money'] = the_function_price($v);
        }
        return $list;
    }

    /**
     * 获取当前及下级分类被推荐内容
     * @param $type_id
     * @param $number
     * @param int $offset
     * @param bool $deep
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_recommended_list($type_id, $number, $offset=0, $deep=false)
    {
        $shop_goods_type = new ShopGoodsType();
        $sub_ids = $shop_goods_type->get_sub_type_ids($type_id, $deep);
        $type_ids = array_merge([$type_id], $sub_ids);
        $list = $this->where(['is_recommended'=>1])
            ->where('gid', 'in', $type_ids)
            ->limit($offset, $number)
            ->order('id desc')
            ->select();
        foreach ($list as $k => &$v) {
            $v['money'] = the_function_price($v);
        }
        return $list;
    }

    /**
     * 按要求查询特定分类下商品
     * @param $type_id
     * @param $start
     * @param $number
     * @param $order
     * @param string $keywords
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_type_list($type_id, $start, $number, $order, $keywords='')
    {
        $list = [];
        // 默认排序
        if (empty($order) || $order == 'recommend') {
            $list = $this
                ->where(['gid'=>$type_id])
                ->where('title','like',"%".$keywords."%")
                ->limit($start, $number)
                ->select();
        }
        // 销量排序
        if ($order == 'sales') {
            $list = $this
                ->where(['gid'=>$type_id])
                ->where('title','like',"%".$keywords."%")
                ->limit($start, $number)
                ->order('sort desc')
                ->select();
            $list = $list->toArray();
            // 统计销量
            foreach ($list as $k => &$v) {
                $info = Db::query("select count(*) as h from ".$this->_order_table." as o where find_in_set($type_id, o.g_id)");
                $v['order_num'] = $info[0]['h'];
            }
            // 大->小排序
            for($i=0;$i<count($list);$i++) {
                for($j=count($list);$j>$i;$j--) {
                    if ($list[$i]['order_num'] < $list[$j]['order_num']) {
                        $temp = $list[$j]['order_num'];
                        $list[$j]['order_num'] = $list[$i]['order_num'];
                        $list[$i]['order_num'] = $temp;
                    }
                }
            }
        }
        // 好评排序
        if ($order == 'praise') {
            $list = $this
                ->alias('g')
                ->join('shop_order_evaluate e', 'g.id=e.goods_id', 'LEFT')
                ->where(['gid'=>$type_id])
                ->where('g.title','like',"%".$keywords."%")
                ->order('e.star_class DESC')
                ->field('g.*, e.star_class')
                ->limit($start, $number)
                ->select();
        }
        // 价格低排序
        if ($order == 'price') {
            $list = $this
                ->where(['gid'=>$type_id])
                ->where('title','like',"%".$keywords."%")
                ->limit($start, $number)
                ->order('money ASC')
                ->select();
        }
        // 价格高排序
        if ($order == 'price_up') {
            $list = $this
                ->where(['gid'=>$type_id])
                ->where('title','like',"%".$keywords."%")
                ->limit($start, $number)
                ->order('money DESC')
                ->select();
        }

        // 根据会员情况，处理显示价格
        foreach ($list as $k => &$v) {
            $v['now_money'] = the_function_price($v);
        }

        return $list;
    }
}