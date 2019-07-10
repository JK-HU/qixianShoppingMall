<?php
namespace app\home\controller;
use app\common\model\ShopGoods;
use think\Db;
use clt\Lunar;
use think\facade\Env;
use think\Request;

class Index extends Common
{
    protected function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        $banner = $this->getBannerList(1);
        $this->assign('banner', $banner);

        $banner8 = $this->getBannerList(8, 1);
        $this->assign('banner8', $banner8);

        $banner9 = $this->getBannerList(9, 4);
        $this->assign('banner9', $banner9);

        $banner10 = $this->getBannerList(10, 1);
        $this->assign('banner10', $banner10);

        $banner11 = $this->getBannerList(11, 3);
        $this->assign('banner11', $banner11);

        $banner12 = $this->getBannerList(12, 1);
        $this->assign('banner12', $banner12);

        $banner13 = $this->getBannerList(13, 3);
        $this->assign('banner13', $banner13);

        $banner14 = $this->getBannerList(14, 1);
        $this->assign('banner14', $banner14);

        $banner15 = $this->getBannerList(15, 4);
        $this->assign('banner15', $banner15);

        // 底部推荐商品-所有分类
        $shop_goods = new ShopGoods();
        // $recommended_list = $shop_goods->get_recommended_list(11, 4, 0, true);
        $recommended_list = db('shop_goods')->where(['is_recommended'=>1])->order('sort desc')->limit(4)->select();
       //var_export($recommended_list);die;
        $this->assign('recommended_list', $recommended_list);

        return $this->fetch();
    }

    /**
     * 地区页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function region()
    {
        $list = db('region')->where(['pid'=>1,'type'=>1])->select();
        foreach ($list as $k => &$v) {
            $v['sub'] = db('region')->where(['pid'=>$v['id'],'type'=>1])->select();
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function senmsg(){
        $data = input('post.');
        $data['addtime'] = time();
        $data['ip'] = getIp();
        $data['content'] = safe_html($data['content']);
        db('message')->insert($data);
        $result['status'] = 1;
        return $result;
    }
    public function down($id=''){
        $map['id'] = $id;
        $files = Db::name('download')->where($map)->find();
        return download(Env::get('root_path').'public'.$files['files'], $files['title']);
    }
}