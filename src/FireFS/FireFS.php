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

namespace ElementaryFramework\FireFS;

use ElementaryFramework\FireFS\Exceptions\FileSystemEntityNotFoundException;
use ElementaryFramework\FireFS\Listener\IFileSystemListener;
use ElementaryFramework\FireFS\Events\FileSystemEntityCreatedEvent;
use ElementaryFramework\FireFS\Events\FileSystemEntityDeletedEvent;
use ElementaryFramework\FireFS\Events\FileSystemEntityModifiedEvent;
use ElementaryFramework\FireFS\Entities\Folder;
use ElementaryFramework\FireFS\Entities\File;

/**
 * FireFS - Filesystem Manager Class
 *
 * @package     FireFS
 * @author      Nana Axel <ax.lnana@outlook.com>
 */
class FireFS
{
    /**
     * Choose to output the real file path
     *
     * @const int
     */
    const REAL_PATH = 1;

    /**
     * Choose to output the internal file path
     *
     * @const int
     */
    const INTERNAL_PATH = 2;

    /**
     * Choose to output the external file path
     *
     * @const int
     */
    const EXTERNAL_PATH = 3;

    /**
     * Choose to output the external file path
     *
     * @const int
     */
    const FILESYSTEM_PATH = 4;

    /**
     * The FileSystem root path
     *
     * @var string
     * @access protected
     */
    protected $rootPath = '';

    /**
     * The current working directory
     *
     * @var string
     * @access protected
     */
    protected $workingDir = '';

    /**
     * The path to the directory used to store
     * temporary files.
     *
     * @var string
     */
    protected $tempDir = '';

    /**
     * Aliases
     *
     * @var array
     * @access protected
     */
    protected $aliases = array();

    /**
     * The file system event listener of
     * this instance.
     *
     * @var IFileSystemListener
     */
    protected $listener;

    /**
     * Class constructor
     *
     * Set de default root path,
     * the default working directory,
     * and defaults aliases.
     *
     * @param string $rootPath The root path of the file system.
     */
    public function __construct(string $rootPath = "./")
    {
        if (is_dir($rootPath))
            $this->setRootPath(realpath($this->cleanPath($rootPath)));
        else
            throw new \RuntimeException("The directory \"{$rootPath}\" can't be located.");

        $this->setWorkingDir('./');
        $this->setTempDir('./tmp');
    }

    /**
     * Changes the current root path
     *
     * @param string $rootPath The new root path
     *
     * @return void
     */
    public function setRootPath(string $rootPath)
    {
        $this->rootPath = $this->cleanPath($rootPath);
    }

    /**
     * Changes the path to the directory of
     * temporary files.
     *
     * @param string $path The path to the directory starting
     *                     to the root path.
     */
    public function setTempDir(string $path)
    {
        $this->tempDir = $this->cleanPath($path);
    }

    /**
     * Sets the file system listener of this instance.
     *
     * It's important to note that this listener will
     * used only to listen actions from this instance
     * of FireFS. It cannot listen for changes from the
     * outside.
     *
     * If you want to watch the file system for changes,
     * use the FileSystemWatcher class.
     *
     * @param IFileSystemListener $listener The event listener.
     *
     * @return void
     */
    public function setListener(IFileSystemListener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * Clean the path for bad directory name
     *
     * @param string $path The path to clean
     *
     * @return string
     */
    public function cleanPath(string $path): string
    {
        if ($this->isRemote($path)) {
            return $path;
        }

        $badDirs = $this->explodePath($path);
        $cleanDirs = array();
        foreach ($badDirs as $i => $dir) {
            if ($dir == '..') {
                array_pop($cleanDirs);
            } elseif ($dir == '.') {
                continue;
            } elseif (empty($dir) && $i > 0) {
                continue;
            } else {
                $cleanDirs[] = $dir;
            }
        }

        if ($path != '/') {
            $beautifiedPath = implode(DIRECTORY_SEPARATOR, $cleanDirs);
        }

        if (empty($beautifiedPath)) {
            $beautifiedPath = (substr($path, 0, 1) == '/') ? '/' : '.';
        }

        return $beautifiedPath;
    }

    /**
     * Check if the path is remote
     *
     * @param string $path The path to Check
     *
     * @return boolean true if the path is remote, and false otherwise.
     */
    public function isRemote(string $path): bool
    {
        return (strpos($path, '://') !== false);
    }

    /**
     * Explode a path in an array.
     *
     * @param string $path The path to explode.
     *
     * @return array The exploded path
     */
    public function explodePath(string $path): array
    {
        return explode(DIRECTORY_SEPARATOR, str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $path));
    }

