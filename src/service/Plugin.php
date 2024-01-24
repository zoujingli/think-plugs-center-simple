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

namespace plugin\center\simple\service;

use think\admin\Plugin as PluginBase;
use think\admin\service\ModuleService;

/**
 * 插件数据服务
 * @class Plugin
 * @package plugin\center\simple\service
 */
abstract class Plugin
{
    public const TYPE_MODULE = 'module';
    public const TYPE_PLUGIN = 'plugin';
    public const TYPE_SERVICE = 'service';
    public const TYPE_LIBRARY = 'library';

    public const types = [
        self::TYPE_MODULE  => '系统应用',
        self::TYPE_PLUGIN  => '功能插件',
        self::TYPE_SERVICE => '基础服务',
        self::TYPE_LIBRARY => '开发组件',
    ];

    /**
     * 判断安装状态
     * @param string $code
     * @return boolean
     */
    public static function isInstall(string $code): bool
    {
        return !empty(PluginBase::get($code));
    }

    /**
     * 获取本地插件
     * @param ?string $type 插件类型
     * @param ?array $total 类型统计
     * @param boolean $check 检查权限
     * @return array
     * @throws \ReflectionException
     * @throws \think\admin\Exception
     */
    public static function getLocalPlugs(?string $type = null, ?array &$total = [], bool $check = false): array
    {
        if (is_null($total)) $total = [];
        [$data, $plugins, $onlines] = [[], ModuleService::getLibrarys(), static::getOnlinePlugs()];
        foreach (PluginBase::get() as $code => $info) {
            if (empty($plugins[$info['package']])) continue;
            // 插件类型过滤
            $ptype = $plugins[$info['package']]['type'] ?? '';
            if (is_string($type) && $ptype !== $type) continue;
            // 插件菜单处理
            $menus = $info['service']::menu();
            if ($check) {
                foreach ($menus as $k1 => &$one) {
                    if (!empty($one['subs'])) foreach ($one['subs'] as $k2 => $two) {
                        if (isset($two['node']) && !auth($two['node'])) unset($one['subs'][$k2]);
                    }
                    if ((empty($one['node']) && empty($one['subs'])) || (isset($one['node']) && !auth($one['node']))) {
                        unset($menus[$k1]);
                    }
                }
                // 如果插件为空，不显示插件
                if (empty($menus)) continue;
            }
            // 组件应用插件
            $plugin = $plugins[$info['package']];
            $online = $onlines[$info['package']] ?? [];
            $total[$ptype] = ($total[$ptype] ?? 0) + 1;
            $data[$info['package']] = [
                'type'      => $ptype,
                'code'      => $code,
                'name'      => $online['name'] ?? ($plugin['name'] ?? ''),
                'cover'     => $online['cover'] ?? ($plugin['cover'] ?? ''),
                'amount'    => $online['amount'] ?? '0.00',
                'remark'    => $online['remark'] ?? ($plugin['description'] ?? ''),
                'version'   => $plugin['version'],
                'service'   => $info['service'],
                'package'   => $info['package'],
                'license'   => $online['license'] ?? (empty($plugin['license']) ? 'unknow' : $plugin['license'][0]),
                'licenses'  => $online['license_name'] ?? (empty($online['amount'] ?? '0.00') ? "插件体验" : "收费插件"),
                'platforms' => empty($info['platforms']) ? ($online['platforms'] ?? []) : $info['platforms'],
                'plugmenus' => $menus,
            ];
        }
        return $data;
    }

    /**
     * 获取市场插件
     * @return array
     * @throws \think\admin\Exception
     */
    public static function getOnlinePlugs(): array
    {
        $data = [];
        foreach (Api::call('plugin.all') as $item) {
            $data[$item['package']] = $item;
        }
        return $data;
    }

    /**
     * 获取插件信息
     * @param string $code
     * @return mixed
     * @throws \think\admin\Exception
     */
    public static function get(string $code = '')
    {
        return Api::call('plugin.get', $code);
    }
}