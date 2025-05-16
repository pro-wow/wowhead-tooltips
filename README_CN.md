# WordPress Wowhead Tooltips Shortcode 插件

**版本:** 3.0.0
**作者:**您的名字 (Your Name)
**作者网址:** https://your-website.com

"Wowhead Tooltips Shortcode" WordPress 插件允许您轻松地将来自 Wowhead 数据库的各种游戏实体、法术、NPC、任务等链接插入到您的 WordPress 网站中，并在鼠标悬停时自动显示浮动提示信息。该插件支持多种《魔兽世界》游戏版本（如正式服、经典怀旧服、巫妖王之怒怀旧服等）和多种语言。

## 功能特性

* 显示各种游戏内实体的 Wowhead 浮动提示信息。
* 支持正式服 (Retail)、经典怀旧服 (Classic Era, SoD)、燃烧的远征怀旧服 (TBC Classic)、巫妖王之怒怀旧服 (WotLK Classic)、大地的裂变怀旧服 (Cataclysm Classic)、熊猫人之谜怀旧服 (MoP Classic)、PTR测试服和Beta测试服。
* 支持多种语言，包括中文（简体/繁体）、英语、俄语、德语、法语、西班牙语等。
* 根据 WordPress 设置自动检测语言，或在短代码中明确指定语言。
* 能够为链接设置自定义文本。
* 能够更改链接的颜色和样式。
* 链接图标由 Wowhead 脚本自动添加（默认尺寸为 'tiny'）。
* 如果短代码中未指定，则从 Wowhead 的 XML Feed 中检索对象名称。
* 缓存 XML 响应以提高性能并减少对 Wowhead 服务器的负载。
* 通过 `[wowhead]` 短代码轻松集成。

## 安装

1.  将插件文件夹（例如 `wowhead-tooltips-shortcode`）上传到您 WordPress 网站的 `/wp-content/plugins/` 目录。
    * 或者，通过 WordPress 后台的“插件” > “安装插件” > “上传插件”部分上传插件的 ZIP 压缩包。
2.  在 WordPress 的“插件”菜单中激活该插件。

## 使用方法

该插件提供了一个 `[wowhead]` 短代码，您可以将其插入到您网站的文章、页面或小工具中。

### 基本语法

[wowhead id="对象参数"]

### 主要短代码属性

* **`id`** (必需): 一个包含 Wowhead 对象信息及其参数的字符串。此字符串中的参数使用与号 (`&`) 分隔。
    * **对象类型和ID:** 例如, `item=19019` (物品), `spell=33786` (法术), `quest=10129` (任务), `npc=16060` (NPC) 等。支持的类型完整列表可以在插件的 `edit.txt` 文件或 Wowhead 文档中找到。
    * **`domain`**: (可选参数, 位于 `id` 内部) 定义语言和/或游戏版本。
        * 示例: `domain=cn` (简体中文正式服), `domain=tw` (繁體中文正式服), `domain=classic` (英文经典怀旧服), `domain=cn.wotlk` (简体中文巫妖王之怒怀旧服)。
        * 如果未指定 `domain`，插件将尝试使用您 WordPress 网站的语言 (如果 Wowhead 正式服支持该语言)。如果这也不可能，则将使用英文正式服。
        * 插件会尝试修正无效格式，例如 `classic.cn` 将被处理为 `cn.classic`。
    * **额外的 Wowhead 参数:** 可以添加以进一步指定对象，例如物品的 `gems=...`, `ench=...`, `pcs=...`。
* **`name`** (可选): 允许您为显示的链接设置自定义文本。
    * 示例: `name="我最喜欢的剑"`
    * 如果未提供 `name`，插件将尝试从 Wowhead 的 XML Feed 中获取官方对象名称。如果这也失败，将使用默认名称 (例如, "Item 19019")。
    * 使用 `name` 属性时，Wowhead 脚本的 `renameLinks` 选项将自动为整个页面设置为 `false`，以防止您的自定义名称被覆盖。
* **`color`** (可选): 允许您设置链接的文本颜色。
    * 可以是 CSS 颜色名称 (例如, `color="green"`), HEX 代码 (例如, `color="#FF0000"`), 或您在样式中定义的 CSS 类名 (例如, `color="my-custom-color-class"`)。
    * 使用 `color` (或 `style`) 属性时，Wowhead 脚本的 `colorLinks` 选项将自动为整个页面设置为 `false`，以防止您的自定义颜色被 Wowhead 的默认链接颜色覆盖。
* **`style`** (可选): 允许您向链接应用自定义内联 CSS 样式。
    * 示例: `style="font-weight: bold; text-decoration: underline;"`
    * 与 `color` 属性类似，使用 `style` 将为整个页面设置 `colorLinks: false`。

**关于图标的说明:** 在此插件版本 (`v3.0.0`) 中，图标大小默认为页面上所有 Wowhead 链接的 `'tiny'`。短代码中的 `icon` 属性将被**忽略**，以简化并确保稳定操作。如果 `iconizeLinks` 启用 (默认为 `true`)，则图标由 Wowhead 脚本自动添加。

### 使用示例

#### 1. 基本对象类型 (默认语言和版本 - 正式服，英语，除非网站语言是中文/俄语/德语等)

* **物品:**
    `[wowhead id="item=19019"]` (显示风剑)
* **法术:**
    `[wowhead id="spell=33786"]` (显示龙卷风)