    /**
     * Set the current working directory
     *
     * @param string $workingDir The path to the directory starting
     *                           to the root path.
     *
     * @return void
     */
    public function setWorkingDir(string $workingDir)
    {
        $this->workingDir = $workingDir;
    }

    /**
     * Gets the aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Create a new alias
     *
     * @param string $key The key of the alias
     * @param string $val The value of the alias
     *
     * @return void
     */
    public function newAlias(string $key, string $val)
    {
        if (substr($val, -1) == '/') {
            $val = substr($val, 0, -1);
        }

        $this->aliases[$key] = $val;
    }

    /**
     * Read the file's contents
     *
     * @param string $path The path to the file
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function read(string $path): string
    {
        $internalPath = $this->toInternalPath($path);

        if (!$this->isRemote($path) && !is_readable($internalPath)) {
            throw $this->accessDeniedException($path, 'read');
        }

        $contents = file_get_contents($internalPath);

        if ($contents === false) {
            throw new \RuntimeException("Cannot read the file \"{$path}\"");
        }

        return $contents;
    }

    /**
     * Transform a path to an internal FileSystem path
     *
     * @param string $path The path to transform
     *
     * @return string
     */
    public function toInternalPath(string $path): string
    {
        $internalPath = $path;

        // Do nothing if is a remote path
        if ($this->isRemote($internalPath)) {
            return $internalPath;
        }

        if (substr($internalPath, 0, strlen($this->rootPath())) == $this->rootPath()) {
            $internalPath = str_replace($this->rootPath(), './', $internalPath);
        } else {
            $realRootPath = realpath($this->rootPath());
            if (substr($internalPath, 0, strlen($realRootPath)) == $realRootPath) {
                $internalPath = substr($internalPath, strlen($realRootPath));
            }
        }

        // Convert relative path to absolute path
        if (preg_match('#^(\.)+/#', $internalPath)) {
            $internalPath = $this->makePath($this->workingDir(), $internalPath);
        }

        // Apply aliases
        $nbrTurns = 0;
        $maxNbrTurns = count($this->aliases);
        do {
            $appliedAliasesNbr = 0;

            foreach ($this->aliases as $key => $value) {
                if (substr($internalPath, 0, strlen($key)) == $key) {
                    $internalPath = $this->makePath($value, substr($internalPath, strlen($key)));
                    $appliedAliasesNbr++;
                }
            }

            $nbrTurns++;
        } while ($appliedAliasesNbr > 0 && $nbrTurns <= $maxNbrTurns);

        // Prepend the root path
        $rootPath = $this->rootPath();
        if (!empty($rootPath)) {
            $internalPath = $this->makePath($rootPath, $internalPath);
        }

        return $this->cleanPath($internalPath);
    }

    /**
     * Gets the value of root path
     *
     * @return string
     */
    public function rootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Implode all parts of $path and return a valid path
     *
     * @param  string[] $path Parts of the path to build
     * @return string
     */
    public function makePath(string ...$path): string
    {
        return implode(DIRECTORY_SEPARATOR, array_map(function ($field) {
            return rtrim($field, '/\\');
        }, $path));
    }

    /**
     * Gets the current working directory
     *
     * @return string
     */
    public function workingDir(): string
    {
        return $this->workingDir;
    }

