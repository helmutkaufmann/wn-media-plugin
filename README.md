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

### iresize
Replacement for the Twig built-in resize function. If no *extension* option is specified, 
it will return the image in *webp* format if the browser is able to process it.
```
{{ image | iresize }}
```

The filter supports Winter's *filter* options, please, see the respective documentation.
You can also use many (but not yet all) [http://image.intervention.io](Intervention) functions by specifying an 
option called *manipulation*. Multiple filters can be daisy-changed as per the following example. 
```
{{ image | iresize(150, 100, { manipulation: "blur(1)->colorize(-100, 0, 0)->flip('v')" }}
```
This example would first resize the image to 150x100px, add a 1% blur filter to off take all red out of the image
and finally flip it vertically.

While [http://image.intervention.io](Intervention) holds the current description of all available functions,
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

##### gamma(value)
Performs a gamma correction operation on the current image.

##### greyscale()
Turns image into a greyscale version.

##### invert()
Reverses all colors of the current image.

##### limitColors(count, [matte])
Method converts the existing colors of the current image into a color table with a given maximum count of colors. 
The function preserves as much alpha channel information as possible and blends transarent pixels against a optional matte color.

##### opacity()
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
Note that this functionality is also included in [https://github.com/mjauvin/wn-ngmedia-plugin](Next Generation Media)
and is likely to be excluded in a future version of the plugin.

## A word of caution
[http://image.intervention.io](Intervention) is rather resource-intensive. It is therefore advised to apply filters
with caution. Further optimisation is planned for.
