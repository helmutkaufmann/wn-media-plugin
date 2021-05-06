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

### ```iresize```
Relacement for the Twig built-in resize function. If no *extension* option is specified, 
it will return the image in *webp* format if the browser is able to process it.
```
{{ image | iresize }}
```

The filter supports Winter's *filter* options, please, see the respective documentation.
You can also use [Intervention](http://image.intervention.io) functions.
```
{{ image | iresize(150, 100, { manipulation: "blur(1)->colorize(-100, 0, 0)->flip('v')" }}
```
This would first resize the image to 150x100px, add a 1% blur filter to iff and take all red out of the image

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