    /**
     * Used to throw an access denied exception
     *
     * @param string $path The path to the file
     * @param string $action The type of access denied
     *
     * @return \RuntimeException
     */
    protected function accessDeniedException(string $path, string $action = 'write'): \RuntimeException
    {
        $msg = "Cannot {$action} the file \"{$path}\": permission denied";
        if (!$this->isRemote($path)) {
            $msg .= " (The web server user cannot {$action} files, chmod needed)";
        }
        return new \RuntimeException($msg);
    }

    /**
     * Write in the file
     *
     * @param string $path The file to write in
     * @param string $data The new contents of the file
     * @param boolean $append If the data have to be appended in the file
     *
     * @throws \RuntimeException
     *
     * @return boolean
     */
    public function write(string $path, string $data, bool $append = false): bool
    {
        $internalPath = $this->toInternalPath($path);
        $applyChmod = false;

        if ($append === true) {
            $file = fopen($internalPath, "a");
            if (fwrite($file, $data) !== false) {
                fclose($file);
                $this->_onModify($path);
                return true;
            } else {
                fclose($file);
                throw new \RuntimeException("Cannot write in the file \"{$path}\"");
            }
        } else {
            if ($this->exists($path)) {
                if (!$this->isRemote($path) && !is_writable($internalPath)) {
                    throw $this->accessDeniedException($path, 'write');
                }
            } else {
                $applyChmod = true;
                $this->mkfile($path, true);
            }

            if (file_put_contents($internalPath, $data) !== false) {
                $this->_onModify($path);
                if ($applyChmod) {
                    chmod($internalPath, 0777);
                }
                return true;
            } else {
                throw new \RuntimeException("Cannot write in the file \"{$path}\"");
            }
        }
    }

    /**
     * Check if the file exists
     *
     * @param string $path The path to the file
     *
     * @return boolean
     */
    public function exists(string $path): bool
    {
        return file_exists($this->toInternalPath($path));
    }

    /**
     * Create a new file
     *
     * @param string $path The path of the new file
     * @param boolean $createParent Define if we have to create parent directories
     *
     * @throws \RuntimeException
     *
     * @return boolean
     */
    public function mkfile(string $path, bool $createParent = false): bool
    {
        $parentDir = $this->dirname($path);

        if (!$this->exists($parentDir)) {
            if ($createParent) {
                $this->mkdir($parentDir, true);
            } else {
                throw new \RuntimeException("Cannot create file \"{$path}\" (parent directory \"{$parentDir}\" doesn't exist)");
            }
        }

        $internalPath = $this->toInternalPath($path);

        if (touch($internalPath) === false) {
            throw new \RuntimeException("Cannot create file \"{$path}\"");
        } else {
            chmod($internalPath, 0777);
            $this->_onCreate($path);
            return true;
        }
    }

