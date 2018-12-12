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

/**
 * File
 *
 * Represent a file in the file system.
 *
 * @package    FireFS
 * @subpackage Entities
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
class File extends FileSystemEntity
{
    /**
     * @inheritdoc
     */
    public function create(): bool
    {
        return $this->_fs->mkfile($this->_path, true);
    }

    /**
     * Gets the extension of this file.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->_fs->extension($this->_path);
    }

    /**
     * Checks if the file is binary or not.
     *
     * @return bool
     */
    public function isBinary(): bool
    {
        return $this->_fs->isBinary($this->_path);
    }

    /**
     * Gets the mime type of this file.
     *
     * @param int $index The mime type index from the registry.
     *
     * @return string
     */
    public function getMimeType(int $index = 0): string
    {
        return $this->_fs->mimeType($this->_path, $index);
    }

    /**
     * Read the file and return the content as string.
     *
     * @return string
     */
    public function read(): string
    {
        return $this->_fs->read($this->_path);
    }

    /**
     * Writes data into the file.
     *
     * @param string $data   The data to write into the file.
     * @param bool   $append Define if the data have to be appended at the
     *                       end of the file (true), or overwrite the file
     *                       content (false).
     *
     * @return bool
     */
    public function write(string $data, bool $append = false): bool
    {
        return $this->_fs->write($this->_path, $data, $append);
    }
}