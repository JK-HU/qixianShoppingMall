<?php
/**
 * 商品
 */
namespace app\home\controller;

use app\common\model\ShopGoods;
use app\common\model\ShopOrder;

class Goods extends Common
{

    /**
     * 甲醛治理频道页
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function channel1($id)
    {
        $banner_list = $this->getBannerList(2);
        $type = db('shop_goods_type')->where(['pid'=>$id])->select();

        $this->assign('type_list', $type);
        $this->assign('banner_list', $banner_list);
        return $this->fetch('goods_channel1');
    }

    /**
     * 智能家居频道页
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function channel2($id)
    {
        $banner_list = $this->getBannerList(4);
        $banner_list2 = $this->getBannerList(5, 1);
        $banner_list3 = $this->getBannerList(6, 2);
        $banner_list4 = $this->getBannerList(7, 1);

        $shop_goods = new ShopGoods();
        $list = $shop_goods->get_list($id, 3);
        $recommended_list = $shop_goods->get_recommended_list($id, 2);

        $type = db('shop_goods_type')->where(['pid'=>$id])->select();

        $this->assign('type_list', $type);
        $this->assign('banner_list', $banner_list);
        $this->assign('banner_list2', $banner_list2);
        $this->assign('banner_list3', $banner_list3);
        $this->assign('banner_list4', $banner_list4);
        $this->assign('list', $list);
        $this->assign('recommended_list', $recommended_list);

        return $this->fetch('goods_channel2');
    }

    /**
     * 家政保洁、家电清洗、汽车休养列表页
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list1($id)
    {
        return $this->lists($id, 'goods_list1');
    }

    /**
     * 旧房翻新
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list2($id)
    {
        return $this->lists($id, 'goods_list2');
    }

    /**
     * 甲醛治理
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list3($id)
    {
        return $this->lists($id,'goods_list3');
    }

    /**
     * 辅材集市
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list4($id)
    {
        $banner_list = $this->getBannerList(3, 1);

        $this->assign('banner_list', $banner_list);
        return $this->lists($id,'goods_list4');
    }

    /**
     * 智能家居
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list5($id)
    {
        return $this->lists($id,'goods_list5');
    }

    /**
     * 列表公用部分
     * @param $id
     * @param $view
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function lists($id, $view)
    {
        $type_info = db('shop_goods_type')->find($id);
        $type = db('shop_goods_type')->where(['pid'=>$id])->select();

        $this->assign('type_info', $type_info);
        $this->assign('type_list', $type);
        $this->assign('type_id', $id);
        return $this->fetch($view);
    }

    /**
     * 获取商品分类列表信息
     */
    public function get_list()
    {
        if (request()->isAjax()) {
            $type_id = !empty(input('post.id')) ? input('post.id') : input('post.tabId'); // 栏目id
            $start = input('post.swipernum'); // 开始id
            $number = input('post.onloadnum'); // 获取数量
            $order = input('post.attr_cur'); // 排序方式
            $keyword = input('post.keyword'); // 搜索关键词

            $shop_goods = new ShopGoods();
            $list = $shop_goods->get_type_list($type_id, $start, $number, $order, $keyword);

            return json($list);
        }
    }

    /**
     * 获取商品规格父子分类
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNorms()
    {
        if (request()->isAjax()) {
            $id = input('post.id');
            $parent_list = db('shop_goods_norms')->where(['gid'=>$id])->order('id ASC')->select();
            $son_list = db('shop_goods_norms_type')->where(['gid'=>$id])->select();
            // 按父id排序重组数组
            foreach ($parent_list as $k => &$v) {
                $son_temp = [];
                foreach ($son_list as $ks => $vs) {
                    if ($v['id'] == $vs['nid']) {
                        array_push($son_temp, $vs);
                    }
                }
                $v['son'] = $son_temp;
            }
            return json($parent_list);
        }
    }

    /**
     * 获取用户选定规格的内容，包括价格、库存等
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNormsContent()
    {
        if (request()->isAjax()) {
            $goods_id = input('post.id');
            $ids = input('post.ids');
            sort($ids);
            $info = db('shop_goods_nt')->where(['gid'=>$goods_id])->select();
            $data = [];
            foreach ($info as $k => $v) {
                $info_ids = explode('-', $v['ntid']);
                sort($info_ids);
                if ($ids == $info_ids) {
                    $data = $v;
                }
            }
            $data['now_price'] = the_function_price($data);
            return json($data);
        }
    }

    /**
     * 家政保洁、家电清洗、汽车休养列表页
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function content1($id)
    {
        return $this->contents('goods_content1', $id);
    }

    /**
     * 旧房翻新
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function content2($id)
    {
        return $this->contents('goods_content2', $id);
    }

    /**
     * 智能家居 - 辅材集市
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function content3($id)
    {
        return $this->contents('goods_content3', $id);
    }

    /**
     * 内容公用部分
     * @param $view
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function contents($view, $id)
    {
        $info = db('shop_goods')->find($id);
        // 参考费用
        $reference_list = [];
        if (!empty($info['reference_cost'])) {
            $reference = explode('|', $info['reference_cost']);
            foreach ($reference as $k => $v) {
                if (strpos($v, '-')) {
                    $in = explode('-', $v);
                    array_push($reference_list, [
                        'name' => $in[0],
                        'price' => $in[1]
                    ]);
                }
            }
        }
        // 真正的价格
        $info['money'] = the_function_price($info);
        $info['sale_count'] = ShopOrder::get_sale_count($info['id']);
        $this->assign('info', $info);
        $this->assign('reference_list', $reference_list);

        return $this->fetch($view);
    }
}