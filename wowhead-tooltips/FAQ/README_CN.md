=== Wowhead Tooltips Shortcode ===
Contributors: Strogino
Donate link: https://pro-wow.ru/
Tags: wowhead, tooltips, shortcode, world of warcraft, wow, classic, wotlk, cata, mop, retail, 浮动信息, 短代码, 魔兽世界, 经典旧世, 巫妖王之怒, 大地的裂变, 熊猫人之谜
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 3.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

通过简单的短代码，轻松为《魔兽世界》中的物品、法术、任务、NPC等显示交互式 Wowhead 浮动信息。支持多种游戏版本和语言。

== 描述 (Description) ==

"Wowhead Tooltips Shortcode" 插件可让您轻松地将 Wowhead 丰富的交互式浮动信息集成到您的 WordPress 文章和页面中。通过使用简单的短代码 `[wowhead]`，您可以链接到《魔兽世界》中各种游戏版本的各种游戏实体，如物品、法术、任务、成就、NPC 和区域，包括正式服、经典怀旧服、巫妖王之怒怀旧服、大地的裂变经典版和熊猫人之谜经典版/Remix。

该插件设计灵活，支持多种语言（例如中文简体/繁体、英语、俄语、德语、法语、韩语）和游戏版本。它会根据您的 WordPress 设置智能确定语言和版本，或允许通过短代码进行明确控制。主要功能包括设置自定义链接文本、自定义链接颜色和样式，以及自动显示图标（为保持一致性，大小固定为 'tiny'）。为确保准确性和性能，官方实体名称会从 Wowhead 的 XML Feed 中获取，并使用 WordPress Transients API 在本地缓存。

此工具非常适合粉丝网站、公会页面或任何希望在其内容中直接提供来自 Wowhead 的丰富上下文信息的内容创建者。

== 功能 (Features) ==

* 为《魔兽世界》的各种实体显示官方 Wowhead 浮动信息。
* 支持多种游戏版本：
    * 正式服 (Retail/Live)
    * 经典怀旧服 / 探索赛季 (Classic Era / Season of Discovery)
    * 燃烧的远征经典版 (TBC Classic)
    * 巫妖王之怒怀旧服 (WotLK Classic)
    * 大地的裂变经典版 (Cataclysm Classic)
    * 熊猫人之谜经典版 / Remix (Mists of Pandaria Classic / Remix)
    * 公共测试服 (PTR)
    * Beta 测试服
* 广泛的语言支持，包括中文简体/繁体（默认为中文网站）、英语、俄语、德语、法语、西班牙语、意大利语、葡萄牙语、韩语。
* 根据 WordPress 网站设置自动检测语言，并可在每个短代码中明确设置语言和游戏版本。
* 允许用户为链接定义自定义文本。
* 允许用户自定义链接颜色并应用自定义 CSS 样式。
* 链接图标由 Wowhead 脚本自动添加（页面上所有图标的默认大小为 'tiny'）。
* 如果短代码中未指定，则从 Wowhead 的 XML Feed 中获取官方实体名称。
* 使用 WordPress Transients API 缓存 XML 响应，以提高性能并减少对 Wowhead 服务器的负载。
* 通过 `[wowhead]` 短代码轻松集成。

== 安装 (Installation) ==

