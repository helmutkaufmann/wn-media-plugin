<?php namespace Mercator\Media\Classes;


use File;
use Request;
use System\Classes\ImageResizer;
use System\Models\EventLog as EventLog;
use Intervention\Image\ImageManagerStatic as Image;

class MediaExtensions extends \Backend\Classes\Controller
{
    /**
     * Define image format to be generated. For ngfullsize and ngresize, the FIRST format the browser
     * can display will be generated, unless the user has requested a format explicitly.
     * As a consequence, the most desirable formats (imageTypes) must go to the top.
     */
    static $defaultOptions = [
        'imageTypes' => [
            'image/webp' => [
                'extension' => 'webp',
                'quality' => 60,
            ],
            'image/jpg' => [
                'extension' => 'jpg',
                'quality' => 70,
            ],
            'image/jpeg' => [
                'extension' => 'jpeg',
                'quality' => 70,
            ],
            'image/gif' => [
                'extension' => 'gif',
                'quality' => 80,
            ],
            'image/png' => [
                'extension' => 'png',
                'quality' => 80,
            ],
        ],
    ];

    public static function acceptsFormat($type)
    {
        $acceptableTypes = array_merge([
            'image/gif',
            'image/jpg',
            'image/jpeg',
            'image/png',
        ], Request::getAcceptableContentTypes());

        return in_array($type, $acceptableTypes) || in_array("image/$type", $acceptableTypes);
    }


    public static function iresize($image, $width=null, $height=null, $filters=null, $extension=null, $quality=null)
    {
    	// Check if the file exists
        $path = public_path(parse_url($image, PHP_URL_PATH));
        if (!File::exists($path)) {
            return $image;
        }

        // Uxtension must be all lower case
        if ($extension)
        	$extension = strtolower($extension);

     	// Use the explicitly spcifie format or provide the best available if no format has been specified
        foreach (static::$defaultOptions['imageTypes'] as $type => $typeOptions) {

        	$ext = array_get($typeOptions, 'extension');

            if (($extension && !strcmp($ext, $extension)) || (!$extension &&  static::acceptsFormat($type))) {

            	if (!$quality)
            		$quality = array_get($typeOptions, 'quality', 90);
                $publicPath = File::localToPublic($path);
                $resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension' => $ext, 'quality'=> $quality, 'filters' => $filters ]);

                $url = urldecode(urldecode($resizerPath));
                $resizedImagePath = base_path(substr($url, strpos($url, "//")));

                /*

                if (is_file($resizedImagePath) && ( filectime($resizedImagePath) < filectime(base_path() . $image))) {
                	// The image is newer than the resiuzed one, unlink the resized, so it gets re-generated.
                	// EventLog::add("isresize - Resized image removed as there is a new original in " . __FILE__);
                	unlink($resizedImagePath);
                }

                */

				return $resizerPath;

            }
        }

        EventLog::add("Could not resize image, unknown format $extension in " . __FILE__);
        return $image;
    }

    public static function exif($image, $type=null)
    {
		if (!$type)
			return $data = Image::make(base_path(). $image)->exif();
		else
			return Image::make($image)->exif($type);
    }

    public static function iptc($image, $type=null)
    {
		if (!$type)
			return $data = Image::make(base_path().$image)->iptc();
		else
			return Image::make($image)->iptc($type);
    }

    public static function ithumb($image, $size)
    {
        return dirname($image) ."/_thumb$size/" . basename($image);
    }
}
