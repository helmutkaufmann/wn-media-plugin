<?php namespace Mercator\Media;

use Backend;
use Event;
use File;
use Request;
use System\Classes\PluginBase;
use Mercator\Media\Classes\MediaExtensions;
use Intervention\Image\ImageManagerStatic as Image;

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
        return [
            'name'        => 'Media',
            'description' => 'Media Processing Plugin for Winter CMS. Based in parts around Next Generation Media by Marc Jauvin.',
            'author'      => 'Helmut Kaufmann',
            'icon'        => 'icon-leaf'
        ];
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
    	
    	
    	Image::configure(array('driver' => 'imagick'));
    	
        Event::listen('system.resizer.processResize', function ($resizer, $tempPath) { 	
        	
            // Get the configuration options the user has sumitted 
            $config = $resizer->getConfig();
            $options = array_get($config, 'options', []);
            $width=$config['width'];
            $height=$config['height'];
            $quality=$options["quality"];
            $manipulation = array_get($config['options'], 'manipulation', null);

            list($base, $ext) = explode('.', $tempPath);
            $newPath = $base . '.' . array_get($options, 'extension', $ext);
			
			if ($manipulation) {

				$image = Image::make($tempPath)->resize($width, $height);
				$image=eval("{ return \$image->" . $manipulation . "; }");
				$image->save($newPath, $options["quality"], $options["extension"]);   
				
			} else {
				
				// Use built-in resizer as Intervention is slow
				\Winter\Storm\Database\Attach\Resizer::open($tempPath)->resize($width, $height, $options)->save($newPath, $quality);
                
                // And not 
                // Image::make($tempPath)->resize($width, $height)->save($newPath, $quality);
                
			}

            if ($newPath != $tempPath) {
                File::move($newPath, $tempPath);
            }

            // Prevent any other resizing replacer logic from running
            return true;
        });
        
        // 
        // Next extension step:
        // Listen to resizing events and set the default image format based on the browser's rendering capabilities
        // At the moment, the only advanced media format is webp. This would optimize the behavior of the standard
        // Twig resize function.
        /*
        Event::listen('system.resizer.getDefaultOptions', function (&$defaultOptions) {

			if (MediaExtensions::acceptsFormat("webp")) {
    			$defaultOptions['extension'] = 'webp';
    		}
    		else {
    			$defaultOptions['extension'] = 'jpg';
    		}
    	
		});
        */
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
            'filters' => [
                'ngresize' =>  [MediaExtensions::class, 'resize'],
                'iresize' =>  [MediaExtensions::class, 'resize'],
                
            ],
            'functions' => [
                'acceptsFormat' => [MediaExtensions::class, 'acceptsFormat'], 
                
            ],
        ];
    }
}
