<?php namespace Mercator\Media;

use Backend;
use Event;
use File;
use Request;
use System\Classes\PluginBase;
use System\Models\EventLog as EventLog;
use Winter\Storm\Database\Attach\Resizer as DefaultResizer;
use Intervention\Image\ImageManagerStatic as Image;
use Mercator\Media\Classes\MediaExtensions;

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
    public function pluginDetails()
    {
        return ['name' => 'Media',
                'author' => 'Helmut Kaufmann',
                'homepage' => 'htpps://mercator.li',
                'description' => 'Image/Media Processing Plugin for Winter CMS, replacing resize and introducing advanced image filter capabilities based on the Intervention library.'];
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
        Image::configure(array(
            'driver' => 'imagick'
        ));

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

            // Resize
            if (($width + $height) > 0)
            {
                // Resize large images with the default resizer (>8 megapixels)
                if (($dimensions["width"] * $dimensions["height"]) > NATIVE_RESIZE)
                {
                    // EventLog::add("iresize / ifilter: Native Resize " . __FILE__);

                    if (!$filters)
                    {
                        // End here if there are no filters to apply
                        \Winter\Storm\Database\Attach\Resizer::open($tempPath)->resize($width, $height, $options)->save($newPath);
                        if ($newPath != $tempPath)
                          File::move($newPath, $tempPath);

    					          // Prevent any other resizing replacer logic from running
                        return true;
                    }
                    else
                    {
                        // Save as JPG with 100% quality to limit quality deterioration
                        $intermediateOptions = $options;
                        $intermediateOptions["extension"] = 'jpg';
                        $intermediateOptions["quality"] = 100;
                        \Winter\Storm\Database\Attach\Resizer::open($tempPath)->resize($width, $height, $intermediateOptions)->save($newPath);
                        $image = Image::make($newPath);
                    }

                }
                else
                {
                    if (!$filters)
                    {
                        // End here if there are no filters to apply
                        $image = Image::make($tempPath)->resize($width, $height)->save($newPath, $quality, $extension);
                        if ($newPath != $tempPath)
                        	File::move($newPath, $tempPath);

                        	// Prevent any other resizing replacer logic from running
                        	return true;
                    }
                    else
                    {
                    	// Resize
                    	$image = Image::make($tempPath)->resize($width, $height);
                    }
                }
            }
            else $image = Image::make($tempPath);

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
                            EventLog::add("mercator/media: Detected and ignored unknown filter >>" . $arguments[0] . "<<. See " . __FILE__);
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
