**Wowhead Tooltips Shortcode**
* Contributors: Strogino
* Donate link: [pro-wow.ru](https://pro-wow.ru/?utm_source=github&utm_medium=organic&utm_campaign=wowhead-tooltips)
* Tags: wowhead, tooltips, shortcode, world of warcraft, wow, classic, wotlk, cata, mop, retail, подсказки, шорткод, вов, 浮动信息
* Requires at least: 5.0
* Tested up to: 6.8
* Requires PHP: 7.4
* Stable tag: 3.1.1
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html

### This plugin allows you to easily display interactive Wowhead tooltips for World of Warcraft entities using a simple shortcode. It supports multiple game versions and languages. For more details, please select your preferred language below.

---

**Choose your language / Выберите ваш язык / 请选择您的语言:**

* <a href="#english-readme">English</a>
* <a href="#russian-readme">Русский</a>
* <a href="#chinese-readme">中文 (简体)</a>

---

<a id="english-readme"></a>

## Wowhead Tooltips Shortcode (English)

The "Wowhead Tooltips Shortcode" plugin allows you to seamlessly integrate Wowhead's rich, interactive tooltips into your WordPress posts and pages. By using a simple shortcode `[wowhead]`, you can link to various game entities like items, spells, quests, achievements, NPCs, and zones from any World of Warcraft expansion, including Retail, Classic Era, WotLK Classic, Cataclysm Classic, and Mists of Pandaria Classic/Remix.

The plugin is designed for flexibility, offering support for numerous languages and game versions. It intelligently determines the language and version based on your WordPress settings or allows explicit control via the shortcode. Key features include custom link text, customizable link colors and styles, and automatic icon display (fixed at 'tiny' size for consistency). To ensure accuracy and performance, official entity names are fetched from Wowhead's XML feed and cached locally using WordPress Transients.

This tool is ideal for fan sites, guild pages, or any content creator looking to provide rich, contextual information from Wowhead directly within their content.

### Features

* Displays official Wowhead tooltips for a wide range of World of Warcraft entities.
* Supports multiple game versions:
    * Retail (Live)
    * Classic Era / Season of Discovery
    * The Burning Crusade Classic (TBC)
    * Wrath of the Lich King Classic (WotLK)
    * Cataclysm Classic (Cata)
    * Mists of Pandaria Classic (MoP) / Remix
    * Public Test Realm (PTR)
    * Beta servers
* Extensive language support, including English (default), Russian, German, French, Spanish, Italian, Portuguese, Korean, Simplified Chinese, and Traditional Chinese.
* Automatic language detection based on WordPress site settings, with an option to explicitly set language and game version per shortcode.
* Allows users to define custom link text.
* Allows users to customize link color and apply custom CSS styles.
* Icons for links are automatically added by the Wowhead script (default size is 'tiny' for all icons on the page).
* Fetches official names for entities from Wowhead's XML feed if not specified in the shortcode.
* Caches XML responses using WordPress Transients API to improve performance and reduce load on Wowhead servers.
* Simple integration via the `[wowhead]` shortcode.

### Installation

1.  **Upload via WordPress Admin:**
    * Download the plugin ZIP file from the [GitHub repository](https://github.com/pro-wow/wowhead-tooltips/) or [pro-wow.ru](https://pro-wow.ru/?utm_source=github&utm_medium=organic&utm_campaign=wowhead-tooltips).
    * In your WordPress admin panel, go to "Plugins" > "Add New".
    * Click "Upload Plugin" at the top.
    * Choose the downloaded ZIP file and click "Install Now".
    * Activate the plugin.
2.  **Manual Upload:**
    * Download and unzip the plugin package.
    * Upload the plugin folder (e.g., `wowhead-tooltips`) to the `/wp-content/plugins/` directory on your server.
    * Activate the plugin through the "Plugins" menu in WordPress.
3.  **Create `languages` folder:**
    * For internationalization, ensure a `languages` folder exists in the plugin's root directory (e.g., `/wp-content/plugins/wowhead-tooltips/languages/`). The plugin uses `wowhead-tooltips` as its text domain.

### Usage

The plugin provides the `[wowhead]` shortcode, which can be inserted into WordPress posts, pages, or widgets.

#### Basic Syntax

`[wowhead id="entity_parameters_string"]`

#### Shortcode Attributes

The shortcode accepts several attributes to customize its behavior and appearance:

* **`id`** (required):
    * A string containing information about the Wowhead entity and its specific parameters.
    * Parameters within this string are separated by an ampersand (`&`).
    * **Entity Type and ID:** Must include the type of entity and its numerical ID (e.g., `item=19019`, `spell=33786`, `quest=10129`).
    * **`domain`** (optional, within the `id` string): Specifies the language and/or game version for the tooltip.
        * Examples: `domain=ru` (Russian Retail), `domain=classic` (English Classic), `domain=de.wotlk` (German WotLK Classic).
        * If omitted, the plugin attempts to use the WordPress site's language for Retail Wowhead. If unsupported, it defaults to English Retail.
        * The plugin attempts to correct common misformats like `version.lang` to the expected `lang.version`.
    * **Additional Wowhead Parameters:** Other parameters recognized by Wowhead (e.g., `gems=...`, `ench=...`, `pcs=...` for items) can be included.
* **`name`** (optional):
    * Sets custom text for the displayed link.
    * Example: `name="My Awesome Item"`
    * If not provided, the plugin tries to fetch the official name via Wowhead's XML feed. If that fails, a generic name like "Item 19019" is used.
    * **Note:** Using this attribute will set the global `renameLinks` option in `whTooltips` to `false` for the page, preventing Wowhead's script from overriding custom link names.
* **`color`** (optional):
    * Sets the text color of the link.
    * Accepts CSS color names (e.g., `color="green"`), HEX codes (e.g., `color="#FF8C00"`), or a custom CSS class name defined in your theme's stylesheet (e.g., `color="my-custom-link-class"`).
    * **Note:** Using this attribute (or `style`) will set the global `colorLinks` option in `whTooltips` to `false` for the page, preventing Wowhead's script from applying its default quality colors.
* **`style`** (optional):
    * Applies custom inline CSS styles to the link.
    * Example: `style="font-weight: bold; text-decoration: underline;"`
    * Using this also sets `colorLinks: false` globally for the page.

**Important Note on Icons:**
In this version of the plugin, the `iconSize` for all Wowhead tooltips on a page is fixed to `'tiny'`. The `icon` attribute in the shortcode is **ignored** for setting `iconSize`. Icons are automatically added by Wowhead's `power.js` script if `iconizeLinks` is enabled in the `whTooltips` object (which it is by default).

#### How `whTooltips` Global Options are Determined by the Plugin

The plugin configures the global `whTooltips` JavaScript object based on the attributes used in *any* `[wowhead]` shortcode on the current page:

* `iconSize`: Always set to `'tiny'`.
* `renameLinks`: Set to `false` if any shortcode on the page uses the `name` attribute; otherwise, `true`.
* `colorLinks`: Set to `false` if any shortcode on the page uses the `color` or `style` attribute; otherwise, `true`.
* `iconizeLinks`: Always set to `true`.

#### Examples

##### 1. Basic Object Types
(Default language/version: Retail English, unless site language is a supported Wowhead retail language)

* **Item:**
    `[wowhead id="item=19019"]`
* **Spell:**
    `[wowhead id="spell=33786"]`
* **Quest:**
    `[wowhead id="quest=10129"]`

##### 2. Specifying Language (Retail Example)

* **Russian:**
    `[wowhead id="item=19019&domain=ru"]`
* **German:**
    `[wowhead id="item=19019&domain=de"]`

##### 3. Specifying Game Version (Default language: English)

* **World of Warcraft Classic:**
    `[wowhead id="item=19019&domain=classic"]`
* **WotLK Classic:**
    `[wowhead id="item=49623&domain=wotlk"]`

##### 4. Combining Game Version and Language

* **Russian Classic:**
    `[wowhead id="item=19019&domain=ru.classic"]`
* **German WotLK Classic:**
    `[wowhead id="item=49623&domain=de.wotlk"]`

##### 5. Using `name`, `color`, `style` Attributes

* **Custom Link Name:**
    `[wowhead id="item=19019&domain=en" name="Legendary Sword!"]`
* **Green Link Color:**
    `[wowhead id="item=19019&domain=en" color="green"]`
* **Link Color via HEX and Bold Font:**
    `[wowhead id="item=19019&domain=en" color="#FF8C00" style="font-weight: bold;"]`
* **Custom CSS Class for Link:**
    `[wowhead id="item=19019&domain=en" color="my-custom-wowhead-link"]`
    *(You will need to define the style for `.my-custom-wowhead-link` in your theme's CSS).*

##### 6. Advanced Wowhead Parameters within `id`

You can pass additional Wowhead-specific parameters directly in the `id` string. For items, these can include gems, enchants, set bonuses, etc.

* **Item with Gem and Enchant (Example):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`

##### 7. Empty or Invalid Shortcodes

* **Empty Shortcode `[wowhead]` or `[wowhead id=""]`:**
    Will display a simple link to the Wowhead main page (respecting site language if applicable).
* **Shortcode with Non-existent ID or Invalid Data:**
    `[wowhead id="item=999999999"]`
    Will display a link "Wowhead.com (unavailable)" in red.
* **Shortcode with Only Domain:**
    `[wowhead id="domain=ru.classic"]`
    Will display a link to the Wowhead main page for the specified domain.

### Language and Version Determination Priority

1.  **Explicit Specification in Shortcode:** The `domain` parameter in the `id` attribute has the highest priority.
2.  **WordPress Site Language:** If `domain` is not specified, the plugin uses your WordPress site language for the Retail version of Wowhead (if that language is supported).
3.  **English Default:** If none of the above methods are applicable, English for Retail Wowhead is used.

### Caching

The plugin caches responses from Wowhead's XML feeds (object names, 404 status) for 12 hours for successful requests and 1 hour for 404 errors using the WordPress Transients API. This helps reduce the number of requests to Wowhead's servers and speeds up your page loads.

### Troubleshooting

* **Tooltips Not Appearing / Icons Missing:**
    1.  Ensure the plugin is active.
    2.  Ensure there is at least one `[wowhead]` shortcode on the page.
    3.  Check the page's HTML source (Ctrl+U or Cmd+Option+U): the lines `const whTooltips = {...};` and `<script src="https://wow.zamimg.com/widgets/power.js"></script>` should be present near the end of the page, before `</body>`.
    4.  Check your browser's JavaScript console (F12 -> Console) for errors. Any JS errors can prevent Wowhead scripts from initializing.
    5.  Ensure your theme's `footer.php` file contains the `<?php wp_footer(); ?>` call before the closing `</body>` tag.
    6.  Try temporarily disabling other plugins and switching to a default WordPress theme (like Twenty Twenty-Three) to rule out conflicts.
    7.  Clear all levels of cache (browser, WordPress plugins, server-side cache, CDN).
* **Incorrect Language or Game Version:** Carefully check the value of the `domain` parameter in your shortcode.
* **PHP Errors:** If the plugin fails to activate or causes issues, enable WordPress debugging (`WP_DEBUG`, `WP_DEBUG_LOG` in `wp-config.php`) to check for PHP errors in `wp-content/debug.log`.

### Contributing

Contributions are welcome! Please feel free to fork the repository on [GitHub](https://github.com/pro-wow/wowhead-tooltips/), make changes, and submit pull requests.

### Changelog

#### 3.1.1
* Updated plugin header for WordPress.org compatibility (PCP plugin check).
* Added English comments throughout the PHP code (for development version).
* Set "Tested up to" WordPress version to 6.8.
* Standardized plugin name to "Wowhead Tooltips Shortcode".
* Corrected `Stable tag` to match plugin version.
* Shortened short description to meet 150 character limit.

#### 3.0.0
* Initial stable version with direct script output for Wowhead JS.
* XML Caching enabled via Transients API.
* `iconSize` fixed to 'tiny' globally.
* `renameLinks` and `colorLinks` determined globally based on shortcode attributes.
* Comprehensive domain and language handling.

---
<a id="russian-readme"></a>

## Wowhead Tooltips Shortcode (Русский)

Плагин "Wowhead Tooltips Shortcode" позволяет вам легко интегрировать многофункциональные интерактивные всплывающие подсказки Wowhead в ваши записи и страницы WordPress. Используя простой шорткод `[wowhead]`, вы можете ссылаться на различные игровые сущности, такие как предметы, заклинания, задания, достижения, NPC и зоны из любого дополнения World of Warcraft, включая Retail, Classic Era, WotLK Classic, Cataclysm Classic и Mists of Pandaria Classic/Remix.

Плагин разработан с упором на гибкость и предлагает поддержку множества языков (например, русский, английский, немецкий, французский, китайский, корейский) и версий игры. Он интеллектуально определяет язык и версию на основе настроек вашего WordPress или позволяет явно указать их через шорткод. Ключевые функции включают установку собственного текста для ссылок, настройку цвета и стиля ссылок, а также автоматическое отображение иконок (размер зафиксирован как 'tiny' для единообразия). Для обеспечения точности и производительности официальные названия сущностей извлекаются из XML-фида Wowhead и кэшируются локально с помощью WordPress Transients.

Этот инструмент идеально подходит для фан-сайтов, сайтов гильдий или для любого создателя контента, который хочет предоставить насыщенную контекстную информацию из Wowhead непосредственно в своем контенте.

### Возможности

* Отображение официальных всплывающих подсказок Wowhead для широкого спектра сущностей World of Warcraft.
* Поддержка нескольких версий игры:
    * Retail (Актуальная версия)
    * Classic Era / Сезон Открытий
    * The Burning Crusade Classic (TBC)
    * Wrath of the Lich King Classic (WotLK)
    * Cataclysm Classic (Cata)
    * Mists of Pandaria Classic (MoP) / Remix
    * Public Test Realm (PTR / Тестовый игровой мир)
    * Beta-серверы
* Обширная языковая поддержка, включая русский (по умолчанию для русскоязычных сайтов), английский, немецкий, французский, испанский, итальянский, португальский, корейский, китайский (упрощенный) и китайский (традиционный).
* Автоматическое определение языка на основе настроек сайта WordPress, с возможностью явного указания языка и версии игры в каждом шорткоде.
* Позволяет пользователям задавать собственный текст для ссылок.
* Позволяет пользователям настраивать цвет ссылок и применять пользовательские CSS-стили.
* Иконки для ссылок автоматически добавляются скриптом Wowhead (размер по умолчанию 'tiny' для всех иконок на странице).
* Получение официальных названий сущностей из XML-фида Wowhead, если они не указаны в шорткоде.
* Кэширование XML-ответов с помощью WordPress Transients API для улучшения производительности и снижения нагрузки на серверы Wowhead.
* Простая интеграция с помощью шорткода `[wowhead]`.

### Установка

1.  **Загрузка через админ-панель WordPress:**
    * Скачайте ZIP-архив плагина с [репозитория GitHub](https://github.com/pro-wow/wowhead-tooltips/) или [pro-wow.ru](https://pro-wow.ru/?utm_source=github&utm_medium=organic&utm_campaign=wowhead-tooltips).
    * В вашей админ-панели WordPress перейдите в "Плагины" > "Добавить новый".
    * Нажмите "Загрузить плагин" вверху страницы.
    * Выберите скачанный ZIP-файл и нажмите "Установить".
    * Активируйте плагин.
2.  **Ручная загрузка:**
    * Скачайте и распакуйте ZIP-архив плагина.
    * Загрузите папку плагина (например, `wowhead-tooltips`) в директорию `/wp-content/plugins/` на вашем сервере.
    * Активируйте плагин через меню "Плагины" в WordPress.
3.  **Создание папки `languages`:**
    * Для интернационализации убедитесь, что в корневой директории плагина существует папка `languages` (например, `/wp-content/plugins/wowhead-tooltips/languages/`). Плагин использует `wowhead-tooltips` в качестве своего текстового домена.

### Использование

Плагин предоставляет шорткод `[wowhead]`, который можно вставлять в записи, на страницы или в виджеты WordPress.

#### Базовый синтаксис

`[wowhead id="строка_параметров_сущности"]`

#### Атрибуты шорткода

Шорткод принимает несколько атрибутов для настройки его поведения и внешнего вида:

* **`id`** (обязательный):
    * Строка, содержащая информацию о сущности Wowhead и ее специфические параметры.
    * Параметры внутри этой строки разделяются амперсандом (`&`).
    * **Тип сущности и ID:** Должен включать тип сущности и ее числовой ID (например, `item=19019`, `spell=33786`, `quest=10129`).
    * **`domain`** (необязательный, внутри строки `id`): Указывает язык и/или версию игры для всплывающей подсказки.
        * Примеры: `domain=ru` (русский Retail), `domain=classic` (английский Classic), `domain=de.wotlk` (немецкий WotLK Classic).
        * Если опущен, плагин пытается использовать язык сайта WordPress для Retail Wowhead. Если он не поддерживается, по умолчанию используется английский Retail.
        * Плагин пытается исправить распространенные ошибки формата, такие как `версия.язык`, приводя их к ожидаемому `язык.версия`.
    * **Дополнительные параметры Wowhead:** Могут быть включены другие параметры, распознаваемые Wowhead (например, `gems=...`, `ench=...`, `pcs=...` для предметов).
* **`name`** (необязательный):
    * Устанавливает пользовательский текст для отображаемой ссылки.
    * Пример: `name="Мой потрясающий предмет"`
    * Если не указан, плагин попытается получить официальное название через XML-фид Wowhead. Если это не удастся, будет использовано общее имя (например, "Предмет 19019").
    * **Примечание:** Использование этого атрибута установит глобальную опцию `renameLinks` в `whTooltips` в `false` для страницы, предотвращая перезапись пользовательских названий ссылок скриптом Wowhead.
* **`color`** (необязательный):
    * Устанавливает цвет текста ссылки.
    * Принимает CSS-названия цветов (например, `color="green"`), HEX-коды (например, `color="#FF8C00"`) или имя пользовательского CSS-класса, определенного в стилях вашей темы (например, `color="my-custom-link-class"`).
    * **Примечание:** Использование этого атрибута (или `style`) установит глобальную опцию `colorLinks` в `whTooltips` в `false` для страницы, предотвращая применение стандартных цветов качества скриптом Wowhead.
* **`style`** (необязательный):
    * Применяет пользовательские инлайн CSS-стили к ссылке.
    * Пример: `style="font-weight: bold; text-decoration: underline;"`
    * Использование этого атрибута также устанавливает `colorLinks: false` глобально для страницы.

**Важное примечание по иконкам:**
В этой версии плагина размер иконок (`iconSize`) для всех всплывающих подсказок Wowhead на странице зафиксирован как `'tiny'`. Атрибут `icon` в шорткоде **игнорируется** для установки `iconSize`. Иконки автоматически добавляются скриптом Wowhead `power.js`, если опция `iconizeLinks` в объекте `whTooltips` включена (что является поведением по умолчанию).

#### Как плагин определяет глобальные опции `whTooltips`

Плагин настраивает глобальный JavaScript-объект `whTooltips` на основе атрибутов, использованных в *любом* из шорткодов `[wowhead]` на текущей странице:

* `iconSize`: Всегда устанавливается в `'tiny'`.
* `renameLinks`: Устанавливается в `false`, если хотя бы один шорткод на странице использует атрибут `name`; в противном случае `true`.
* `colorLinks`: Устанавливается в `false`, если хотя бы один шорткод на странице использует атрибут `color` или `style`; в противном случае `true`.
* `iconizeLinks`: Всегда устанавливается в `true`.

#### Примеры

##### 1. Основные типы объектов
(Язык/версия по умолчанию: Retail English, если язык сайта не является поддерживаемым языком Wowhead для Retail)

* **Предмет:**
    `[wowhead id="item=19019"]`
* **Заклинание:**
    `[wowhead id="spell=33786"]`
* **Задание:**
    `[wowhead id="quest=10129"]`

##### 2. Указание языка (пример для Retail)

* **Русский:**
    `[wowhead id="item=19019&domain=ru"]`
* **Немецкий:**
    `[wowhead id="item=19019&domain=de"]`

##### 3. Указание версии игры (язык по умолчанию: английский)

* **World of Warcraft Classic:**
    `[wowhead id="item=19019&domain=classic"]`
* **WotLK Classic:**
    `[wowhead id="item=49623&domain=wotlk"]`

##### 4. Комбинация версии игры и языка

* **Русский Classic:**
    `[wowhead id="item=19019&domain=ru.classic"]`
* **Немецкий WotLK Classic:**
    `[wowhead id="item=49623&domain=de.wotlk"]`

##### 5. Использование атрибутов `name`, `color`, `style`

* **Пользовательское название ссылки:**
    `[wowhead id="item=19019&domain=ru" name="Легендарный Меч!"]`
* **Зеленый цвет ссылки:**
    `[wowhead id="item=19019&domain=ru" color="green"]`
* **Цвет ссылки через HEX и жирный шрифт:**
    `[wowhead id="item=19019&domain=ru" color="#FF8C00" style="font-weight: bold;"]`
* **Пользовательский CSS-класс для ссылки:**
    `[wowhead id="item=19019&domain=ru" color="my-custom-wowhead-link"]`
    *(Вам нужно будет определить стиль для `.my-custom-wowhead-link` в CSS вашей темы).*

##### 6. Расширенные параметры Wowhead внутри `id`

Вы можете передавать дополнительные специфичные для Wowhead параметры прямо в строке `id`. Для предметов это могут быть камни, зачарования, бонусы от комплекта и т.д.

* **Предмет с камнем и зачарованием (Пример):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`

##### 7. Пустые или некорректные шорткоды

* **Пустой шорткод `[wowhead]` или `[wowhead id=""]`:**
    Отобразит простую ссылку на главную страницу Wowhead (с учетом языка сайта, если применимо).
* **Шорткод с несуществующим ID или некорректными данными:**
    `[wowhead id="item=999999999"]`
    Отобразит ссылку "Wowhead.com (unavailable)" красного цвета.
* **Шорткод только с доменом:**
    `[wowhead id="domain=ru.classic"]`
    Отобразит ссылку на главную страницу Wowhead для указанного домена.

### Приоритет определения языка и версии

1.  **Явное указание в шорткоде:** Параметр `domain` в атрибуте `id` имеет наивысший приоритет.
2.  **Язык сайта WordPress:** Если `domain` не указан, плагин использует язык вашего сайта WordPress для Retail-версии Wowhead (если этот язык поддерживается).
3.  **Английский по умолчанию:** Если ни один из вышеуказанных методов не применим, используется английский язык для Retail-версии Wowhead.

### Кэширование

Плагин кэширует ответы от XML-фидов Wowhead (названия объектов, статус 404) на 12 часов для успешных запросов и на 1 час для ошибок 404, используя WordPress Transients API. Это помогает уменьшить количество запросов к серверам Wowhead и ускоряет загрузку ваших страниц.

### Устранение неполадок

* **Всплывающие подсказки не появляются / Иконки отсутствуют:**
    1.  Убедитесь, что плагин активен.
    2.  Убедитесь, что на странице есть хотя бы один шорткод `[wowhead]`.
    3.  Проверьте исходный HTML-код страницы (Ctrl+U или Cmd+Option+U): строки `const whTooltips = {...};` и `<script src="https://wow.zamimg.com/widgets/power.js"></script>` должны присутствовать ближе к концу страницы, перед `</body>`.
    4.  Проверьте консоль JavaScript вашего браузера (F12 -> Console) на наличие ошибок. Любые ошибки JS могут помешать инициализации скриптов Wowhead.
    5.  Убедитесь, что файл `footer.php` вашей темы содержит вызов `<?php wp_footer(); ?>` перед закрывающим тегом `</body>`.
    6.  Попробуйте временно отключить другие плагины и переключиться на стандартную тему WordPress (например, Twenty Twenty-Three) для исключения конфликтов.
    7.  Очистите все уровни кэша (браузера, плагинов WordPress, серверного кэша, CDN).
* **Некорректный язык или версия игры:** Внимательно проверьте значение параметра `domain` в вашем шорткоде.
* **Ошибки PHP:** Если плагин не активируется или вызывает проблемы, включите отладку WordPress (`WP_DEBUG`, `WP_DEBUG_LOG` в `wp-config.php`), чтобы проверить наличие ошибок PHP в `wp-content/debug.log`.

### Участие в разработке

Мы приветствуем ваш вклад! Вы можете сделать форк репозитория на [GitHub](https://github.com/pro-wow/wowhead-tooltips/), внести изменения и отправить pull-запросы.

### Журнал изменений

#### 3.1.1
* Обновлен заголовок плагина для совместимости с WordPress.org (проверка плагином PCP).
* Добавлены комментарии на английском языке в PHP-код (для версии разработки).
* Установлена версия "Проверено до" WordPress до 6.8.
* Стандартизировано название плагина на "Wowhead Tooltips Shortcode".
* Исправлен `Stable tag` для соответствия версии плагина.
* Сокращено краткое описание для соблюдения лимита в 150 символов.

#### 3.0.0
* Начальная стабильная версия с прямым выводом скриптов Wowhead JS.
* Включено XML-кэширование через Transients API.
* `iconSize` глобально зафиксирован на 'tiny'.
* `renameLinks` и `colorLinks` определяются глобально на основе атрибутов шорткода.
* Комплексная обработка доменов и языков.

---
<a id="chinese-readme"></a>

## Wowhead Tooltips Shortcode (中文 - 简体)

"Wowhead Tooltips Shortcode" 插件可让您轻松地将 Wowhead 丰富的交互式浮动信息集成到您的 WordPress 文章和页面中。通过使用简单的短代码 `[wowhead]`，您可以链接到《魔兽世界》中各种游戏版本的各种游戏实体，如物品、法术、任务、成就、NPC 和区域，包括正式服、经典怀旧服、巫妖王之怒怀旧服、大地的裂变经典版和熊猫人之谜经典版/Remix。

该插件设计灵活，支持多种语言（例如中文简体/繁体、英语、俄语、德语、法语、韩语）和游戏版本。它会根据您的 WordPress 设置智能确定语言和版本，或允许通过短代码进行明确控制。主要功能包括设置自定义链接文本、自定义链接颜色和样式，以及自动显示图标（为保持一致性，大小固定为 'tiny'）。为确保准确性和性能，官方实体名称会从 Wowhead 的 XML Feed 中获取，并使用 WordPress Transients API 在本地缓存。

此工具非常适合粉丝网站、公会页面或任何希望在其内容中直接提供来自 Wowhead 的丰富上下文信息的内容创建者。

### 功能 (Features)

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

### 安装 (Installation)

1.  **通过 WordPress 后台上传：**
    * 从 [GitHub 仓库](https://github.com/pro-wow/wowhead-tooltips/) 或 [pro-wow.com](https://pro-wow.ru/?utm_source=github&utm_medium=organic&utm_campaign=wowhead-tooltips) 下载插件 ZIP 文件。
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

### 使用方法 (Usage)

该插件提供 `[wowhead]` 短代码，可以插入到 WordPress 文章、页面或小工具中。

#### 基本语法

`[wowhead id="实体参数字符串"]`

#### 短代码属性

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

#### 插件如何确定 `whTooltips` 全局选项

插件会根据当前页面上*任何* `[wowhead]` 短代码中使用的属性来配置全局 `whTooltips` JavaScript 对象：

* `iconSize`: 始终设置为 `'tiny'`。
* `renameLinks`: 如果页面上的任何短代码使用 `name` 属性，则设置为 `false`；否则为 `true`。
* `colorLinks`: 如果页面上的任何短代码使用 `color` 或 `style` 属性，则设置为 `false`；否则为 `true`。
* `iconizeLinks`: 始终设置为 `true`。

#### 示例

##### 1. 基本对象类型
(默认语言/版本：正式服英语，除非网站语言是 Wowhead 支持的正式服语言)

* **物品:**
    `[wowhead id="item=19019"]`
* **法术:**
    `[wowhead id="spell=33786"]`
* **任务:**
    `[wowhead id="quest=10129"]`

##### 2. 指定语言 (正式服示例)

* **简体中文:**
    `[wowhead id="item=19019&domain=cn"]`
* **德语:**
    `[wowhead id="item=19019&domain=de"]`

##### 3. 指定游戏版本 (默认语言：英语)

* **魔兽世界经典怀旧服:**
    `[wowhead id="item=19019&domain=classic"]`
* **巫妖王之怒怀旧服:**
    `[wowhead id="item=49623&domain=wotlk"]`

##### 4. 组合游戏版本和语言

* **简体中文经典怀旧服:**
    `[wowhead id="item=19019&domain=cn.classic"]`
* **德语巫妖王之怒怀旧服:**
    `[wowhead id="item=49623&domain=de.wotlk"]`

##### 5. 使用 `name`, `color`, `style` 属性

* **自定义链接名称:**
    `[wowhead id="item=19019&domain=cn" name="传说之剑!"]`
* **绿色链接颜色:**
    `[wowhead id="item=19019&domain=cn" color="green"]`
* **通过 HEX 和粗体设置链接颜色:**
    `[wowhead id="item=19019&domain=cn" color="#FF8C00" style="font-weight: bold;"]`
* **为链接自定义 CSS 类:**
    `[wowhead id="item=19019&domain=cn" color="my-custom-wowhead-link"]`
    *(您需要在主题的 CSS 中为 `.my-custom-wowhead-link` 定义样式)。*

##### 6. `id` 内的高级 Wowhead 参数

您可以直接在 `id` 字符串中传递其他 Wowhead 特定参数。对于物品，这些可以包括宝石、附魔、套装奖励等。

* **带宝石和附魔的物品 (示例):**
    `[wowhead id="item=25697&domain=tbc&gems=23121&ench=2647"]`

##### 7. 空或无效的短代码

* **空短代码 `[wowhead]` 或 `[wowhead id=""]`:**
    将显示一个指向 Wowhead 主页的简单链接 (如果适用，会考虑网站语言)。
* **ID 不存在或数据无效的短代码:**
    `[wowhead id="item=999999999"]`
    将以红色显示链接 "Wowhead.com (unavailable)"。
* **仅包含域的短代码:**
    `[wowhead id="domain=cn.classic"]`
    将显示指向指定域的 Wowhead 主页的链接。

### 语言和版本确定优先级

1.  **在短代码中明确指定:** `id` 属性中的 `domain` 参数具有最高优先级。
2.  **WordPress 网站语言:** 如果未指定 `domain`，插件将使用您 WordPress 网站的语言用于 Wowhead 正式服 (如果支持该语言)。
3.  **默认英语:** 如果以上方法均不适用，则使用 Wowhead 正式服的英语。

### 缓存

该插件会使用 WordPress Transients API 缓存来自 Wowhead XML Feed 的响应 (对象名称、404 状态)，成功请求缓存 12 小时，404 错误缓存 1 小时。这有助于减少对 Wowhead 服务器的请求次数并加快您页面的加载速度。

### 故障排除

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

### 贡献 (Contributing)

欢迎贡献！请随时在 [GitHub](https://github.com/pro-wow/wowhead-tooltips/) 上 fork 本仓库，进行更改并提交拉取请求 (pull requests)。

### 更新日志 (Changelog)

#### 3.1.1
* 更新插件头部信息以兼容 WordPress.org (PCP 插件检查)。
* 在 PHP 代码中添加了英文注释 (用于开发版本)。
* 将 WordPress "Tested up to" 版本更新为 6.8。
* 将插件名称标准化为 "Wowhead Tooltips Shortcode"。
* 修正 `Stable tag` 以匹配插件版本。
* 缩短了简短描述以符合 150 个字符的限制。

#### 3.0.0
* Wowhead JS 脚本直接输出的初始稳定版本。
* 通过 Transients API 启用 XML 缓存。
* `iconSize` 全局固定为 'tiny'。
* `renameLinks` 和 `colorLinks` 根据短代码属性全局确定。
* 全面的域和语言处理。
