# Winter CMS Media Plugin
Improved media handling for [WinterCMS](https://wintercms.com), including:
- Winter CMS image resizer replacement: Rendering modern image formats where they can be displayed by the browsers.
- Advanced image manipulation using the [Intervention](http://image.intervention.io) library.

## Installation
#### composer
```
composer require mercator/wn-media-plugin
php artisan winter:up
```

#### github
```
git clone git@github.com:helmutkaufmann/wn-media-plugin.git
```

## Twig Filters

### iresize([width=null], [height=null], [filters=null], [extension=null], [quality=null])
or
### ifilter([width=null], [height=null], [filters], [extension], [quality])
These are identical filters to resize images and apply filters to them.

If *width* and *height* are specified, the image is resized **before** applying any *filters*. This significantly
boosts performance.

To keep the aspect ration of an image, set **either** *width* or *height* to *zero (0)*.

Setting either *width* or *height* to *null* leaves this dimension untouched and results typically in distorted
images. Examples:

```
{{ image | iresize(300, null) }}
```
resize only the width of the image. Likewise

```
{{ image | ifilter(null, 200) }}
```
resize only the height of the image


You can apply [Intervention](https://image.intervention.io/v2) *filters* to the image.
See the Intervention [website](https://image.intervention.io/v2) for information about the
available filters.

You can specify the filters as a Twig array:

```
{{ image | iresize(150, 100, [["blur", 1],["colorize", -100, 0, 0], ["flip"]])
```

Alternatively (but that this approach will be deprecated in the future) filters can
be specified as a string, such as
```
{{ image | iresize(150, 100, "blur(1)->colorize(-100, 0, 0)->flip('v')" }}
```

Both examples would first resize the image to 150x100px, add a 1% blur filter, remove all red from the image
and finally flip the image vertically.

By default, the plugin will serve an optimal image format depending on the rendering capabilities of the browser.
If the optional parameter *extension* is specified, the plugin will convert the image to  the image format corresponding
to the extension. Valid extensions are *jpg, gif, tiff* and *webp*. If the respective image format supports
compression, the *quality* can be explicitly set.

Consequence: If you specify an explicit *exentsion*,
the browser might not be able to display it due to lack of functionality (e.g. *webp* images on certain Safari versions).


## Twig Functions
### exif([key])
Returns an array (key/value) of all EXIF meta data from image. Alternatively, if a *key* is set, it returns a string
with the value for that EXIF key. If no data is found, null is returned.

Example
```
exif("Model")
```
returns the camera's model name if set.

### iptc([key])
Returns an array (key/value) of all IPTC meta data from image. Alternatively, if a *key* is set, it returns a string
with the value for that IPTC key. If no data is found, null is returned.

## Limitations and next steps
This is a pre-production version of the plugin. Winter's original resize function has not been fully implemented,
in particular *mode*. Implementation will follow in the very near future.

Error handling must still be implemented, in particular failure to apply a filter, in particular when a filter is
incorrectly written (e.g. *blurs* instead of *blur*).

## A word of caution
[Intervention](http://image.intervention.io) is rather resource-intensive. As a consequence:
- Apply [Intervention](http://image.intervention.io) filters carefully.
- For large files (at the moment, 8 megapixels), the initial resize of an image is handled by Winter's internal resizer. At the moment,
this results in the original image being opened, resized, compressed, stored and then re-opened to apply any filers of the
[Intervention](http://image.intervention.io) library before the final result is again compressed and saved to disk.
This approach results generally in faster processing but re-compresses images twice, which implies a (slight) loss of quality.
