<?php
/**
 * 文章
 */
namespace app\home\controller;

use app\common\model\ArticleFabulous;
use app\common\model\Article as ArticleModel;

class Article extends Common
{
    public function initialize()
    {
        parent::initialize();
        if (!session('user.id'))
        {
            $this->redirect('user/login/index');
        }
    }

    /**
     * 文章列表页面
     * @param $id
     * @return mixed
     */
    public function index($id)
    {
        if ($id) {
            $this->assign('id', $id);
            return $this->fetch();
        }
    }

    public function getList()
    {
        $cid = input('post.id'); // 栏目id
        $start = input('post.start'); // 开始id
        $number = input('post.loadsize'); // 获取数量

        $a = new ArticleModel();
        $list = $a->alias('a')
            ->join('article_fabulous aff', 'a.id=aff.aid', 'LEFT')
            ->join('article_fabulous af', 'a.id=af.aid and af.uid='.session('user.id'), 'LEFT')
            ->where(['a.cid'=>$cid])
            ->field('a.*, af.id as fid, count(aff.id) as fabulous_number')
            ->group('aff.aid')
            ->limit($start, $number)
            ->select();
        foreach ($list as $k => &$v) {
            $v['url'] = (!empty($v['url'])) ? $v['url'] : url('content', ['id'=>$v['id']]);
        }

        return json($list);
    }

    public function content($id)
    {
        if ($id) {
            $info = ArticleModel::find($id);
            $info->setInc('view_number', 1);
            $this->assign('info', $info);
            return $this->fetch();
        }
    }

    /**
     * 点赞
     * @param $id
     * @throws \think\Exception
     */
    public function clickGood($id)
    {
        if ($id) {
            $result = ArticleFabulous::where(['aid'=>$id, 'uid'=>session('user.id')])->find();
            if ($result) {
                $result->delete();
            } else {
                $data = [
                    'uid' => session('user.id'),
                    'aid' => $id,
                    'ip'  => getIp(),
                    'create_time' => time()
                ];
                db('article_fabulous')->insert($data);
            }
        }
    }
}