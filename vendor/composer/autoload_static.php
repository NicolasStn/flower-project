<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite8a5367a1a3337f457bde7cda5ba2bb4
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite8a5367a1a3337f457bde7cda5ba2bb4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite8a5367a1a3337f457bde7cda5ba2bb4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite8a5367a1a3337f457bde7cda5ba2bb4::$classMap;

        }, null, ClassLoader::class);
    }
}
