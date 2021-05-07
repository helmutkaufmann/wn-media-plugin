# Winter CMS Media Plugin
Improved media handling for [WinterCMS](https://wintercms.com), including:
- Winter CMS image resizer replacement: Rendering modern image formats where they can be displayed by the browsers.
- Advanced image manipulation using the [Intervention](http://image.intervention.io) library.

Note that this is a pre-production version of the plugin to demonstrate capabilities and should nto be used in a
production library. To see it in action, see the galleries on [mercator dot li](https://mercator.li).

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

### iresize([width], [height], [filters], [extension], [quality]) 
### ifilter([width], [height], [filters], [extension], [quality]) 
These identical filters resize images and apply filters to an image.

If *width* and *height* are specified, the image is before applying any *filters*. 
To keep the aspect ration of an image, set **either** *width* or *height* to *null*.

The function can apply [Intervention](http://image.intervention.io) *filters* to the image. 
Filters can be specified as a string, such as 

```
{{ image | iresize(150, 100, "blur(1)->colorize(-100, 0, 0)->flip('v')" }}
```

This example would first resize the image to 150x100px, add a 1% blur filter, remove all red from the image
and finally flip the image vertically.

Alternatively, you can specify the filters as a Twig array:

```
{{ image | iresize(150, 100, [["blur", 1],["colorize", -100, 0, 0], ["flip"]])
```

Note that the *flip* filter was called without the optional parameter. In this case, the plugin honored the
"v" as the default option.

By default, the plugin will serve an optimal image format depending on the rendering capabilities of the browser.
If the optional parameter *extension* is specified, the plugin will convert the image to  the image format corresponding
to the extension. Valid extensions are *jpg, gif, tiff* and *webp*. If the respective image format supports
compression, the *quality* can be explicitly set. 

Consequence: If you specify an explicit *exentions*, 
the browser might not be able to display it due to lack of functionality (e.g. webp images on certain Safari versions).

The following [Intervention](http://image.intervention.io) filters are currently available. See 
[Intervention](http://image.intervention.io) for additional information.

##### blur([amount = 1])
Apply a gaussian blur filter with a optional amount on the current image. Use values between 0 and 100.

##### brightness(amount)
Changes the brightness of the current image by the given level. Use values between -100 for min. brightness 0
for no change and +100 for max. brightness.

##### colorize(red, green, blue)
Change the RGB color values of the current image on the given channels red, green and blue. 
The input values are normalized so you have to include parameters from 100 for maximum color value 0 for no change 
and -100 to take out all the certain color on the image.

##### contrast (level)
Changes the contrast of the current image by the given level. Use values between -100 for min. contrast 0 for no 
change and +100 for max. contrast.

##### crop(width, height, [x=0, y=0])
Cut out a rectangular part of the current image with given width and height. Define optional x,y coordinates to 
move the top-left corner of the cutout to a certain position.

##### flip([mode="v"])
Mirror the current image horizontally or vertically by specifying the mode.
Specify the mode the image will be flipped. You can set "h" for horizontal (default) or "v" for vertical flip.

##### gamma(correction)
Performs a gamma correction operation on the current image.

##### greyscale()
Turns image into a greyscale version.

##### heighten(height)
Resizes the current image to new height, constraining aspect ratio. 

##### invert()
Reverses all colors of the current image.

##### limitColors(count, [matte])
Method converts the existing colors of the current image into a color table with a given maximum count of colors. 
The function preserves as much alpha channel information as possible and blends transarent pixels against a optional matte color.

##### opacity(transparency)
Set the opacity in percent of the current image ranging from 100% for opaque and 0% for full transparency.

Note: Performance intensive on larger images. Use with care.

##### rotate(angle, [background color='#ffffff'])
Rotate the current image counter-clockwise by a given angle. Optionally define a background color for the uncovered 
zone after the rotation.

The rotation angle in degrees to rotate the image counter-clockwise.

A background color for the uncovered zone after the rotation. The background color can be passed in different color formats. D
efault: #ffffff, transparent if supported by the output format

##### sharpen(amount=10)
harpen current image with an optional amount. Use values between 0 and 100. Default: 10.

##### widen(width)
Resizes the current image to new width, constraining aspect ratio. 

## Twig Functions
### exif([key])
Returns an array (key/value) of all EXIF meta data from image. Alternatively, if a *key* is set, it returns a string
with the value for that EXIF key. If no data is found, null is returned.

Example
```
exif("Model)
```
returns the camera's model name if set.

### iptc([key])
Returns an array (key/value) of all IPTC meta data from image. Alternatively, if a *key* is set, it returns a string
with the value for that IPTC key. If no data is found, null is returned.

## Limitations
This is a production version of the plugin. Not all options of the original resize function have yet been implemented, 
in particular *mode, offset* and *sharpen*. Implementation will follow in the very near future.

## A word of caution
[Intervention](http://image.intervention.io) is rather resource-intensive. As a consequence:
- Apply [Intervention](http://image.intervention.io) filters carefully.
- For large files (at the moment, 8 megapixels), the initial resize of an image is handled by Winter's internal resizer. At the moment, 
this results in the original image being opened, resized, compressed, stored and then re-opened to apply any filers of the
[Intervention](http://image.intervention.io) library before the final result is again compressed and saved to disk.
This approach results generally in faster processing but re-compresses images twice, which implies a (slight) loss of quality.

Further optimisations are planned.
