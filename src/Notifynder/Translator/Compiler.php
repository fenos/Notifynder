<?php

namespace Fenos\Notifynder\Translator;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Class Compiler.
 *
 * Cache compiler for translations
 * I got part of the code from the view compiler
 * of laravel :)
 */
class Compiler
{
    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new compiler instance.
     *
     * @param Filesystem|\Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Get cached file.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getCompiledPath('notification_categories');
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string $filename
     * @return string
     */
    public function getCompiledPath($filename)
    {
        return $this->cachePath().'/'.md5($filename);
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        $compiled = $this->getFilePath();

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (! $this->cachePath() || ! $this->files->exists($compiled)) {
            return true;
        }

        $lastModified = $this->files->lastModified($this->getFilePath());

        return $lastModified >= $this->files->lastModified($compiled);
    }

    /**
     * Get cache path.
     *
     * @return string
     */
    protected function cachePath()
    {
        return storage_path('app/notifynder');
    }

    /**
     * Cache the file in json format.
     *
     * @param  array    $contents
     * @return bool|int
     */
    public function cacheFile(array $contents)
    {
        $contents = json_encode($contents);

        return $this->files->put($this->getFilePath(), $contents);
    }
}
