<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit82ad08a0b24b9adbdd37dd9305173144
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Combodo\\iTop\\Extension\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Combodo\\iTop\\Extension\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Combodo\\iTop\\Extension\\Helper\\MessageHelper' => __DIR__ . '/../..' . '/src/Helper/MessageHelper.php',
        'Combodo\\iTop\\Extension\\Helper\\ProviderHelper' => __DIR__ . '/../..' . '/src/Helper/ProviderHelper.php',
        'Combodo\\iTop\\Extension\\Service\\IMAPOAuthEmailSource' => __DIR__ . '/../..' . '/src/Service/IMAPOAuthEmailSource.php',
        'Combodo\\iTop\\Extension\\Service\\IMAPOAuthLogin' => __DIR__ . '/../..' . '/src/Service/IMAPOAuthLogin.php',
        'Combodo\\iTop\\Extension\\Service\\IMAPOAuthStorage' => __DIR__ . '/../..' . '/src/Service/IMAPOAuthStorage.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit82ad08a0b24b9adbdd37dd9305173144::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit82ad08a0b24b9adbdd37dd9305173144::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit82ad08a0b24b9adbdd37dd9305173144::$classMap;

        }, null, ClassLoader::class);
    }
}