1.  **通过 WordPress 后台上传：**
    * 从 [GitHub 仓库](https://github.com/pro-wow/wowhead-tooltips/) 下载插件 ZIP 文件。
    * 在您的 WordPress 后台，转到“插件” > “添加新插件”。
    * 点击顶部的“上传插件”。
    * 选择下载的 ZIP 文件，然后点击“立即安装”。
    * 激活插件。
2.  **手动上传：**
    * 下载并解压插件包。
    * 将插件文件夹（例如 `wowhead-tooltips`）上传到您服务器上的 `/wp-content/plugins/` 目录。
    * 通过 WordPress 中的“插件”菜单激活插件。
3.  **创建 `languages` 文件夹：**
    * 为了国际化，请确保插件的根目录中存在一个 `languages` 文件夹（例如 `/wp-content/plugins/wowhead-tooltips/languages/`）。该插件使用 `wowhead-tooltips` 作为其文本域 (text domain)。

== 使用方法 (Usage) ==

该插件提供 `[wowhead]` 短代码，可以插入到 WordPress 文章、页面或小工具中。

=== 基本语法 ===

`[wowhead id="实体参数字符串"]`

=== 短代码属性 ===

该短代码接受多个属性以自定义其行为和外观：

* **`id`** (必需):
    * 一个包含有关 Wowhead 实体及其特定参数信息的字符串。
    * 此字符串中的参数用和号 (`&`) 分隔。
    * **实体类型和 ID:** 必须包括实体类型及其数字 ID (例如, `item=19019`, `spell=33786`, `quest=10129`)。
    * **`domain`** (可选, 在 `id` 字符串内): 指定浮动信息的语言和/或游戏版本。
        * 示例: `domain=cn` (简体中文正式服), `domain=classic` (英文经典怀旧服), `domain=de.wotlk` (德文巫妖王之怒怀旧服)。
        * 如果省略，插件会尝试使用您 WordPress 网站的语言用于 Wowhead 正式服。如果不支持，则默认为英文正式服。
        * 插件会尝试纠正常见的错误格式，例如将 `version.lang` 改为预期的 `lang.version`。
    * **其他 Wowhead 参数:** 可以包含 Wowhead 识别的其他参数 (例如, 物品的 `gems=...`, `ench=...`, `pcs=...`)。
* **`name`** (可选):
    * 设置显示链接的自定义文本。
    * 示例: `name="我的超棒物品"`
    * 如果未提供，插件会尝试通过 Wowhead 的 XML Feed 获取官方名称。如果失败，则使用通用名称 (例如, "物品 19019")。
    * **注意:** 使用此属性会将页面上 `whTooltips` 中的全局 `renameLinks` 选项设置为 `false`，以防止 Wowhead 脚本覆盖自定义链接名称。
* **`color`** (可选):
    * 设置链接的文本颜色。
    * 接受 CSS 颜色名称 (例如, `color="green"`), HEX 代码 (例如, `color="#FF8C00"`), 或您在主题样式表中定义的自定义 CSS 类名 (例如, `color="my-custom-link-class"`)。
    * **注意:** 使用此属性 (或 `style`) 会将页面上 `whTooltips` 中的全局 `colorLinks` 选项设置为 `false`，以防止 Wowhead 脚本应用其默认的品质颜色。
* **`style`** (可选):
    * 向链接应用自定义内联 CSS 样式。
    * 示例: `style="font-weight: bold; text-decoration: underline;"`
    * 使用此属性也会在页面上全局设置 `colorLinks: false`。

**关于图标的重要说明:**
在此插件版本中，页面上所有 Wowhead 浮动信息的图标大小 (`iconSize`) 固定为 `'tiny'`。短代码中的 `icon` 属性将被**忽略**，不会用于设置 `iconSize`。如果 `whTooltips` 对象中的 `iconizeLinks` 已启用 (默认情况下为 `true`)，则图标由 Wowhead 的 `power.js` 脚本自动添加。

=== 插件如何确定 `whTooltips` 全局选项 ===

插件会根据当前页面上*任何* `[wowhead]` 短代码中使用的属性来配置全局 `whTooltips` JavaScript 对象：

* `iconSize`: 始终设置为 `'tiny'`。
* `renameLinks`: 如果页面上的任何短代码使用 `name` 属性，则设置为 `false`；否则为 `true`。
* `colorLinks`: 如果页面上的任何短代码使用 `color` 或 `style` 属性，则设置为 `false`；否则为 `true`。
* `iconizeLinks`: 始终设置为 `true`。

=== 示例 ===

==== 1. 基本对象类型 ====
(默认语言/版本：正式服英语，除非网站语言是 Wowhead 支持的正式服语言)

* **物品:**
    `[wowhead id="item=19019"]`
* **法术:**
    `[wowhead id="spell=33786"]`
* **任务:**
    `[wowhead id="quest=10129"]`

==== 2. 指定语言 (正式服示例) ====

* **简体中文:**
    `[wowhead id="item=19019&domain=cn"]`
* **德语:**
    `[wowhead id="item=19019&domain=de"]`

==== 3. 指定游戏版本 (默认语言：英语) ====

* **魔兽世界经典怀旧服:**
    `[wowhead id="item=19019&domain=classic"]`
* **巫妖王之怒怀旧服:**
    `[wowhead id="item=49623&domain=wotlk"]`

==== 4. 组合游戏版本和语言 ====

* **简体中文经典怀旧服:**
    `[wowhead id="item=19019&domain=cn.classic"]`
* **德语巫妖王之怒怀旧服:**
    `[wowhead id="item=49623&domain=de.wotlk"]`

==== 5. 使用 `name`, `color`, `style` 属性 ====

* **自定义链接名称:**
    `[wowhead id="item=19019&domain=cn" name="传说之剑!"]`
* **绿色链接颜色:**
    `[wowhead id="item=19019&domain=cn" color="green"]`
* **通过 HEX 和粗体设置链接颜色:**
    `[wowhead id="item=19019&domain=cn" color="#FF8C00" style="font-weight: bold;"]`
* **为链接自定义 CSS 类:**
    `[wowhead id="item=19019&domain=cn" color="my-custom-wowhead-link"]`
    *(您需要在主题的 CSS 中为 `.my-custom-wowhead-link` 定义样式)。*

==== 6. `id` 内的高级 Wowhead 参数 ====

您可以直接在 `id` 字符串中传递其他 Wowhead 特定参数。对于物品，这些可以包括宝石、附魔、套装奖励等。

* **带宝石和附魔的物品 (示例):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`

==== 7. 空或无效的短代码 ====

* **空短代码 `[wowhead]` 或 `[wowhead id=""]`:**
    将显示一个指向 Wowhead 主页的简单链接 (如果适用，会考虑网站语言)。
* **ID 不存在或数据无效的短代码:**
    `[wowhead id="item=999999999"]`
    将以红色显示链接 "Wowhead.com (unavailable)"。
* **仅包含域的短代码:**
    `[wowhead id="domain=cn.classic"]`
    将显示指向指定域的 Wowhead 主页的链接。

== 语言和版本确定优先级 ==

1.  **在短代码中明确指定:** `id` 属性中的 `domain` 参数具有最高优先级。
2.  **WordPress 网站语言:** 如果未指定 `domain`，插件将使用您 WordPress 网站的语言用于 Wowhead 正式服 (如果支持该语言)。
3.  **默认英语:** 如果以上方法均不适用，则使用 Wowhead 正式服的英语。

== 缓存 ==

该插件会使用 WordPress Transients API 缓存来自 Wowhead XML Feed 的响应 (对象名称、404 状态)，成功请求缓存 12 小时，404 错误缓存 1 小时。这有助于减少对 Wowhead 服务器的请求次数并加快您页面的加载速度。

== 故障排除 ==

* **浮动信息未出现 / 图标丢失:**
    1.  确保插件已激活。
    2.  确保页面上至少有一个 `[wowhead]` 短代码。
    3.  检查页面的 HTML 源代码 (Ctrl+U 或 Cmd+Option+U): `const whTooltips = {...};` 和 `<script src="https://wow.zamimg.com/widgets/power.js"></script>` 这两行应该出现在页面末尾的 `</body>` 之前。
    4.  检查浏览器的 JavaScript 控制台 (F12 -> Console) 中是否有错误。任何 JS 错误都可能阻止 Wowhead 脚本初始化。
    5.  确保您主题的 `footer.php` 文件在关闭 `</body>` 标签之前包含 `<?php wp_footer(); ?>` 调用。
    6.  尝试临时禁用其他插件并切换到默认的 WordPress 主题 (例如 Twenty Twenty-Three) 以排除冲突。
    7.  清除所有级别的缓存 (浏览器、WordPress 插件、服务器端缓存、CDN)。
* **语言或游戏版本不正确:** 仔细检查短代码中 `domain` 参数的值。
* **PHP 错误:** 如果插件无法激活或导致问题，请在 `wp-config.php` 中启用 WordPress 调试 (`WP_DEBUG`, `WP_DEBUG_LOG`) 以检查 `wp-content/debug.log` 中的 PHP 错误。

== 贡献 (Contributing) ==

欢迎贡献！请随时在 [GitHub](https://github.com/pro-wow/wowhead-tooltips/) 上 fork 本仓库，进行更改并提交拉取请求 (pull requests)。

== 更新日志 (Changelog) ==

= 3.1.1 =
* 更新插件头部信息以兼容 WordPress.org (PCP 插件检查)。
* 在 PHP 代码中添加了英文注释 (用于开发版本)。
* 将 WordPress "Tested up to" 版本更新为 6.8。
* 将插件名称标准化为 "Wowhead Tooltips Shortcode"。
* 修正 `Stable tag` 以匹配插件版本。
* 缩短了简短描述以符合 150 个字符的限制。

= 3.0.0 =
* Wowhead JS 脚本直接输出的初始稳定版本。
* 通过 Transients API 启用 XML 缓存。
* `iconSize` 全局固定为 'tiny'。
* `renameLinks` 和 `colorLinks` 根据短代码属性全局确定。
* 全面的域和语言处理。