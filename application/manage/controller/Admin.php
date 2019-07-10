<?php
/**
 * 后台管理
 */
namespace app\manage\controller;
use think\Controller;
use think\Db;
use think\facade\Env;

class Admin extends Controller
{
    private $_controller_path; // 控制器目录
    private $_view_path; // 视图目录
    private $_public_path; // public目录
    private $_install_content_txt_path; // 安装内容片段目录
    private $_install_content_controller_path; // 安装内容控制器目录
    private $_install_content_view_path; // 安装内容视图目录
    private $_install_content_sql_path; // 安装内容视图目录
    private $_config; // 总配置项

    protected function initialize()
    {
        parent::initialize();
        $this->_view_path = Env::get('app_path').'admin/view/';
        $this->_controller_path = Env::get('app_path').'admin/controller/';
        $this->_public_path = Env::get('root_path').'public/';
        $this->_install_content_txt_path = Env::get('app_path').'manage/install/content/';
        $this->_install_content_controller_path = Env::get('app_path').'manage/install/controller/';
        $this->_install_content_view_path = Env::get('app_path').'manage/install/view/';
        $this->_install_content_sql_path = Env::get('app_path').'manage/install/sql/';
        $this->_config = require(Env::get('app_path').'manage/install/config.php');
    }
    /**
     * 管理界面
     * @return mixed
     */
    public function index()
    {
        //根据控制器判断是否安装订单模块
        $is_show = $this->_isInstall();

        $this->assign('is_show', $is_show);
        return $this->fetch();
    }

    /**
     * 安装管理
     * @param $name
     */
    public function installManage($name=false)
    {
        $name && $this->_doInstallDo($this->_config[$name]['data'], $this->_config[$name]['unified'], $this->_config[$name]['content']);
    }

    /**
     * 删除管理
     * @param $name
     */
    public function deleteManage($name=false)
    {
        $name && $this->_doRemoveDo($this->_config[$name]['data'], $this->_config[$name]['unified'], $this->_config[$name]['content']);
    }

    /**
     * 统一安装
     * @param $info
     * @param bool $unified
     * @param bool $content
     */
    private function _doInstallDo($info, $unified=true, $content=false)
    {
        set_time_limit(0);
        // 符合标准规范，一致性
        if ($unified && is_string($info)) {
            $info = [
                'sql' => $info,
                'controller' => [$info],
                'view' => [$info],
                'view_dir' => [$info],
                'table' => [$info]
            ];
        }
        // 执行sql文件内容
        $this->_execute_sql_file($info['sql'].'_install');
        if (!$content) { // 整体式
            // 添加控制器和内容
            foreach ($info['controller'] as $v) {
                $this->_installAddController(ucfirst($v).'.php');
            }
            // 添加视图文件
            foreach ($info['view'] as $k => $v) {
                $this->_installAddView($v, $info['view_dir'][$k]);
            }
        } else { // 片段式
            foreach ($info['content_txt'] as $v) {
                $content = $this->_getContentTxt($v);
                $this->_install_alert($this->_controller_path . ucfirst($info['controller']).'.php', $content);
            }
            // 添加控制器和内容
            foreach ($info['view'] as $k => $v) {
                $this->_installAddView($v, $info['view_dir']);
            }
        }

        // 清除缓存
        $this->_clear();
        // 成功提示
        $table_string = isset($info['table']) ? implode(',', $info['table']) : '无';
        $controller_string = is_array($info['controller']) ? implode(',', $info['controller']) : '无';
        $view_string = implode(',', $info['view']);
        echo "安装成功<br>".$this->msg('新增', $table_string, $controller_string, $view_string);
    }

