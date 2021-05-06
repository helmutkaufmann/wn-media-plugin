# Winter CMS Media Plugin
Improved media handling for Winter CMS, including:
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

### iresize
Replacement for the Twig built-in resize function. If no *extension* option is specified, 
it will return the image in *webp* format if the browser is able to process it.
```
{{ image | iresize(150, 100)}}
```

The filter supports Winter's *filter* options, please, see the respective documentation.
You can also use many (but not yet all) [Intervention](http://image.intervention.io) functions by specifying an 
option called *manipulation*. Multiple filters can be daisy-changed as per the following example. 
```
{{ image | iresize(150, 100, { manipulation: "blur(1)->colorize(-100, 0, 0)->flip('v')" }}
```
This example would first resize the image to 150x100px, add a 1% blur filter to off take all red out of the image
and finally flip it vertically.

While [Intervention](http://image.intervention.io) holds the current description of all available functions,
here are some of the probably most common ones:

##### blur(amount = 1)
Apply a gaussian blur filter with a optional amount on the current image. Use values between 0 and 100.

##### brightness(amount)
Changes the brightness of the current image by the given level. Use values between -100 for min. brightness 0
for no change and +100 for max. brightness.

##### colorize(red, green, blue)
Change the RGB color values of the current image on the given channels red, green and blue. 
The input values are normalized so you have to include parameters from 100 for maximum color value 0 for no change 
and -100 to take out all the certain color on the image.

##### flip(mode)
Mirror the current image horizontally or vertically by specifying the mode.
Specify the mode the image will be flipped. You can set "h" for horizontal (default) or "v" for vertical flip.

##### gamma(correction)
Performs a gamma correction operation on the current image.

##### greyscale()
Turns image into a greyscale version.

##### invert()
Reverses all colors of the current image.

##### limitColors(count, [matte])
Method converts the existing colors of the current image into a color table with a given maximum count of colors. 
The function preserves as much alpha channel information as possible and blends transarent pixels against a optional matte color.

##### opacity(transparency)
Set the opacity in percent of the current image ranging from 100% for opaque and 0% for full transparency.

Note: Performance intensive on larger images. Use with care.

##### rotate(angle, [background color])
Rotate the current image counter-clockwise by a given angle. Optionally define a background color for the uncovered 
zone after the rotation.

The rotation angle in degrees to rotate the image counter-clockwise.

A background color for the uncovered zone after the rotation. The background color can be passed in different color formats. D
efault: #ffffff, transparent if supported by the output format

##### sharpen([amount])
harpen current image with an optional amount. Use values between 0 and 100. Default: 10.

## Limitations
This is a production version of the plugin. Not all options of the original resize function have yet been implemented, 
in particular *mode, offset* and *sharpen*. Implementation will follow in the very near future.

## A word of caution
[Intervention](http://image.intervention.io) is rather resource-intensive. As a consequence:
- Apply [Intervention](http://image.intervention.io) carefully.
- For large files (at the moment, 8 megapixels), the initial resize of an image is handled by Winter's internal resizer. At the moment, 
this results in the original image being opened, resized, compressed, stored and then re-opened to apply any filers of the
[Intervention](http://image.intervention.io) library before the final result is again compressed and saved to disk.
This approach results generally in faster processing but re-compresses images twice, which implies a (slight) loss of quality.

Further optimisations are planned.
