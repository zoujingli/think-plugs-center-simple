# ThinkPlugsCenter for ThinkAdmin

[![Latest Stable Version](https://poser.pugx.org/zoujingli/think-plugs-center-simple/v/stable)](https://packagist.org/packages/zoujingli/think-plugs-center-simple)
[![Total Downloads](https://poser.pugx.org/zoujingli/think-plugs-center-simple/downloads)](https://packagist.org/packages/zoujingli/think-plugs-center-simple)
[![Monthly Downloads](https://poser.pugx.org/zoujingli/think-plugs-center-simple/d/monthly)](https://packagist.org/packages/zoujingli/think-plugs-center-simple)
[![Daily Downloads](https://poser.pugx.org/zoujingli/think-plugs-center-simple/d/daily)](https://packagist.org/packages/zoujingli/think-plugs-center-simple)
[![PHP Version](https://thinkadmin.top/static/icon/php-7.1.svg)](https://thinkadmin.top)
[![License](https://thinkadmin.top/static/icon/license-apache2.svg)](https://www.apache.org/licenses/LICENSE-2.0)

**ThinkAdmin** 简易版插件入库，用于管理已安装的插件入口！

自 v1.0.7 开始，不再加载线上插件信息，因此之后的版本可以在内网运行，无需外网支持。

代码主仓库放在 **Gitee**，**Github** 仅为镜像仓库用于发布 **Composer** 包。

### 安装插件

```shell
### 安装前建议尝试更新所有组件
composer update --optimize-autoloader

### 安装稳定版本 ( 插件仅支持在 ThinkAdmin v6.1 中使用 )
composer require zoujingli/think-plugs-center-simple --optimize-autoloader

### 安装测试版本（ 插件仅支持在 ThinkAdmin v6.1 中使用 ）
composer require zoujingli/think-plugs-center-simple dev-master --optimize-autoloader
```

### 卸载插件

```shell
composer remove zoujingli/think-plugs-center-simple
```

### 插件数据

该插件未使用独立数据表；

### 版权说明

**ThinkPlugsCenter** 遵循 **Apache2** 开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有 Copyright © 2014-2024 by ThinkAdmin (https://thinkadmin.top) All rights reserved。

更多细节参阅 [LICENSE.txt](license)
