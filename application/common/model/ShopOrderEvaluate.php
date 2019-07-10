<?php
/**
 * 订单评价
 */
namespace app\common\model;

use think\Model;

class ShopOrderEvaluate extends Model
{
    /**
     * 获取数据
     * @param int $id
     * @param $pageSize
     * @param $page
     * @param $key
     * @return array
     * @throws \think\exception\DbException
     */
    public function get_list($id=0, $pageSize, $page, $key)
    {
        $condition = [];
        if ($id>0) {
            $condition['order_id'] = $id;
        }
        // 数据
        $list = $this->alias('e')
            ->join('shop_order o', 'e.order_id=o.id', 'LEFT')
            ->join('users u', 'e.uid=u.id', 'LEFT')
            ->where($condition)
            ->where('e.content','like',"%".$key."%")
            ->order('id desc')
            ->field('e.*,u.mobile')
            ->paginate(array('list_rows'=>$pageSize, 'page'=>$page))
            ->toArray();
        // 日期
        foreach ($list['data'] as $k => &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }

        return $list;
    }
}