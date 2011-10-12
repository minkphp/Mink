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
            ->name('*.xml')
            ->name('*.xliff')
            ->name('LICENSE')
            ->notName('PharCompiler.php')
            ->notName('PearCompiler.php')
            ->notName('Compiler.php')
            ->in($this->libPath . '/src')
            ->in($this->libPath . '/vendor/Symfony/Component/BrowserKit')
            ->in($this->libPath . '/vendor/Symfony/Component/CssSelector')
            ->in($this->libPath . '/vendor/Symfony/Component/DomCrawler')
            ->in($this->libPath . '/vendor/Symfony/Component/Process')
            ->in($this->libPath . '/vendor/Buzz/lib')
            ->in($this->libPath . '/vendor/Goutte/src')
            ->in($this->libPath . '/vendor/SahiClient/src')
            ->in($this->libPath . '/vendor/WebDriver');

        foreach ($finder as $file) {
            // don't compile test suites
            if (!preg_match('/\/tests\/|\/test\//', $file->getRealPath())) {
                $this->addFileToPhar($file, $phar);
            }
        }

        $zendDir = $this->libPath . '/vendor/Goutte/vendor/zend/library/';
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
            $this->addFileToPhar(new \SplFileInfo($zendDir . $path), $phar);
        }
        foreach ($this->findPhpFile()->in($zendDir . '/Zend/Uri') as $file) {
            $this->addFileToPhar($file, $phar);
        }
        foreach ($this->findPhpFile()->in($zendDir . '/Zend/Http') as $file) {
            $this->addFileToPhar($file, $phar);
        }

        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/LICENSE'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/autoload.php'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/autoload_map.php'), $phar);

        // license and autoloading
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/LICENSE'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/autoload.php'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/autoload_map.php'), $phar);

        // 3rd-party licenses
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/vendor/Goutte/LICENSE'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/vendor/Buzz/LICENSE'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/vendor/SahiClient/LICENSE'), $phar);
        $this->addFileToPhar(new \SplFileInfo($this->libPath . '/vendor/Goutte/vendor/zend/LICENSE.txt'), $phar);

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
require_once 'phar://mink.phar/autoload.php';

__HALT_COMPILER();
EOF
        , $version);
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
