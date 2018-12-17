<?php

/**
 * FireFS - Easily manage your filesystem, through PHP
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @category  Library
 * @package   FireFS
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   MIT <https://github.com/ElementaryFramework/FireFS/blob/master/LICENSE>
 * @version   GIT: 0.0.1
 * @link      http://firefs.na2axl.tk
 */

namespace ElementaryFramework\FireFS\Watcher;

use ElementaryFramework\FireFS\FireFS;
use ElementaryFramework\FireFS\Events\FileSystemEntityModifiedEvent;
use ElementaryFramework\FireFS\Events\FileSystemEntityDeletedEvent;
use ElementaryFramework\FireFS\Events\FileSystemEntityCreatedEvent;
use ElementaryFramework\FireFS\Exceptions\FileSystemEntityNotFoundException;
use ElementaryFramework\FireFS\Exceptions\FileSystemWatcherException;
use ElementaryFramework\FireFS\Listener\IFileSystemListener;

/**
 * File System Watcher
 *
 * Watch for changes (files/folders creation, modification and deletion) on the file system.
 *
 * @package    FireFS
 * @subpackage Watcher
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
class FileSystemWatcher
{
    /**
     * The file system listener associated
     * to this watcher.
     *
     * @var IFileSystemListener
     */
    private $_listener;

    /**
     * The file system manager.
     *
     * @var FireFS
     */
    private $_fs;

    /**
     * The path to watch.
     *
     * @var string
     */
    private $_path = "./";

    /**
     * Define if we are watching changes
     * recursively.
     *
     * @var bool
     */
    private $_recursive = true;

    /**
     * The regex pattern of files and folders to watch.
     *
     * @var array
     */
    private $_patternInclude = array();

    /**
     * The regex pattern of files and folders
     * to excludes from watching.
     *
     * @var array
     */
    private $_patternExclude = array(
        "/^.+[\/\\\\]node_modules[\/\\\\]?.*$/",
        "/^.+[\/\\\\]\.git[\/\\\\]?.*$/"
    );

    /**
     * The number of milliseconds to wait before
     * watch for changes.
     *
     * @var integer
     */
    private $_watchInterval = 1000000;

    /**
     * Defines if the watcher is started and running.
     *
     * @var bool
     */
    private $_started = false;

    /**
     * Defines if the watcher is built.
     *
     * @var bool
     */
    private $_built = false;

    /**
     * Stores the list of files in the watched
     * folder.
     *
     * @var array
     */
    private $_filesCache = array();

    /**
     * Stores last modification times of watched files.
     *
     * @var array
     */
    private $_lastModTimeCache = array();

    /**
     * Defines if we are currently watching a
     * directory or not.
     *
     * @var bool
     */
    private $_watchingDirectory;

    /**
     * Creates a new instance of FileSystemWatcher.
     *
     * @param FireFS $fs The file system manager instance to use by the watcher.
     */
    public function __construct(FireFS &$fs)
    {
        $this->_fs = $fs;
    }

    /**
     * Sets the listener to use on watched files.
     *
     * @param IFileSystemListener $listener The listener instance.
     *
     * @return self
     */
    public function setListener(IFileSystemListener $listener): self
    {
        $this->_listener = $listener;

        return $this;
    }

    /**
     * Sets the path to the file/directory to watch.
     *
     * @param string $path The path.
     *
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->_path = $path;

        return $this;
    }

    /**
     * Defines if the watcher must watch for files recursively
     * (Works only if the watched entity is a directory).
     *
     * @param boolean $recursive
     *
     * @return self
     */
    public function setRecursive(bool $recursive): self
    {
        $this->_recursive = $recursive;

        return $this;
    }

    /**
     * Adds a new regex pattern for files to watch.
     *
     * @param string $pattern The regex pattern to add.
     *
     * @return self
     */
    public function addPattern(string $pattern): self
    {
        $this->_patternInclude[] = $pattern;

        return $this;
    }

    /**
     * Adds a new regex pattern for files to exclude from watcher.
     *
     * @param string $pattern The regex pattern to add.
     *
     * @return self
     */
    public function addExcludePattern(string $pattern): self
    {
        $this->_patternExclude[] = $pattern;

        return $this;
    }

    /**
     * Set the array of regex patterns matching files to watch.
     *
     * @param array $patterns The array of regex patterns.
     *
     * @return self
     */
    public function setPatterns(array $patterns) : self
    {
        $this->_patternInclude = $patterns;

        return $this;
    }

    /**
     * Set the array of regex patterns matching files to exclude from watcher.
     *
     * @param array $patterns The array of regex patterns.
     *
     * @return self
     */
    public function setExcludePatterns(array $patterns)
    {
        $this->_patternExclude = $patterns;

        return $this;
    }

    /**
     * Sets the watch interval.
     *
     * @param integer $interval The interval in milliseconds.
     *
     * @return self
     */
    public function setWatchInterval(int $interval): self
    {
        $this->_watchInterval = $interval * 1000;

        return $this;
    }

    public function build(): self
    {
        if (!$this->_built) {
            $this->_watchingDirectory = false;

            $this->_lastModTimeCache = array();

            if ($this->_fs->isDir($this->_path)) {
                $this->_filesCache = $this->_fs->readDir($this->_path, $this->_recursive);
                $this->_watchingDirectory = true;
            } else if ($this->_fs->exists($this->_path)) {
                $this->_addForWatch($this->_path);
            }

            $this->_cacheLastModTimes();

            $this->_built = true;
        }

        return $this;
    }

    /**
     * Start the file system watcher.
     *
     * @return void
     */
    public function start()
    {
        if (!$this->_built) {
            throw new FileSystemWatcherException("You must build the watcher before start it.");
        }

        if ($this->_started) return;

        $this->_started = true;

        while ($this->_started) {
            $this->process();
            usleep($this->_watchInterval);
        }
    }

    /**
     * Process a watch
     *
     * @return void
     */
    public function process()
    {
        clearstatcache(true);
        $this->_detectChanges();
        $this->_cacheLastModTimes();
    }

    /**
     * Stop the file system watcher.
     *
     * @return void
     */
    public function stop()
    {
        if (!$this->_built) {
            throw new FileSystemWatcherException("You must build the watcher before stop it.");
        }

        $this->_started = false;
        $this->_cacheLastModTimes();
    }

    /**
     * Restart the file system watcher.
     *
     * @return void
     */
    public function restart()
    {
        if (!$this->_built) {
            throw new FileSystemWatcherException("You must build the watcher before restart it.");
        }

        $this->stop();
        $this->_built = false;
        $this->build()->start();
    }

    private function _detectChanges()
    {
        if ($this->_watchingDirectory) {
            $this->_watchFolder($this->_path);
        } else {
            $this->_watchFile($this->_path);
        }
    }

    private function _watchFolder(string $_path)
    {
        $directory = $this->_fs->readDir($_path, $this->_recursive);
        $watching = array_merge($this->_filesCache, $directory);

        foreach ($watching as $name => $path) {
            if ($this->_fs->isDir($path)) {
                continue;
            } else {
                $this->_watchFile($path);
            }
        }

        $this->_filesCache = $directory;
    }

    private function _watchFile(string $_path)
    {
        if (count($this->_patternExclude) > 0) {
            foreach ($this->_patternExclude as $pattern) {
                if (preg_match($pattern, $_path, $m)) {
                    return;
                }
            }
        }

        $match = true;

        if (count($this->_patternInclude) > 0) {
            $match = false;
            foreach ($this->_patternInclude as $pattern) {
                if (preg_match($pattern, $_path, $m)) {
                    $match = true;
                    break;
                }
            }
        }

        if ($match) {
            $path = $this->_fs->cleanPath($_path);

            if ($this->_fs->exists($path)) {
                if (array_key_exists($path, $this->_lastModTimeCache)) {
                    if ($this->_lastModTimeCache[$path] < $this->_lmt($path)) {
                        if ($this->_listener->onAny(new FileSystemEntityModifiedEvent($path))) {
                            $this->_listener->onModified(new FileSystemEntityModifiedEvent($path));
                        }
                    }
                } else {
                    $this->_addForWatch($_path);
                    if ($this->_listener->onAny(new FileSystemEntityCreatedEvent($path))) {
                        $this->_listener->onModified(new FileSystemEntityCreatedEvent($path));
                    }
                }
            } else {
                if (array_key_exists($path, $this->_lastModTimeCache)) {
                    $this->_removeFromWatch($_path);
                    if ($this->_listener->onAny(new FileSystemEntityDeletedEvent($path))) {
                        $this->_listener->onDeleted(new FileSystemEntityDeletedEvent($path));
                    }
                }
            }
        }
    }

    private function _cacheLastModTimes()
    {
        foreach ($this->_filesCache as $name => $path) {
            $this->_lastModTimeCache[$path] = $this->_lmt($path);
        }
    }

    private function _lmt(string $path): int
    {
        return $this->_fs->lastModTime($path);
    }

    private function _addForWatch(string $path)
    {
        $p =  $this->_fs->cleanPath($path);
        $this->_filesCache = array($path => $p);
        $this->_lastModTimeCache[$p] = $this->_lmt($p);
    }

    private function _removeFromWatch(string $path)
    {
        $p =  $this->_fs->cleanPath($path);
        unset($this->_filesCache[$path]);
        unset($this->_lastModTimeCache[$p]);
    }
}
