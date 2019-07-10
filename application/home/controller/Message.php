<?php
/**
 * 留言管理
 */
namespace app\home\controller;

use think\Request;

class Message extends Common
{
    /**
     * @param string $type
     * @return mixed
     */
    public function index($type='person')
    {
        if (request()->isAjax()) {
            $data = request()->post();
            $insert_data = [
                'message_id' => $data['form_name_idt'],
                'name' => $data['form_name_ipt'],
                'tel' => $data['form_phone_ipt'],
                'address' => $data['form_area_ipt'],
                'ip' => getIp(),
                'content' => json_encode($data['form_content_ipt']),
                'title' => $data['form_title_ipt'],
                'addtime' => time()
            ];
            $result = db('message')->insert($insert_data);
            if ($result) {
                return ['code'=>1, 'text'=>'申请成功，请等待管理员审核联系'];
            } else {
                return ['code'=>0, 'text'=>'留言失败，请联系管理员'];
            }
        }
        $template = 'message_index_'.$type;
        return $this->fetch($template);
    }
}