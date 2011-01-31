<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * A collection of assets loaded by glob.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class GlobAsset extends AssetCollection
{
    private $globs;
    private $baseDir;
    private $initialized;

    /**
     * Constructor.
     *
     * @param string|array $globs   A single glob path or array of paths
     * @param string       $baseDir A base directory to use for determining each source URL
     * @param array        $filters An array of filters
     *
     * @throws InvalidArgumentException If the base directory doesn't exist
     */
    public function __construct($globs, $baseDir = null, $filters = array())
    {
        $this->globs = (array) $globs;

        if (null !== $baseDir && $this->baseDir = realpath($baseDir)) {
            $this->baseDir .= DIRECTORY_SEPARATOR;
        }

        $this->initialized = false;

        parent::__construct(array(), $filters);
    }

    /**
     * Initializes the collection based on the glob(s) passed in.
     */
    private function initialize()
    {
        foreach ($this->globs as $glob) {
            if (false !== $paths = glob($glob)) {
                foreach (array_map('realpath', $paths) as $path) {
                    $this->add(new FileAsset($path, 0 === strpos($path, $this->baseDir) ? substr($path, strlen($this->baseDir)) : null));
                }
            }
        }

        $this->initialized = true;
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        parent::load($additionalFilter);
    }

    public function dump($targetUrl = null, FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::dump($targetUrl, $additionalFilter);
    }

    public function getLastModified()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getLastModified();
    }

    public function rewind()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::rewind();
    }
}
