<?php namespace Mercator\Media;

use Log;
use Backend;
use Event;
use File;
use Request;
use System\Classes\PluginBase;
use System\Models\EventLog as EventLog;
use Winter\Storm\Database\Attach\Resizer as DefaultResizer;
use Mercator\Media\Classes\MediaExtensions;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// Use native resize method for larger images, e.g. above 8 megapixels
define("NATIVE_RESIZE", (8 * 1024 * 1024));

/**
 *  Media Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
	
	static $acceptsWebP;
	
    public function pluginDetails()
    {
        return ['name' => 'Media',
                'author' => 'Helmut Kaufmann',
                'homepage' => 'htpps://mercator.li',
                'description' => 'Image processing plugin for Winter CMS, replacing resize and introducing advanced image filter capabilities based on the Intervention library.'];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        
		
		$acceptsWebP = Request::accepts(['images/webp']);
		
        Event::listen('system.resizer.processResize', function ($resizer, $tempPath)
        {

            // Get the configuration options the user has sumitted
            $config = $resizer->getConfig();
            $options = array_get($config, 'options', []);
			
            $width = $config['width'];
            $height = $config['height'];
            $quality = $options["quality"];
            $extension = $options["extension"];
            $filters = array_get($config['options'], 'filters', null);

            list($base, $ext) = explode('.', $tempPath);
            $newPath = $base . '.' . array_get($options, 'extension', $ext);
			
            $size = getimagesize($tempPath);
            $dimensions['width'] = $size[0];
            $dimensions['height'] = $size[1];

            $manager = new ImageManager(Driver::class);
            $image = $manager->read($tempPath); 
            
            if (($width > 0) && ($height > 0)) 
                $image = $image->scale($width, $height);
            elseif ($width == 0)
                $image = $image->scale(height: $height);
            else
                $image = $image->scale(width: $width);

            // Apply filters
            if (is_array($filters))
            {

                // Iterate over filer
                foreach ($filters as $filter)
                {
                    $arguments = array_values($filter);
                    $command = $arguments[0];
                    $arguments = array_shift($arguments);

                    try {
                        if (is_array($arguments))
                          call_user_func_array(array($image, $command), $arguments);
                        else
                          call_user_func(array($image, $command));
                    }
                    catch (Exception $e) {
                    
					// EventLog::add("wn-mercator-media: Detected and ignored unknown filter and/or arguments>>" . $arguments[0] . "<< in " . __FILE__);
						
                    }

                }
            }
            elseif (strcmp($filters, ""))
              $image = eval("{ return \$image->" . $filters . "; }");

            $image->save($newPath, $quality, $extension);
            if ($newPath != $tempPath)
              File::move($newPath, $tempPath);

            // Prevent any other resizing replacer logic from running
            return true;

        });

    }


    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

    }

    public function registerMarkupTags()
    {
        Log::info("media - iresize registered");
        return [
          'filters' =>    [
                            'iresize' => [MediaExtensions::class , 'iresize'],
                            'ifilter' => [MediaExtensions::class , 'iresize'],
                            'iprefetch' => [MediaExtensions::class , 'iprefetch'],
                            'ithumb' => [MediaExtensions::class , 'ithumb']
                          ],
          'functions' =>  [
                            'exif' => [MediaExtensions::class , 'exif'],
                            'iptc' => [MediaExtensions::class , 'iptc']
                          ]
        ];
    }
}
