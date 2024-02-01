<?php

// +----------------------------------------------------------------------
// | Center Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2024 ThinkAdmin [ thinkadmin.top ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 免责声明 ( https://thinkadmin.top/disclaimer )
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/think-plugs-center-simple
// | github 代码仓库：https://github.com/zoujingli/think-plugs-center-simple
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace plugin\center\simple\controller;

use plugin\center\simple\Service;
use plugin\center\simple\service\Plugin;
use think\admin\Controller;
use think\admin\service\AdminService;

/**
 * 应用插件管理
 * Class Index
 * @package plugin\center\simple\controller
 */
class Index extends Controller
{
    /**
     * 应用插件入口
     * @menu true
     * @login true
     * @return void|\think\Response
     * @throws \ReflectionException
     * @throws \think\admin\Exception
     */
    public function index()
    {
        // 检查默认插件并自动跳转
        $this->default = sysdata('plugin.center.config')['default'] ?? '';
        $default = $this->request->get('from') === 'force' ? '' : $this->default;
        if (!empty($default) && Plugin::isInstall($default) && sysvar('CurrentPluginCode', $default)) {
            return json(['code' => 1, 'info' => '已设置默认插件', 'data' => strstr(liteuri(), '#', true), 'wait' => 'false']);
        }
        $this->items = Plugin::getLocalPlugs('module', $total, true);
        // 如果只有一个插件，自动进入应用插件
        if ($this->request->get('from') !== 'force' && count($this->items) === 1) {
            sysvar('CurrentPluginCode', array_pop($this->items)['code']);
            return json(['code' => 1, 'info' => '进入应用插件', 'data' => strstr(liteuri(), '#', true), 'wait' => 'false']);
        }
        // 插件列表跳转链接处理
        foreach ($this->items as &$vo) {
            $vo['encode'] = encode($vo['code']);
            $vo['center'] = sysuri("layout/{$vo['encode']}", [], false);
        }
        $this->fetch();
    }

    /**
     * 显示插件菜单
     * @login true
     * @param string $encode 应用插件编码
     * @throws \ReflectionException
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function layout(string $encode = '')
    {
        if (empty($code = decode($encode))) {
            $this->error('应用插件不能为空！');
        }
        sysvar('CurrentPluginCode', $code);
        $this->plugin = \think\admin\Plugin::get($code);
        if (empty($this->plugin)) $this->error('插件未安装！');

        // 读取插件菜单
        $menus = $this->plugin['service']::menu();
        if (empty($menus)) $this->fetchError('插件未配置菜单！');

        foreach ($menus as $k1 => &$one) {
            $one['id'] = $k1 + 1;
            $one['url'] = $one['url'] ?? (empty($one['node']) ? '#' : liteuri($one['node']));
            $one['title'] = lang($one['title'] ?? $one['name']);
            if (!empty($one['subs'])) {
                foreach ($one['subs'] as $k2 => &$two) {
                    if (isset($two['node']) && !auth($two['node'])) {
                        unset($one['subs'][$k2]);
                        continue;
                    }
                    $two['id'] = intval($k2) + 1;
                    $two['pid'] = $one['id'];
                    $two['url'] = empty($two['node']) ? '#' : liteuri($two['node']);
                    $two['title'] = lang($two['title'] ?? $two['name']);
                }
                $one['sub'] = $one['subs'];
                unset($one['subs']);
            }
            if ($one['url'] === '#' && empty($one['sub']) || (isset($one['node']) && !auth($one['node']))) {
                unset($menus[$k1]);
            }
        }

        /*! 读取当前用户权限菜单树 */
        $this->menus = [
            [
                'id'    => 9999998,
                'url'   => '#',
                'sub'   => $menus,
                'node'  => Service::getAppCode(),
                'title' => $this->plugin['name']
            ],
            [
                'id'    => 9999999,
                'url'   => admuri('index/index') . '?from=force',
                'title' => '返回首页'
            ]
        ];

        $this->super = AdminService::isSuper();
        $this->title = $this->plugin['name'] ?? '';
        $this->fetch('layout/index');
    }

    /**
     * 设置默认插件
     * @auth true
     * @return void
     * @throws \think\admin\Exception
     */
    public function setDefault()
    {
        sysdata('plugin.center.config', $this->_vali([
            'default.require' => '默认插件不能为空！'
        ]));
        $this->success('设置默认插件成功！');
    }

    /**
     * 显示异常模板
     * @return void
     */
    protected function fetchError(string $content)
    {
        $this->content = $content;
        $this->fetch('layout/error');
    }
}