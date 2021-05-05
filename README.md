# Winter CMS Media Plugin
Improved media handling for Winter CMS. In particular, a Winter CMS image resizer replacement, 
providing next generation image formats where they can be displayed by the requester's browsers.

## Installation
#### composer
```
composer require mercator/wn-media-plugin
```

#### github
```
git clone git@github.com:helmutkaufmann/wn-media-plugin.git
```

## Twig Filters

### resize
Relacement for the Twig built-in resize function. If no *extension* option is specified, 
it will return the image in *webp* format if the browser is able to process it. See Winter's *filter* 
documentation for applicable options.

```
{{ image | igresize }}
```



## Twig Functions
### acceptsFormat 
Tests if a browser is able to render a specific image format. It takes *jpg, jpeg, gif, web, avif* and *png* 
as parameters and returns *true* or *false*.

```
{% if acceptsFormat('avif') %}
    <h1>Your browser supports <i>avif</i> file type</h1>
{% endif %}

{% if acceptsFormat('webp') %}
    <h1>Your browser supports <i>webp</i> file type</h1>
{% endif %}
<h1>Hello There!</h1>
```
