<?php return array(
    'root' => array(
        'name' => 'mercator/wn-media-plugin',
        'pretty_version' => '3.0.x-dev',
        'version' => '3.0.9999999.9999999-dev',
        'reference' => '3edd6c546b28bbde3f4acf288fc3f4e7be68d9d5',
        'type' => 'winter-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => '1.x-dev',
            'version' => '1.9999999.9999999.9999999-dev',
            'reference' => '894a0b5c5d34c88b69b097f2aae1439730fa6836',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'intervention/gif' => array(
            'pretty_version' => '4.2.0',
            'version' => '4.2.0.0',
            'reference' => '42c131a31b93c440ad49061b599fa218f06f93be',
            'type' => 'library',
            'install_path' => __DIR__ . '/../intervention/gif',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'intervention/image' => array(
            'pretty_version' => '3.10.0',
            'version' => '3.10.0.0',
            'reference' => '1ddc9a096b3a641958515700e09be910bf03a5bd',
            'type' => 'library',
            'install_path' => __DIR__ . '/../intervention/image',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'mercator/wn-media-plugin' => array(
            'pretty_version' => '3.0.x-dev',
            'version' => '3.0.9999999.9999999-dev',
            'reference' => '3edd6c546b28bbde3f4acf288fc3f4e7be68d9d5',
            'type' => 'winter-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