* **任务:**
    `[wowhead id="quest=10129"]`
* **成就:**
    `[wowhead id="achievement=621"]`
* **NPC:**
    `[wowhead id="npc=16060"]`
* **区域:**
    `[wowhead id="zone=3703"]`

#### 2. 指定语言 (以正式服为例)

* **简体中文:**
    `[wowhead id="item=19019&domain=cn"]`
* **繁體中文:**
    `[wowhead id="item=19019&domain=tw"]`
* **韩语:**
    `[wowhead id="item=19019&domain=ko"]`

#### 3. 指定游戏版本 (默认语言 - 英语，除非网站语言与支持的版本语言匹配)

* **魔兽世界经典怀旧服 (英文):**
    `[wowhead id="item=19019&domain=classic"]`
* **巫妖王之怒怀旧服 (英文):**
    `[wowhead id="item=49623&domain=wotlk"]`
* **大地的裂变怀旧服 (英文):**
    `[wowhead id="item=71086&domain=cata"]`

#### 4. 组合游戏版本和语言

* **简体中文经典怀旧服:**
    `[wowhead id="item=19019&domain=cn.classic"]`
* **繁體中文巫妖王之怒怀旧服 (修正格式):**
    `[wowhead id="item=49623&domain=tw.wotlk"]` (插件会将 `wotlk.tw` 处理为 `tw.wotlk`)
* **韩语大地的裂变怀旧服:**
    `[wowhead id="item=71086&domain=ko.cata"]`

#### 5. 使用 `name`, `color`, `style` 属性

* **自定义链接名称:**
    `[wowhead id="item=19019&domain=cn" name="传说之剑!"]`
* **绿色链接颜色:**
    `[wowhead id="item=19019&domain=cn" color="green"]`
* **通过 HEX 和粗体设置链接颜色:**
    `[wowhead id="item=19019&domain=cn" color="#FF8C00" style="font-weight: bold;"]`
* **为链接自定义 CSS 类:**
    `[wowhead id="item=19019&domain=cn" color="my-custom-wowhead-link"]`
    *(您需要在主题的 CSS 中为 `.my-custom-wowhead-link` 定义样式)。*
* **包含不同属性的完整示例:**
    `[wowhead id="spell=33786&domain=fr" name="Cyclone (FR)" style="text-transform: uppercase;" color="blue"]`

#### 6. `id` 内的高级 Wowhead 参数

您可以直接在 `id` 字符串中传递额外的 Wowhead 特定参数。例如，对于物品，这些参数可以包括宝石、附魔、套装奖励等。

* **带宝石和附魔的物品 (示例):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`
* **带套装奖励的物品 (示例):**
    `[wowhead id="item=25697&domain=tbc&pcs=25695:25697"]`

#### 7. 空或无效的短代码

* **空短代码 `[wowhead]` 或 `[wowhead id=""]`:**
    将显示一个指向 Wowhead 主页的简单链接 (如果适用，会考虑网站语言)。
* **ID 不存在或数据无效的短代码:**
    `[wowhead id="item=999999999"]`
    将以红色显示链接 "Wowhead.com (unavailable)"。
* **仅包含域的短代码:**
    `[wowhead id="domain=cn.classic"]`
    将显示指向指定域的 Wowhead 主页的链接。

## 语言和版本确定优先级

1.  **在短代码中明确指定:** `id` 属性中的 `domain` 参数具有最高优先级。
2.  **WordPress 网站语言:** 如果未指定 `domain`，插件将使用您 WordPress 网站的语言用于 Wowhead 正式服 (如果支持该语言)。
3.  **默认英语:** 如果以上方法均不适用，则使用 Wowhead 正式服的英语。

## 缓存

该插件会缓存来自 Wowhead XML Feed 的响应 (对象名称、404 状态)，成功请求缓存 12 小时，404 错误缓存 1 小时。这有助于减少对 Wowhead 服务器的请求次数并加快您页面的加载速度。

##故障排除

* **提示信息未出现:**
    1.  确保插件已激活。
    2.  确保页面上至少有一个 `[wowhead]` 短代码。
    3.  检查页面的 HTML 源代码 (Ctrl+U 或 Cmd+Option+U): `const whTooltips = {...};` 和 `<script src="https://wow.zamimg.com/widgets/power.js"></script>` 这两行应该出现在页面末尾的 `</body>` 之前。
    4.  检查浏览器 JavaScript 控制台 (F12 -> Console) 中是否有错误。
    5.  确保您主题的 `footer.php` 文件中包含 `<?php wp_footer(); ?>` 调用。
    6.  尝试临时禁用其他插件并切换到默认的 WordPress 主题以排除冲突。
    7.  清除所有级别的缓存 (浏览器、WordPress 插件、服务器端缓存、CDN)。
* **语言或游戏版本不正确:** 仔细检查短代码中 `domain` 参数的值。

## 支持的实体列表 (用于 `id` 的主要类型)

该插件主要针对 Wowhead 为其提供工具提示的主要实体。最常用的是：
`item`, `spell`, `quest`, `achievement`, `npc`, `zone`, `currency`, `faction`, `pet`, `itemset`, `skill`, `mount`, `outfit`, `event`, `object`, `transmog`, `affix`.

对于更具体或罕见的实体类型，请参阅 Wowhead 文档。

---

希望这个插件对您的网站有所帮助！