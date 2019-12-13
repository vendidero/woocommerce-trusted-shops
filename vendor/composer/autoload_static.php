<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit64bf2a602ac7fda4c14c8a570c7e6f0b
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Vendidero\\TrustedShops\\' => 23,
        ),
        'A' => 
        array (
            'Automattic\\Jetpack\\Autoloader\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Vendidero\\TrustedShops\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Automattic\\Jetpack\\Autoloader\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/jetpack-autoloader/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit64bf2a602ac7fda4c14c8a570c7e6f0b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit64bf2a602ac7fda4c14c8a570c7e6f0b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
