# EvoMin - сниппет минификации CSS/JS для MODX Evolution

Данный сниппет использует [CssMin](http://code.google.com/p/cssmin/) для минификации CSS и [JSMinPlus](http://crisp.tweakblogs.net/blog/cat/716) для JavaScript. Две указанных библиотеки неоднократно показали себя с хорошей стороны. Минификаторы на чистом PHP без внешних зависимостей были выбраны из соображений максимального удобства использования сниппета, переноса его на другой сайт или миграции сайта на другой хостинг.

Возможности:

* автоматическое исправление относительных URL в CSS-файлах (с помощью плагина [UrlPrefix](http://code.google.com/p/cssmin/issues/detail?id=30));
* обход [проблем с кэшированием](http://clfh.org/15421) на стороне пользователя путём добавления к возвращаемому URL уникального хэша, а к URL файлов в CSS - их времени изменения;
* опция создания сжатых gzip'ом копий минифицированных файлов для совместимости с модулем Nginx [ngx_http_gzip_static_module](http://nginx.org/ru/docs/http/ngx_http_gzip_static_module.html) или [аналогичным](https://gagor.pl/2013/12/apache-precompressing-static-files-with-gzip/) решением для Apache.
* опция отключения минификации для отладочных целей.

## Установка

Склонируйте репозиторий и залейте его содержимое в `assets/snippets/evomin/`. (Если под рукой нет Git, жмите "Download ZIP" в правой части страницы.) Затем создайте сниппет `EvoMin` с таким кодом:

```php
<?php
return require MODX_BASE_PATH.'assets/snippets/evomin/evomin.php';
?>
```

## Использование

Просто скопируйте, вставьте в ваш шаблон и допилите:

```html
<link rel="stylesheet" href="[(base_url)][[EvoMin? &rel=`assets/templates/mylayout/` &from=`css/reset.css,css/styles.css` &to=`min/mylayout.css`]]" />
<script type="text/javascript" src="[(base_url)][[EvoMin? &rel=`assets/templates/mylayout/` &from=`js/jquery.min.js,js/scripts.js` &to=`min/mylayout.js`]]"></script>
```

Как вы, возможно, заметили, в примере подразумевается, что CSS/JS лежат в `assets/templates/mylayout/{css,js}/`, а минифицированные версии уходят в `assets/templates/mylayout/min/`. В результате, когда вы обновите страницу и нажмёте Ctrl+U, вы увидите примерно следующее:

```html
<link rel="stylesheet" href="/assets/templates/mylayout/min/mylayout.css?c7a628cba22e28eb17b5f5c6ae2a266a" />
<script type="text/javascript" src="/assets/templates/mylayout/min/mylayout.js?32981a13284db7a021131df49e6cd203"></script>
```

Параметры вызова сниппета:

* `from` - разделённый запятыми список входных файлов;
* `to` - выходной файл (его расширение определяет, что мы обрабатываем - CSS или JS);
* `rel` (не обязательно, по умолчанию "") - префикс всех путей, как `from`, так и `to`. Облегчает труд, потому что пути к файлам обычно начинаются одинаково.
* `bypass` (опционально) - выключает процесс минификации (выполняется только склеивание файлов). Добавлено в отладочных целях. В этом режиме URL в CSS не изменяются!
* `gzip` (опционально) - включает создание копий минифицированных файлов, сжатых gzip'ом.

## Советы

* во время разработки сайта используйте некэшируемые вызовы сниппета (`[!!]`). По окончании разработки замените на кэшируемые (`[[]]`). Пользуйтесь кнопкой "Clear Cache" в админке MODX, чтобы перезапустить минификацию.
* если возможно, помещайте подключение CSS и/или JS в конец страницы, чтобы ускорить её отображение в браузере.

