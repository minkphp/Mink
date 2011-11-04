<?php

namespace Behat\Mink\Compiler;

use Symfony\Component\Finder\Finder;

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class loader map file compiler.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MapFileCompiler
{
    /**
     * Behat lib directory.
     *
     * @var     string
     */
    private $libPath;

    /**
     * Initializes compiler.
     */
    public function __construct()
    {
        $this->libPath = realpath(__DIR__ . '/../../../../');
    }

    /**
     * Compiles map file and autoloader.
     *
     * @param   string  $version
     */
    public function compile($autoloaderFilename = 'autoload.php', $mapFilename = 'autoload_map.php')
    {
        if (file_exists($mapFilename)) {
            unlink($mapFilename);
        }
        $mappings = '';

        foreach ($this->findPhpFile()->in($this->libPath . '/src') as $file) {
            $path   = str_replace($this->libPath . '/src/', '', $file->getRealPath());
            $class  = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "\$mappings['$class'] = \$minkDir . 'src/$path';\n";
        }

        // autoload Goutte
        $mappings .= "\nif (!defined('BEHAT_AUTOLOAD_GOUTTE') || true === BEHAT_AUTOLOAD_GOUTTE) {\n";
        $mappings .= "    \$mappings['Goutte\Client'] = __DIR__ . '/vendor/Goutte/src/Goutte/Client.php';\n";
        $mappings .= "}\n";

        // autoload SahiClient
        $mappings .= "\nif (!defined('BEHAT_AUTOLOAD_SAHI') || true === BEHAT_AUTOLOAD_SAHI) {\n";
        foreach ($this->findPhpFile()->in($this->libPath . '/vendor/SahiClient/src') as $file) {
            $path  = str_replace($this->libPath . '/vendor/SahiClient/src/', '', $file->getRealPath());
            $class = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "    \$mappings['$class'] = __DIR__ . '/vendor/SahiClient/src/$path';\n";
        }
        $mappings .= "}\n";

        // autoload php-selenium
        $mappings .= "\nif (!defined('BEHAT_AUTOLOAD_SELENIUM') || true === BEHAT_AUTOLOAD_SELENIUM) {\n";
        foreach ($this->findPhpFile()->in($this->libPath . '/vendor/php-selenium/src') as $file) {
            $path  = str_replace($this->libPath . '/vendor/php-selenium/src/', '', $file->getRealPath());
            $class = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "    \$mappings['$class'] = __DIR__ . '/vendor/php-selenium/src/$path';\n";
        }
        $mappings .= "}\n";

        // autoload Buzz
        $mappings .= "\nif (!defined('BEHAT_AUTOLOAD_BUZZ') || true === BEHAT_AUTOLOAD_BUZZ) {\n";
        foreach ($this->findPhpFile()->in($this->libPath . '/vendor/Buzz/lib') as $file) {
            $path  = str_replace($this->libPath . '/vendor/Buzz/lib/', '', $file->getRealPath());
            $class = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "    \$mappings['$class'] = __DIR__ . '/vendor/Buzz/lib/$path';\n";
        }
        $mappings .= "}\n";

        // autoload Symfony2
        $mappings .= "\nif (!defined('BEHAT_AUTOLOAD_SF2') || true === BEHAT_AUTOLOAD_SF2) {\n";
        foreach ($this->findPhpFile()->in($this->libPath . '/vendor/Symfony') as $file) {
            $path  = str_replace($this->libPath . '/vendor/', '', $file->getRealPath());
            $class = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "    \$mappings['$class'] = \$symfonyDir . '$path';\n";
        }
        $mappings .= "}\n";

        // autoload ZF2
        $mappings .= "\nif (!defined('BEHAT_AUTOLOAD_ZF2') || true === BEHAT_AUTOLOAD_ZF2) {\n";
        $zendDir   = $this->libPath . '/vendor/Goutte/vendor/zend/library';
        foreach (array(
                'Zend\Tool\Framework\Exception',
                'Zend\Registry',
                'Zend\Uri\Uri',
                'Zend\Validator\Validator',
                'Zend\Validator\AbstractValidator',
                'Zend\Validator\Hostname',
                'Zend\Validator\Ip',
                'Zend\Validator\Hostname\Com',
                'Zend\Validator\Hostname\Jp',
            ) as $class) {
            $path = str_replace('\\', '/', $class) . '.php';
            $mappings .= "    \$mappings['$class'] = \$zendDir . '$path';\n";
        }
        foreach ($this->findPhpFile()->in($zendDir . '/Zend/Uri') as $file) {
            $path  = str_replace($zendDir . '/', '', $file->getRealPath());
            $class = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "    \$mappings['$class'] = \$zendDir . '$path';\n";
        }
        foreach ($this->findPhpFile()->in($zendDir . '/Zend/Http') as $file) {
            $path  = str_replace($zendDir . '/', '', $file->getRealPath());
            $class = str_replace(array('/', '.php'), array('\\', ''), $path);
            $mappings .= "    \$mappings['$class'] = \$zendDir . '$path';\n";
        }
        $mappings .= "}\n";

        $mapContent = <<<MAP_FILE
<?php

\$minkDir = __DIR__ . '/';
\$zendDir = __DIR__ . '/vendor/Goutte/vendor/zend/library/';
if (is_dir(__DIR__ . '/vendor/Symfony/')) {
    \$symfonyDir = __DIR__ . '/vendor/';
} else {
    \$symfonyDir = '';
}

\$mappings = array();
$mappings
return \$mappings;
MAP_FILE;

        file_put_contents($mapFilename, $mapContent);
        file_put_contents($autoloaderFilename, $this->getAutoloadScript($mapFilename));
    }

    /**
     * Returns autoload.php content.
     *
     * @param   string  $mapFilename
     *
     * @return  string
     */
    protected function getAutoloadScript($mapFilename)
    {
        return sprintf(<<<'EOF'
<?php

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!class_exists('Behat\Mink\ClassLoader\MapFileClassLoader')) {
    require_once __DIR__ . '/src/Behat/Mink/ClassLoader/MapFileClassLoader.php';
}

use Behat\Mink\ClassLoader\MapFileClassLoader;

$loader = new MapFileClassLoader(__DIR__ . '/%s');
$loader->register();

EOF
        , $mapFilename);
    }

    /**
     * Creates finder instance to search php files.
     *
     * @return  Symfony\Component\Finder\Finder
     */
    private function findPhpFile()
    {
        $finder = new Finder();

        return $finder->files()->ignoreVCS(true)->name('*.php');
    }
}
