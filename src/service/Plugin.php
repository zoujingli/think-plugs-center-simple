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
        [$data, $plugins] = [[], ModuleService::getLibrarys()];
        foreach (PluginBase::get() as $code => $packer) {
            if (empty($plugins[$packer['package']])) continue;
            // 插件类型过滤
            $ptype = $plugins[$packer['package']]['type'] ?? '';
            $total[$ptype] = ($total[$ptype] ?? 0) + 1;
            if (is_string($type) && $ptype !== $type) continue;
            // 插件菜单处理
            $menus = $packer['service']::menu();
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
            $plugin = $plugins[$packer['package']];
            $data[$packer['package']] = [
                'type'      => $ptype,
                'code'      => $code,
                'name'      => $plugin['name'] ?? '',
                'cover'     => $plugin['cover'] ?? '',
                'amount'    => $plugin['amount'] ?? '0.00',
                'remark'    => $plugin['remark'] ?? ($plugin['description'] ?? ''),
                'version'   => $plugin['version'],
                'package'   => $packer['package'],
                'service'   => $packer['service'],
                'license'   => empty($plugin['license']) ? 'unknow' : $plugin['license'][0],
                'licenses'  => "",
                'platforms' => $packer['platforms'] ?? [],
                'plugmenus' => $menus,
            ];
        }
        return $data;
    }
}