    /**
     * Get the parent directory of a file
     *
     * @param string $path The path to the file
     *
     * @return string
     */
    public function dirname(string $path): string
    {
        return $this->pathInfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Get an information about a specified path.
     *
     * @param string $path The path to get the information.
     * @param int $info The information to get about the path.
     *
     * @access protected
     *
     * @return string
     */
    protected function pathInfo(string $path, int $info): string
    {
        $internalPath = $this->cleanPath($path);

        switch ($info) {

            case PATHINFO_BASENAME :
                return basename($internalPath);

            case PATHINFO_EXTENSION :
                $basename = basename($internalPath);
                if (strrpos($basename, '.') !== false) {
                    return substr($basename, strrpos($basename, '.') + 1);
                } else {
                    if ($this->isDir($internalPath)) {
                        return "folder";
                    } else {
                        return "file";
                    }
                }
                break;

            case PATHINFO_FILENAME :
                $basename = basename($internalPath);
                return substr($basename, 0, strrpos($basename, '.'));

            case PATHINFO_DIRNAME:
                if (strpos($path, "/", 1) !== false) {
                    $dirname = preg_replace('#/[^/]*/?$#', '', $path);
                } elseif (strpos($path, "/") === 0) {
                    $dirname = "/";
                } else {
                    $dirname = false;
                }

                if ($dirname == ".") {
                    $dirname = false;
                }

                return $dirname;

            default:
                return "unknown";
        }
    }

    /**
     * Check if the file is a directory
     *
     * @param string $path The path to the file
     *
     * @return boolean
     */
    public function isDir(string $path): bool
    {
        return is_dir($this->toInternalPath($path));
    }

    /**
     * Create a new directory
     *
     * @param string $path The path of the new directory
     * @param bool $recursive Define if we have to create all parent directories
     *
     * @throws \RuntimeException
     *
     * @return boolean
     */
    public function mkdir(string $path, bool $recursive = false): bool
    {
        $internalPath = $this->toInternalPath($path);

        if ($this->exists($internalPath)) {
            throw new \RuntimeException("The directory already exists.");
        }

        $parentDir = $this->dirname($path);

        if (!$this->exists($parentDir)) {
            if ($recursive) {
                $this->mkdir($parentDir, true);
            } else {
                throw new \RuntimeException("Cannot create directory \"{$path}\" (parent directory \"{$parentDir}\" doesn't exist)");
            }
        }

        if (mkdir($internalPath, 0777) === false) {
            throw new \RuntimeException("Cannot create directory \"{$path}\"");
        } else {
            chmod($internalPath, 0777);
            $this->_onCreate($path);
            return true;
        }
    }

    /**
     * Rename the file
     *
     * @param string $path The current path of the file
     * @param string $new_name The new name of the file
     *
     * @throws \RuntimeException
     *
     * @return boolean
     */
    public function rename(string $path, string $new_name): bool
    {
        return $this->move($path, $this->makePath($this->dirname($path), $new_name));
    }

    /**
     * Move the file.
     *
     * @param string $path The current path of the file.
     * @param string $new_path The new path of the file.
     *
     * @throws \RuntimeException
     *
     * @return boolean
     */
    public function move(string $path, string $new_path): bool
    {
        if ($this->isDir($new_path) && !$this->isDir($path)) {
            $new_path = $this->makePath($new_path, $this->basename($path));
        }

        $destDirname = $this->dirname($new_path);

        if (!$this->exists($destDirname)) {
            $this->mkdir($destDirname, true);
        }

        $destInternalPath = $this->toInternalPath($new_path);
        $sourceInternalPath = $this->toInternalPath($path);

        if (!rename($sourceInternalPath, $destInternalPath)) {
            throw new \RuntimeException("Cannot move file from \"{$path}\" to \"{$new_path}\"");
        } else {
            $this->_onDelete($path);
            $this->_onCreate($new_path);
            return true;
        }
    }

    /**
     * Get the file's basename.
     *
     * @param string $path The path to the file.
     *
     * @return string
     */
    public function basename(string $path): string
    {
        return $this->pathInfo($path, PATHINFO_BASENAME);
    }

    /**
     * Copy a file
     *
     * @param string $path The path of the file to copy
     * @param string $new_path The path of the destination
     *
     * @throws \RuntimeException When the file can't be copied
     *
     * @return boolean
     */
    public function copy(string $path, string $new_path): bool
    {
        if ($this->isDir($new_path) && !$this->isDir($path)) {
            $new_path = $this->cleanPath($this->makePath($new_path, $this->basename($path)));
        }

        $destDirname = $this->dirname($new_path);

        if (!$this->exists($destDirname)) {
            throw new \RuntimeException("Cannot copy file from \"{$path}\" to \"{$new_path}\" : destination directory \"{$destDirname}\" doesn't exist");
        }

        $destInternalPath = $this->toInternalPath($new_path);
        $sourceInternalPath = $this->toInternalPath($path);

        if ($this->isDir($sourceInternalPath)) {

            if (!$this->exists($new_path)) {
                $this->mkdir($new_path);
            }

            $subfiles = $this->readDir($path);
            $res = false;

            foreach ($subfiles as $fileToCopyName => $fileToCopyPath) {
                $res = $this->copy($this->makePath($path, $fileToCopyName), $this->makePath($new_path, $fileToCopyName));

                if (!$res)
                    break;
            }

            return $res;
        } else {
            if (copy($sourceInternalPath, $destInternalPath) === false) {
                throw new \RuntimeException("Cannot copy file from \"{$path}\" to \"{$new_path}\"");
            } else {
                $this->_onCreate($new_path);
                return true;
            }
        }
    }

    /**
     * Read all elements in a directory
     *
     * @param string $path The path of the directory
     * @param boolean $recursive Define if the directory have to be read recursively
     * @param array $options Additional options to use :
     *                                  path_type => The type of file path;
     *                                  include   => The extension(s) of files to select;
     *                                  exclude   => The extension(s) of files to ignore.
     *
     * @return array
     */
    public function readDir(string $path, bool $recursive = false, array $options = array('path_type' => self::REAL_PATH, 'include' => false, 'exclude' => false)): array
    {
        if (!$this->isRemote($path) && !is_readable($this->toInternalPath($path))) {
            throw $this->accessDeniedException($path, 'read');
        }

        $options['path_type'] = !isset($options['path_type']) ? self::REAL_PATH : $options['path_type'];
        $options['include'] = !isset($options['include']) ? false : $options['include'];
        $options['exclude'] = !isset($options['exclude']) ? false : $options['exclude'];

        $path = $this->toInternalPath($path);
        $files = array();

        if ($handle = opendir($path)) {
            while (($file = readdir($handle)) !== false) {
                $filepath = $this->cleanPath($this->makePath($path, $file));

                // Removing dirty
                if ($file == '.' || $file == '..') {
                    continue;
                }
                // Applying filters
                if (is_string($options['exclude']) && $this->extension($filepath) == $options['exclude']) {
                    continue;
                }
                if (is_array($options['exclude']) && in_array($this->extension($filepath), $options['exclude'])) {
                    continue;
                }
                // Skipping unwanted files
                if (is_string($options['include']) && $this->extension($filepath) != $options['include']) {
                    continue;
                }
                if (is_array($options['include']) && !in_array($this->extension($filepath), $options['include'])) {
                    continue;
                }

                switch ($options['path_type']) {
                    default:
                    case self::REAL_PATH:
                        $files[$file] = $filepath;
                        break;

                    case self::INTERNAL_PATH:
                        $files[$file] = $this->toInternalPath($filepath);
                        break;

                    case self::EXTERNAL_PATH:
                        $files[$file] = $this->toExternalPath($filepath);
                        break;

                    case self::FILESYSTEM_PATH:
                        $files[$file] = $this->toFileSystemPath($filepath);
                        break;
                }

                if ($recursive === true && $this->isDir($filepath)) {
                    $subfiles = $this->readDir($filepath, $recursive, $options);
                    foreach ($subfiles as $subfilename => $subfilepath) {
                        $files[$this->makePath($file, $subfilename)] = $subfilepath;
                    }
                }
            }
            closedir($handle);

            ksort($files);

            return $files;
        } else {
            throw $this->accessDeniedException($path, 'open');
        }
    }

    /**
     * Get the file's extension
     *
     * @param string $path the path to the file
     *
     * @return string
     */
    public function extension(string $path): string
    {
        return $this->pathInfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Convert an internal path to an external path.
     *
     * @param  string $internalPath The internal path.
     *
     * @return string
     */
    public function toExternalPath(string $internalPath): string
    {
        $externalPath = $internalPath;

        if ($this->isRemote($externalPath)) {
            return $externalPath;
        }

        if (substr($externalPath, 0, strlen($this->rootPath())) == $this->rootPath()) {
            $externalPath = substr($externalPath, strlen($this->rootPath()));
        } else {
            $realRootPath = realpath($this->rootPath());
            if (substr($externalPath, 0, strlen($realRootPath)) == $realRootPath) {
                $externalPath = substr($externalPath, strlen($realRootPath));
            }
        }

        if ($externalPath[0] != '/') {
            return $internalPath;
        }

        $nbrTurns = 0;
        $maxNbrTurns = count($this->aliases);
        do {
            $appliedAliasesNbr = 0;

            foreach ($this->aliases as $key => $value) {
                $value = '/' . $value;
                if (substr($externalPath, 0, strlen($value)) == $value) {
                    $externalPath = $this->makePath($key, substr($externalPath, strlen($value)));
                    $appliedAliasesNbr++;
                }
            }

            $nbrTurns++;
        } while ($appliedAliasesNbr > 0 && $nbrTurns <= $maxNbrTurns);

        return $this->cleanPath($externalPath);
    }

    /**
     * Convert an internal path to an absolute path
     * which start on the filesystem root path.
     *
     * @param  string $internalPath The internal path.
     *
     * @return string
     */
    public function toFileSystemPath(string $internalPath): string
    {
        $externalPath = $internalPath;

        if ($this->isRemote($externalPath)) {
            return $externalPath;
        }

        if (substr($externalPath, 0, strlen($this->rootPath())) == $this->rootPath()) {
            $externalPath = substr($externalPath, strlen($this->rootPath()));
        } else {
            $realRootPath = realpath($this->rootPath());
            if (substr($externalPath, 0, strlen($realRootPath)) == $realRootPath) {
                $externalPath = substr($externalPath, strlen($realRootPath));
            }
        }

        // Apply aliases
        $nbrTurns = 0;
        $maxNbrTurns = count($this->aliases);
        do {
            $appliedAliasesNbr = 0;

            foreach ($this->aliases as $key => $value) {
                if (substr($externalPath, 0, strlen($key)) == $key) {
                    $externalPath = $this->makePath($value, substr($externalPath, strlen($key)));
                    $appliedAliasesNbr++;
                }
            }

            $nbrTurns++;
        } while ($appliedAliasesNbr > 0 && $nbrTurns <= $maxNbrTurns);

        while (substr($externalPath, 0, 1) == '/' || substr($externalPath, 0, 2) == './') {
            $externalPath = substr($externalPath, 1);
        }

        return $this->cleanPath($externalPath);
    }

    /**
     * Create a new temporary file.
     *
     * @param string $prefix The prefix of the temporary
     *                       file to create.
     *
     * @return string The temporary file path.
     */
    public function tmpfile(string $prefix = "tmp"): string
    {
        $tmpDir = $this->toInternalPath($this->tempDir);

        if (!$this->isDir($tmpDir)) {
            $this->mkdir($tmpDir, true);
        }

        $tmpFile = tempnam($tmpDir, $prefix);

        $fileManager = $this;

        register_shutdown_function(function () use ($tmpFile, $fileManager) {
            if ($fileManager->exists($tmpFile)) {
                $fileManager->delete($tmpFile, true);
            }
        });

        return $tmpFile;
    }

    /**
     * Delete the file.
     *
     * @param string $path The path to the file to delete.
     * @param bool $recursive Define if we have to delete all subfiles.
     *
     * @throws \RuntimeException
     *
     * @return boolean
     */
    public function delete(string $path, bool $recursive = false): bool
    {
        $internalPath = $this->toInternalPath($path);

        if ($this->exists($path)) {
            if ($this->isDir($path)) {
                $subfiles = $this->readDir($path);
                if ($recursive === true) {
                    foreach ($subfiles as $fileToDelete) {
                        $this->delete($fileToDelete, $recursive);
                    }
                } else if (count($subfiles) > 0) {
                    throw new \RuntimeException("Cannot delete directory \"{$path}\". The directory is not empty.");
                }

                if (rmdir($internalPath) === false) {
                    throw new \RuntimeException("Cannot delete directory \"{$path}\".");
                } else {
                    $this->_onDelete($path);
                    return true;
                }
            } else {
                if (unlink($internalPath) === false) {
                    throw new \RuntimeException("Cannot delete file \"{$path}\".");
                } else {
                    $this->_onDelete($path);
                    return true;
                }
            }
        } else {
            throw new \RuntimeException("The file {$path} doesn't exist.");
        }
    }

    /**
     * Get the file's last modification time.
     *
     * @param string $path The path to the file.
     *
     * @return int
     */
    public function lastModTime(string $path): int
    {
        return filemtime($this->toInternalPath($path));
    }

    /**
     * Get the file's last access time.
     *
     * @param string $path The path to the file.
     *
     * @return int
     */
    public function lastAccessTime(string $path): int
    {
        return fileatime($this->toInternalPath($path));
    }

    /**
     * Get the file's name without extension.
     *
     * @param string $path the path to the file.
     *
     * @return string
     */
    public function filename(string $path): string
    {
        return $this->pathInfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get the file's size in octets.
     *
     * @param string $path The path to the file.
     *
     * @return string
     */
    public function sizeInOctets(string $path): string
    {
        $size = $this->size($path);
        $unit = 'b';

        if ($size > 1024) {
            $size = $size / 1024;
            $unit = 'Kb';
        }
        if ($size > 1024) {
            $size = $size / 1024;
            $unit = 'Mb';
        }
        if ($size > 1024) {
            $size = $size / 1024;
            $unit = 'Gb';
        }
        if ($size > 1024) {
            $size = $size / 1024;
            $unit = 'Tb';
        }

        return round($size, 2) . $unit;
    }

    /**
     * Get the file's size.
     *
     * @param string $path The path to the file.
     *
     * @return int
     */
    public function size(string $path): int
    {
        if ($this->isDir($path)) {
            $totalSize = 0;
            $files = $this->readDir($path);

            foreach ($files as $filePath) {
                $totalSize += $this->size($filePath);
            }

            return $totalSize;
        } else {
            return filesize($this->toInternalPath($path));
        }
    }

    /**
     * Check if a file is a binary file.
     *
     * @param  string $path The file path.
     *
     * @return boolean
     */
    public function isBinary(string $path): bool
    {
        $mime = $this->mimeType($path);
        return (substr($mime, 0, 5) !== "text/");
    }

    /**
     * Get the file's MIME type.
     *
     * @param string $path The file's path.
     * @param int $index The index of the mime types array to retrieve.
     *
     * @return string
     */
    public function mimeType(string $path, int $index = 0): string
    {
        $fileExtension = $this->extension($path);

        $mimes =& MimeTypes::get();

        foreach ($mimes as $extension => $mime) {
            if ($extension === $fileExtension) {
                if (is_array($mime))
                    return $mime[$index];

                return $mime;
            }
        }

        if (!class_exists('finfo'))
            return "application/octet-stream";

        $finfo = new \finfo(FILEINFO_MIME);

        if (!$finfo)
            return "application/octet-stream";

        return $finfo->file($this->toInternalPath($path));
    }

    /**
     * Remove the hostname from the path.
     *
     * @param string $path The path to remove the hostname.
     *
     * @return string
     */
    public function removeHostFromPath(string $path): string
    {
        return parse_url($path, PHP_URL_PATH);
    }

    /**
     * Gets the file entity at the given path.
     *
     * @param string $path
     *
     * @return File|Folder
     *
     * @throws Exceptions\FileSystemEntityNotFoundException
     */
    public function getEntity(string $path)
    {
        if ($this->exists($path)) {
            return $this->isDir($path) ? new Folder($path, $this) : new File($path, $this);
        } else {
            throw new FileSystemEntityNotFoundException($path);
        }
    }

    /**
     * Raise a "create" event.
     *
     * @param string $path The path of the entity which raised the event.
     */
    private function _onCreate(string $path)
    {
        if ($this->listener instanceof IFileSystemListener) {
            $this->listener->onCreated(new FileSystemEntityCreatedEvent($path));
        }
    }

    /**
     * Raise a "modify" event.
     *
     * @param string $path The path of the entity which raised the event.
     */
    private function _onModify(string $path)
    {
        if ($this->listener instanceof IFileSystemListener) {
            $this->listener->onCreated(new FileSystemEntityModifiedEvent($path));
        }
    }

    /**
     * Raise a "delete" event.
     *
     * @param string $path The path of the entity which raised the event.
     */
    private function _onDelete(string $path)
    {
        if ($this->listener instanceof IFileSystemListener) {
            $this->listener->onCreated(new FileSystemEntityDeletedEvent($path));
        }
    }
}