    /**
     * 统一卸载操作
     * @param $info
     * @param bool $unified
     * @param bool $content
     */
    private function _doRemoveDo($info, $unified=true, $content=false)
    {
        set_time_limit(0);
        // 符合标准规范，一致性
        if ($unified && is_string($info)) {
            $info = [
                'sql' => $info,
                'controller' => [$info],
                'view' => [$info],
                'table' => [$info]
            ];
        }
        // 执行sql文件内容
        $this->_execute_sql_file($info['sql'].'_remove');
        // 删除控制器文件
        if (!$content) { // 整体式
            foreach ($info['controller'] as $v) {
                file_delete($this->_controller_path.ucfirst($v).'.php');
            }
            // 删除视图文件夹和内部文件
            foreach ($info['view'] as $k => $v) {
                dir_delete($this->_view_path.$v);
            }
        } else { // 片段式
            // 删除控制器中相关行内容
            $this->_delete_alert($info['functions']);
            // 删除视图文件
            foreach ($info['view'] as $v) {
                file_delete($this->_view_path.$info['view_dir'].'/'.$v.'.html');
            }
        }

        //清除缓存
        $this->_clear();
        // 成功提示
        $table_string = isset($info['table']) ? implode(',', $info['table']) : '无';
        $controller_string = is_array($info['controller']) ? implode(',', $info['controller']) : '无';
        $view_string = implode(',', $info['view']);
        echo "卸载成功<br>".$this->msg('卸载', $table_string, $controller_string, $view_string);
    }

    /**
     * 返回当前配置中的模块安装状况
     * @return array
     */
    private function _isInstall()
    {
        $install = [];
        foreach ($this->_config as $k => $v)
        {
            if ($v['content']) { // 片段
                if (is_file($this->_view_path.$v['data']['view_dir'].'/'.$v['data']['view'][0].'.html')) {
                    $install[$k]['install'] = true;
                } else {
                    $install[$k]['install'] = false;
                }
            } else { // 完整
                $controller = isset($v['data']['controller']) ? $v['data']['controller'][0] : $v['data'];
                $install[$k]['install'] = file_exists($this->_controller_path.ucfirst($controller).'.php') ? true : false;
            }
            $install[$k]['name'] = $v['name'];
        }
        return $install;
    }

    /**
     * 清除缓存
     */
    private function _clear()
    {
        $runtime_path = Env::get('runtime_path');
        dir_file_delete($runtime_path);
    }

    /**
     * 消息提示
     * @param $msg_array
     */
    private function _delete_alert($msg_array)
    {
        $msg = '请手动删除如下方法<br>';
        foreach ($msg_array as $k => $v) {
            if (!empty($v)) {
                $msg.=$v.'<br/>';
            }
        }
        echo $msg.'-----------------------------<br>';
    }

    /**
     * 安装时消息提示
     * @param $file
     * @param $content
     */
    private function _install_alert($file, $content)
    {
        echo '请在'.$file.'中手动插入以下内容：<br>';
        echo '<pre>';
        echo $content;
        echo '</pre>';
    }

    /**
     * 获取片段内容
     * @param $name
     * @return false|string
     */
    private function _getContentTxt($name)
    {
        return file_get_contents($this->_install_content_txt_path.$name.'.txt');
    }

    /**
     * 安装时添加控制器文件
     * @param $name
     */
    private function _installAddController($name)
    {
        if (strpos($name, 'php')) {
            copy($this->_install_content_controller_path.$name, $this->_controller_path.$name);
        }
    }

    /**
     * 安装时添加视图文件
     * @param $name
     * @param $dest_dir
     */
    private function _installAddView($name, $dest_dir)
    {
        if (strpos($name, 'html')) {
            copy($this->_install_content_view_path.$name, $this->_view_path.$dest_dir.'/'.$name);
        }
        if (is_dir($this->_install_content_view_path.$name)) {
            dir_copy($this->_install_content_view_path.$name, $this->_view_path.$dest_dir);
        }
    }

    /**
     * 执行sql文件内容
     * @param $name
     */
    private function _execute_sql_file($name)
    {
        if(!$fp = @fopen($this->_install_content_sql_path.$name.'.sql','rb'))
        {
            echo '打开文件sql-data.sql出错，请检查文件是否存在';
        }
        while(!feof($fp))
        {
            $line = rtrim(fgets($fp,2048));
            Db::execute($line);
        }
        @fclose($fp);
    }

    /**
     * 成功提示
     * @param $type
     * @param $model
     * @param $controller
     * @param $view
     * @return string
     */
    public function msg($type, $model, $controller, $view)
    {
        $msg = "{$type}权限节点和管理员权限<br>{$type}数据表：{$model}<br>{$type}控制器：{$controller}<br> {$type}视图：{$view}";
        return $msg;
    }
}