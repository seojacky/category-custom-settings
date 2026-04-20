# Category Custom Settings

WordPress-плагин для добавления пользовательских полей к рубрикам (`category`) в админке и удобного вывода этих значений на сайте.

## Что делает плагин

Плагин добавляет дополнительные настройки на страницу редактирования категории в WordPress.

### Поля, которые поддерживаются

#### Для всех категорий
- `cat_add_description` — дополнительное описание под листингом

#### Только для родительских категорий
- `cat_version` — версия игры
- `cat_video` — ID YouTube-видео
- `bg_category` — цвет фона категории
- `cat_rss` — RSS новостей
- `cat_leftlink` — ссылка слева
- `cat_rightlink` — ссылка справа
- `cat_adsense1` — код Adsense
- `cat_headerbanner` — HTML-код баннера в шапке

## Основные особенности

- использует современный способ хранения данных через `term_meta`
- работает только с таксономией `category`
- расширенные поля показывает только для родительских категорий
- защищает сохранение через `nonce`
- проверяет права пользователя
- выполняет санитизацию данных в зависимости от типа поля
- содержит готовые функции для получения и вывода значений на фронтенде

---

## Установка

1. Создайте папку плагина:

```text
category-custom-settings
```

2. Внутрь папки поместите файл:

```text
category-custom-settings.php
```

3. Загрузите папку в каталог:

```text
/wp-content/plugins/
```

4. Активируйте плагин в админке WordPress:
   `Плагины → Category Custom Settings → Активировать`

---

## Где находятся поля в админке

После активации откройте:

```text
Записи → Рубрики → Изменить категорию
```

На странице редактирования рубрики появятся дополнительные поля.

---

## Как плагин хранит данные

Плагин сохраняет значения в `term meta`, то есть использует стандартный механизм WordPress:

- `get_term_meta()`
- `update_term_meta()`
- `delete_term_meta()`

Это лучше и надёжнее, чем старый способ хранения через `get_option("category_ID")`.

---

## Как работает логика показа полей

### Всегда показывается
- Дополнительное описание под листингом

### Показываются только для родительских категорий
Если у категории `parent = 0`, дополнительно показываются:
- версия игры
- видео
- RSS
- ссылки
- Adsense
- HTML баннер

Если категория дочерняя, эти поля скрываются.

---

## Безопасность

Плагин использует несколько уровней защиты:

- проверка `current_user_can('manage_categories')`
- проверка `nonce`
- санитизация текста через `sanitize_text_field()`
- санитизация URL через `esc_url_raw()`
- ограниченная очистка HTML через `wp_kses_post()`
- отдельная обработка кода баннеров и рекламных блоков

---

## Функции для использования в теме

Плагин добавляет две полезные функции.

### 1. Получить значение поля

```php
ccs_get_category_field($field_name, $term_id = null);
```

#### Параметры
- `$field_name` — имя поля
- `$term_id` — ID категории, необязательный параметр

Если `$term_id` не передан, функция попытается взять текущую категорию из `get_queried_object()`.

#### Возвращает
Строку со значением поля или пустую строку.

#### Пример

```php
$desc = ccs_get_category_field('cat_add_description');
```

---

### 2. Вывести значение поля

```php
ccs_the_category_field($field_name, $term_id = null);
```

Функция сразу выводит значение и сама применяет подходящее экранирование в зависимости от типа поля.

#### Пример

```php
<?php ccs_the_category_field('cat_version'); ?>
```

---

## Примеры использования

## 1. Вывести дополнительное описание на странице категории

Обычно это вставляют в `category.php`, `archive.php` или в хук темы.

```php
<?php
if (is_category()) {
    $desc = ccs_get_category_field('cat_add_description');

    if ($desc !== '') {
        echo '<div class="category-extra-description">';
        echo wp_kses_post($desc);
        echo '</div>';
    }
}
?>
```

---

## 2. Вывести версию игры

```php
<?php
$version = ccs_get_category_field('cat_version');

if ($version !== '') {
    echo '<div class="category-version">';
    echo 'Версия игры: ' . esc_html($version);
    echo '</div>';
}
?>
```

---

## 3. Вывести YouTube-видео по ID

Поле `cat_video` должно содержать только ID видео, например:

```text
a--RlPV3ZBo
```

Пример вывода:

```php
<?php
$video = ccs_get_category_field('cat_video');

if ($video !== '') {
    echo '<div class="category-video">';
    echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr($video) . '" frameborder="0" allowfullscreen></iframe>';
    echo '</div>';
}
?>
```

---

## 4. Вывести ссылку на RSS

