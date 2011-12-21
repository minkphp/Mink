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
 * behat.phar package compiler.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PharCompiler
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
     * Compiles phar archive.
     *
     * @param   string  $version
     */
    public function compile($version)
    {
        if (file_exists($package = "mink-$version.phar")) {
            unlink($package);
        }

        // create phar
        $phar = new \Phar($package, 0, 'mink.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('*.xliff')
            ->name('*.xml')
            ->name('*.js')
            ->name('*.feature')
            ->name('LICENSE')
            ->name('LICENSE.txt')
            ->notName('test')
            ->notName('tests')
            ->exclude(array(
                'Compiler',
                'finder',
                'test',
                'tests',
                'vendor',
            ))
            ->in($this->libPath . '/src')
            ->in($this->libPath . '/vendor/.composer')
            ->in($this->libPath . '/vendor/symfony')
            ->in($this->libPath . '/vendor/alexandresalome')
            ->in($this->libPath . '/vendor/behat')
            ->in($this->libPath . '/vendor/fabpot')
            ->in($this->libPath . '/vendor/facebook')
            ->in($this->libPath . '/vendor/kriswallsmith')
            ->in($this->libPath . '/vendor/zendframework/zend-uri')
            ->in($this->libPath . '/vendor/zendframework/zend-http')
        ;

        $files = array(
            $this->libPath . '/vendor/zendframework/zend-registry/php/Zend/Registry.php',
            $this->libPath . '/vendor/zendframework/zend-validator/php/Zend/Validator/Validator.php',
            $this->libPath . '/vendor/zendframework/zend-validator/php/Zend/Validator/AbstractValidator.php',
            $this->libPath . '/vendor/zendframework/zend-validator/php/Zend/Validator/Hostname.php',
            $this->libPath . '/vendor/zendframework/zend-validator/php/Zend/Validator/Ip.php',
            $this->libPath . '/vendor/zendframework/zend-validator/php/Zend/Validator/Hostname/Com.php',
            $this->libPath . '/vendor/zendframework/zend-validator/php/Zend/Validator/Hostname/Jp.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/Dispatchable.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/Message.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/MessageDescription.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/RequestDescription.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/Parameters.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/ParametersDescription.php',
            $this->libPath . '/vendor/zendframework/zend-stdlib/php/Zend/Stdlib/ResponseDescription.php',
            $this->libPath . '/vendor/zendframework/zend-loader/php/Zend/Loader/PluginClassLoader.php',
            $this->libPath . '/vendor/zendframework/zend-loader/php/Zend/Loader/PluginClassLocator.php',
            $this->libPath . '/vendor/zendframework/zend-loader/php/Zend/Loader/ShortNameLocator.php',
        );

        foreach (array_merge($files, iterator_to_array($finder)) as $file) {
            if (!$file instanceof \SplFileInfo) {
                $file = new \SplFileInfo($file);
            }

            $this->addFileToPhar($file, $phar);
        }

        // stub
        $phar->setStub($this->getStub($version));
        $phar->stopBuffering();

        unset($phar);
    }

    /**
     * Adds a file to phar archive.
     *
     * @param   SplFileInfo $file   file info
     * @param   Phar        $phar   phar packager
     */
    protected function addFileToPhar(\SplFileInfo $file, \Phar $phar)
    {
        $path = str_replace($this->libPath . '/', '', $file->getRealPath());
        $phar->addFromString($path, file_get_contents($file));
    }

    /**
     * Returns autoloader stub.
     *
     * @param   string  $version
     *
     * @return  string
     */
    protected function getStub($version)
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

Phar::mapPhar('mink.phar');
require_once 'phar://mink.phar/vendor/facebook/php-webdriver/__init__.php';
require_once 'phar://mink.phar/vendor/.composer/autoload.php';

__HALT_COMPILER();
EOF
        , $version);
    }
}
