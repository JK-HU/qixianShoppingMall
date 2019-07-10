<?php
namespace app\admin\controller;
class Message extends Common
{
    public function index(){
        if(request()->isPost()) {
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $condition = [];
            if (!empty(input('post.title'))) {
                $condition['title'] = input('post.title');
            }
            $list = db('message')
                ->where('name|tel|content', 'like', "%" . $key . "%")
                ->order('addtime desc')
                ->where($condition)
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            //var_dump(input('post.title'));die;
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
                $images_string = '';
                if (!empty($v['content'])) {
                    $images = json_decode($v['content'], true);
                    if ($images) {
                        foreach ($images as $ik => $iv) {
                            $images_string .= '<img onmouseover="layer.tips(\'<img style=max-width:500px src='.$iv.'>\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();" height="40" src="'.$iv.'" />&nbsp;&nbsp;';
                        }
                    }
                }
                $list['data'][$k]['content'] = $images_string;
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    //删除留言
    public function del(){
        $map['message_id']=input('message_id');
        db('message')->where($map)->delete();
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    public function delall(){
        $map[] =array('message_id','IN',input('param.ids/a'));
        db('message')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }
}