<?php
/**
 * 文章管理
 */
namespace app\admin\controller;
use think\Db;
use think\facade\Request;

class Article extends Common
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 文章列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(Request::isAjax()) {
            $key = input('post.key');
            $this->assign('testkey', $key);
            $type_id = input('post.type_id');
            $where = [];
            if (!empty($type_id)) {
                $where['a.type_id'] = $type_id;
            }
            $page = input('page') ? input('page') : 1;
            $pageSize =input('limit') ? input('limit') : config('pageSize');
            $list = Db::table(config('database.prefix') . 'article')->alias('a')
                ->join(config('database.prefix') . 'article_category ac', 'a.cid = ac.id', 'left')
                ->field('a.*,ac.title as category_name')
                ->where('a.title', 'like', "%" . $key . "%")
                ->where($where)
                ->order('a.sort')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }

            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }

        // 文章分类
        $this->select_list('article_category', 'list_type');

        return $this->fetch();
    }

    /**
     * 添加文章
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        if(Request::isAjax()) {
            //构建数组
            return $this->insert_content('article', [], '文章添加');
        } else {
            $this->select_list('article_category', 'list_type');

            $this->assign('title','添加文章');
            return $this->fetch('form');
        }
    }

    /**
     * 修改文章
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        if(Request::isAjax()) {
            return $this->update_content('article', [], '文章修改');
        } else {
            $this->select_list('article_category', 'list_type',false);
            $this->select_info('article', input('id'), 'info',false);

            $this->assign('title','文章编辑');
            return $this->fetch('form');
        }
    }

    /**
     * 设置文章状态
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editState()
    {
        return $this->update_content('article');
    }

    /**
     * 设置排序
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function articleOrder()
    {
        return $this->update_content('article');
    }

    /**
     * 删除文章
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        return $this->delete('article', input('id'));
    }

    /**
     * 批量删除文章
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delall()
    {
        return $this->delete_all('article', input('param.ids/a'));
    }

    /*************************** 文章分类 *****************************/

    /**
     * 文章分类列表
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function type()
    {
        if( Request::isAjax() ) {
            $key = input('key');
            $this->assign('testkey', $key);
            $list = db('article_category')->where('title', 'like', "%" . $key . "%")->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }


    /**
     * 添加文章分类
     * @return mixed
     */
    public function addType()
    {
        if(Request::isAjax()) {
            return $this->insert_content('article_category', [], '文章分类添加', 'create_time', url('type'));
        }else{
            $this->assign('title','文章分类添加');
            return $this->fetch('typeForm');
        }
    }

    /**
     * 修改文章分类
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function editType()
    {
        if(Request::isAjax()) {
            return $this->update_content('article_category', [], '文章分类修改', false, url('type'));
        }else{
            $this->select_info('article_category', input('param.id'), 'info');
            $this->assign('title','文章分类修改');
            return $this->fetch('typeForm');
        }
    }

    /**
     * 删除文章分类
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delType()
    {
        $this->delete('article', input('param.id'), 'cid');
        return $this->delete('article_category', input('param.id'));
    }
}