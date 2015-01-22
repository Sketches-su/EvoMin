# EvoMin - CSS/JS minification snippet for MODX Evolution

**Русскоязычное описание** [здесь](README_ru.md).

This snippet uses [CssMin](http://code.google.com/p/cssmin/) for minifying CSS and [JSMinPlus](http://crisp.tweakblogs.net/blog/cat/716) for JavaScript. These two have been proven their reliability in many different deployments. Pure PHP minifiers without external dependencies are chosen to make the snippet usage and migration as easy as possible.

Features:
* automatic fixing relative URLs inside CSS files (using [UrlPrefix](http://code.google.com/p/cssmin/issues/detail?id=30) plugin);
* working around the end-user browser caching issues ("...do our clients also have to press Ctrl+F5?") by appending a special hash to the resulting URL and by appending timestamps to the URLs in CSS;
* option for creating gzipped versions of minified files for compatibility with Nginx's [ngx_http_gzip_static_module](http://nginx.org/en/docs/http/ngx_http_gzip_static_module.html) or [similar](https://gagor.pl/2013/12/apache-precompressing-static-files-with-gzip/) solution for Apache.
* option for bypassing minification for debugging purposes.

## Installation

Clone this repo and copy its contents into `assets/snippets/evomin/`. (If you don't have Git handy, hit the "Download ZIP" button on the right.) Then create a snippet called `EvoMin` and paste:

```php
<?php
return require MODX_BASE_PATH.'assets/snippets/evomin/evomin.php';
?>
```

## Usage

Just copy, paste into your template and customize:

```html
<link rel="stylesheet" href="[(base_url)][[EvoMin? &rel=`assets/templates/mylayout/` &from=`css/reset.css,css/styles.css` &to=`min/mylayout.css`]]" />
<script type="text/javascript" src="[(base_url)][[EvoMin? &rel=`assets/templates/mylayout/` &from=`js/jquery.min.js,js/scripts.js` &to=`min/mylayout.js`]]"></script>
```

As yoy might have figured out, it's assumed that CSS/JS are in `assets/templates/mylayout/{css,js}/` and the minified versions go to `assets/templates/mylayout/min/`. As a result, when you refresh your website page and hit Ctrl+U you will see something like this:

```html
<link rel="stylesheet" href="/assets/templates/mylayout/min/mylayout.css?c7a628cba22e28eb17b5f5c6ae2a266a" />
<script type="text/javascript" src="/assets/templates/mylayout/min/mylayout.js?32981a13284db7a021131df49e6cd203"></script>
```

Snippet parameters explained:

* `from` - comma-separated list of input files;
* `to` - output file (its extension determines what are we minifying - CSS or JS);
* `rel` (optional, "" by default) - prefix for all paths, both `from` and `to`. Makes your life easier as lots of file paths usually have the same prefix which is boring to type.
* `bypass` (optional) - bypasses minification stage (only combining is made). Added for debugging purposes. Note that in this mode CSS URLs are not fixed!
* `gzip` (optional) - enables creation of gzipped copies of minified files.

## Suggestions

* during development, use non-cached snippet calls (`[!!]`). After the website has been finished, change the snippet calls into being cached (`[[]]`) and use "Clear Cache" button in MODX to force re-minification.
* if possible, put CSS and/or JS inclusion into the bottom of the page for faster loading.