```php
<?php
$rss = ccs_get_category_field('cat_rss');

if ($rss !== '') {
    echo '<a href="' . esc_url($rss) . '" target="_blank" rel="noopener noreferrer">RSS новостей</a>';
}
?>
```

---

## 5. Вывести левую и правую ссылки

```php
<?php
$left = ccs_get_category_field('cat_leftlink');
$right = ccs_get_category_field('cat_rightlink');

if ($left !== '' || $right !== '') {
    echo '<div class="category-links">';

    if ($left !== '') {
        echo '<a class="category-link-left" href="' . esc_url($left) . '">Ссылка слева</a>';
    }

    if ($right !== '') {
        echo '<a class="category-link-right" href="' . esc_url($right) . '">Ссылка справа</a>';
    }

    echo '</div>';
}
?>
```

---

## 6. Вывести HTML-баннер в шапке категории

```php
<?php
$banner = ccs_get_category_field('cat_headerbanner');

if ($banner !== '') {
    echo '<div class="category-header-banner">';
    echo $banner;
    echo '</div>';
}
?>
```

> Внимание: это поле предназначено для доверенного HTML-кода. Используйте его только если понимаете, какой код туда вставляете.

---

## 7. Вывести Adsense-код

```php
<?php
$adsense = ccs_get_category_field('cat_adsense1');

if ($adsense !== '') {
    echo '<div class="category-adsense">';
    echo $adsense;
    echo '</div>';
}
?>
```

---

## 8. Вывести данные для конкретной категории по ID

Если нужно получить данные не для текущей страницы категории, а для конкретной рубрики:

```php
<?php
$term_id = 123;

echo esc_html(ccs_get_category_field('cat_version', $term_id));
?>
```

---

## 9. Пример готового блока для `category.php`

```php
<?php
if (is_category()) {
    $desc = ccs_get_category_field('cat_add_description');
    $version = ccs_get_category_field('cat_version');
    $video = ccs_get_category_field('cat_video');
    $banner = ccs_get_category_field('cat_headerbanner');

    if ($banner !== '') {
        echo '<div class="category-header-banner">' . $banner . '</div>';
    }

    if ($version !== '') {
        echo '<div class="category-version">Версия игры: ' . esc_html($version) . '</div>';
    }

    if ($video !== '') {
        echo '<div class="category-video">';
        echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr($video) . '" frameborder="0" allowfullscreen></iframe>';
        echo '</div>';
    }
}
?>

<?php if (have_posts()) : ?>
    <div class="category-posts">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            </article>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php
if (is_category()) {
    $desc = ccs_get_category_field('cat_add_description');

    if ($desc !== '') {
        echo '<div class="category-extra-description">';
        echo wp_kses_post($desc);
        echo '</div>';
    }
}
?>
```

---

## Какие поля лучше выводить с экранированием

### Обычный текст
Используйте:

```php
esc_html()
```

Подходит для:
- `cat_version`
- `bg_category`

### URL
Используйте:

```php
esc_url()
```

Подходит для:
- `cat_rss`
- `cat_leftlink`
- `cat_rightlink`

### HTML-текст
Используйте:

```php
wp_kses_post()
```

Подходит для:
- `cat_add_description`

### Доверенный HTML/JS-код
Используйте осторожно:

```php
echo $banner;
```

Подходит для:
- `cat_adsense1`
- `cat_headerbanner`

---

## Где лучше выводить эти данные

Это зависит от вашей темы. Обычно вывод добавляют в один из файлов:

- `category.php`
- `archive.php`
- `taxonomy.php`

Если используется GeneratePress или другая тема с хуками, удобнее выводить данные через хуки или child theme, а не править шаблоны напрямую.

---

## Рекомендации по доработке

При желании плагин можно расширить:

- добавить поля и на страницу создания категории, а не только редактирования
- добавить поддержку других таксономий
- вынести настройки в отдельный класс рендера
- добавить миграцию старых данных из `get_option("category_ID")`
- добавить шорткоды
- добавить виджет или блок Gutenberg для вывода данных категории

---

## Ограничения текущей версии

- плагин добавляет поля только на страницу редактирования существующей категории
- не добавляет поля на форму создания новой категории
- работает только с таксономией `category`
- старые данные из legacy-кода через `get_option("category_ID")` автоматически не переносятся

---

## Пример структуры плагина

```text
category-custom-settings/
└── category-custom-settings.php
```

---

## Лицензия

GPL2+

---

## Авторская заметка

Плагин создан как современная и более безопасная замена старому коду, который хранил данные категорий через `options` вида `category_ID`. Новая реализация использует `term_meta`, проверку прав и безопасное сохранение данных.
