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

namespace ElementaryFramework\FireFS\Entities;

use ElementaryFramework\FireFS\Exceptions\FileSystemEntityNotFoundException;
use ElementaryFramework\FireFS\FireFS;

/**
 * File System Entity
 *
 * Abstract base class used to implement all others entities
 * (fils, folders, etc...)
 *
 * @package    FireFS
 * @subpackage Entities
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
abstract class FileSystemEntity
{
    /**
     * The path to the managed entity in the file system.
     *
     * @var string
     */
    protected $_path;

    /**
     * The file system manager instance.
     *
     * @var FireFS
     */
    protected $_fs;

    /**
     * FileSystemEntity constructor.
     *
     * @param string $path The path to the FS entity.
     * @param FireFS $fs   The filesystem instance.
     *
     * @throws FileSystemEntityNotFoundException When the entity was not found on the given filesystem.
     */
    public function __construct(string $path, FireFS &$fs)
    {
        $this->_path = $path;
        $this->_fs =& $fs;

        if (!$this->_fs->exists($this->_path)) {
            throw new FileSystemEntityNotFoundException($this->_path);
        }
    }

    /**
     * Gets the path to this entity.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Gets the name of this entity.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_fs->basename($this->_path);
    }

    /**
     * Creates this entity on the file system.
     *
     * @return bool
     */
    public abstract function create(): bool;

    /**
     * Deletes this entity from the file system.
     *
     * @return bool
     */
    public function delete(): bool
    {
        return $this->_fs->delete($this->_path, true);
    }

    /**
     * Renames the entity.
     *
     * @param string $newName The new name of the entity.
     *
     * @return bool
     */
    public function rename(string $newName): bool
    {
        return $this->_fs->rename($this->_path, $newName);
    }

    /**
     * Creates a copy of this entity into the given folder.
     *
     * @param Folder $folder The folder which will contain the copy;
     *
     * @return bool
     */
    public function copyToFolder(Folder $folder): bool
    {
        return $this->copyToPath($folder->_path);
    }

    /**
     * Creates a copy of this entity.
     *
     * @param string $path The path of the copy of the entity
     *
     * @return bool
     */
    public function copyToPath(string $path): bool
    {
        return $this->_fs->copy($this->_path, $path);
    }

    /**
     * Moves this entity into another folder.
     *
     * @param Folder $folder The new folder of the entity.
     *
     * @return bool
     */
    public function moveToFolder(Folder $folder): bool
    {
        return $this->moveToPath($folder->_path);
    }

    /**
     * Moves this entity to another path.
     *
     * @param string $path The new path of the entity
     *
     * @return bool
     */
    public function moveToPath(string $path): bool
    {
        return $this->_fs->move($this->_path, $path);
    }

    /**
     * Gets the internal path to this entity.
     *
     * @return string
     */
    public function getInternalPath(): string
    {
        return $this->_fs->toInternalPath($this->_path);
    }

    /**
     * Gets the external path to this entity.
     *
     * @return string
     */
    public function getExternalPath(): string
    {
        return $this->_fs->toExternalPath($this->_path);
    }

    /**
     * Gets the filesystem path to this entity.
     *
     * @return string
     */
    public function getFileSystemPath(): string
    {
        return $this->_fs->toFileSystemPath($this->_path);
    }

    /**
     * Gets the parent folder which contains this entity.
     *
     * @throws FileSystemEntityNotFoundException
     *
     * @return Folder
     */
    public function getParent(): Folder
    {
        return new Folder($this->_fs->dirname($this->_path), $this->_fs);
    }

    /**
     * Gets the size of this entity.
     *
     * @return integer
     */
    public function getSize(): int
    {
        return $this->_fs->size($this->_path);
    }

    /**
     * Gets the size of this entity, formatted in octets.
     *
     * @return string
     */
    public function getSizeInOctets(): string
    {
        return $this->_fs->sizeInOctets($this->_path);
    }

    /**
     * Gets the last modification time of this entity.
     *
     * @return integer
     */
    public function getLastModificationTime(): int
    {
        return $this->_fs->lastModTime($this->_path);
    }

    /**
     * Gets the last access time of this entity.
     *
     * @return integer
     */
    public function getLastAccessTime(): int
    {
        return $this->_fs->lastAccessTime($this->_path);
    }

    /**
     * Checks if the current entity is stored
     * in a remote filesystem.
     *
     * @return bool
     */
    public function isRemote(): bool
    {
        return $this->_fs->isRemote($this->_path);
    }
}