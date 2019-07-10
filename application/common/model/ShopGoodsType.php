<?php
/**
 * 商品分类
 */
namespace app\common\model;

use think\Model;

class ShopGoodsType extends Model
{
    /**
     * 返回下级子栏目信息
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_sub_type($id)
    {
        $list = $this->where(['pid'=>$id])->select();
        return $list;
    }

    /**
     * 获取下级子栏目id
     * @param $id
     * @param bool $deep
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_sub_type_ids($id, $deep=false)
    {
        if (!isset($ids)) $ids = [];
        $list_info = $this->get_sub_type($id);
        if ($list_info) {
            foreach ($list_info as $k => $v) {
                array_push($ids, $v['id']);
                if ($deep) {
                    $this->get_sub_type_ids($v['id'], $deep);
                }
            }
        }
        return $ids;
    }
}