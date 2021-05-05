<?php namespace Mercator\Media\Classes;

use File;
use Request;
use System\Classes\ImageResizer;

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

    public static function resize($image, $width, $height, $options=[]) 
    {
        $path = public_path(parse_url($image, PHP_URL_PATH));
        if (!File::exists($path)) {
            return $image;
        }
        
        list ($filename, $ext) = explode('.', $path);
        
        $options = array_merge($options, static::$defaultOptions);
        $manipulation = array_get($options, 'manipulation', null);
        $quality = array_get($options, 'quality', null);

        // If the extension is set explicitly, honor it (even if the browser might not be able to handle it). 
        if (array_get($options, 'extension', false)) {
            $publicPath = File::localToPublic($path);
            if ($manipulation) {
				$resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension'=>$ext, 'quality'=>$quality, 'manipulation' => $manipulation ]);
			} else {
				$resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension'=>$ext, 'quality'=>$quality]);
			}
            return $resizerPath;
        }

     	// The user has not specified a format - provide the best available
        foreach ($options['imageTypes'] as $type => $typeOptions) {
        
            if (static::acceptsFormat($type)) {
            
                ($ext = array_get($typeOptions, 'extension')) or die();
                ($quality = array_get($typeOptions, 'quality')) or die();
                $publicPath = File::localToPublic($path);
                
                if ($manipulation) {
					$resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension'=>$ext, 'quality'=>$quality, 'manipulation' => $manipulation ]);
				}
				else {
					$resizerPath = ImageResizer::filterGetUrl($publicPath, $width, $height, ['extension'=>$ext, 'quality'=>$quality]);
				}
					
				return $resizerPath;
                
            }
        }
    }

   
}
