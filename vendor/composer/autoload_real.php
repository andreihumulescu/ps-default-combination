<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit5d096bbe2bfe6f147942b5327706a6ab
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit5d096bbe2bfe6f147942b5327706a6ab', 'loadClassLoader'), true, false);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit5d096bbe2bfe6f147942b5327706a6ab', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit5d096bbe2bfe6f147942b5327706a6ab::getInitializer($loader));

        $loader->register(false);

        return $loader;
    }
}
