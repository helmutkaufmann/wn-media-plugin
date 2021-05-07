<?php namespace Mercator\Media\Classes;

use File;
use Request;
use System\Classes\ImageResizer;
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
                'quality' => 50,
            ],
            'image/jpeg' => [
                'extension' => 'jpg',
                'quality' => 60,
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
    
    
    public static function iresize($image, $width=null, $height=null, $filters=[], $extension=null, $quality=null) 
    {
        $path = public_path(parse_url($image, PHP_URL_PATH));
        if (!File::exists($path)) {
            return $image;
        }
        
        list ($filename, $ext) = explode('.', $path);
        
        // If the extension is set explicitly, honor it (even if the browser might not be able to handle it). 
        if ($extension) {
            $publicPath = File::localToPublic($path);
            $resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension'=>$extension, 'quality'=>$quality, 'filters' => $filters ]);
            return $resizerPath;
        }

     	// The user has not specified a format - provide the best available
        foreach (static::$defaultOptions['imageTypes'] as $type => $typeOptions) {
        
            if (static::acceptsFormat($type)) {
            
                ($ext = array_get($typeOptions, 'extension'));
                $quality = array_get($typeOptions, 'quality', 90);
                $publicPath = File::localToPublic($path);
                $resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension'=>$ext, 'quality'=>$quality, 'filters' => $filters ]);
				return $resizerPath;
                
            }
        }
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
}
