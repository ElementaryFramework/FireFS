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
 * Class Folder
 *
 * Represent a folder in the file system.
 *
 * @package    FireFS
 * @subpackage Entities
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
class Folder extends FileSystemEntity
{
    /**
     * @inheritdoc
     */
    public function create(): bool
    {
        return $this->_fs->mkdir($this->_path, true);
    }

    /**
     * Reads the folder and return the list of entities
     * contained in it.
     *
     * @param bool  $recursive Define if we have to read the folder recursively. Defaults to false.
     * @param array $options   An array of options for the directory reader. Options are:
     *  - `include` which is an array of extensions to lookup in the directory and subdirectories if any.
     *  - `exclude` which is an array of extensions to ignore in the directory and subdirectories if any.
     * Both options are optionals.
     *
     * @return FileSystemEntity[] The list of entities in the folder.
     *
     * @throws Exceptions\FileSystemEntityNotFoundException
     */
    public function read(bool $recursive = false, array $options = array('include' => false, 'exclude' => false)): array
    {
        $content = $this->_fs->readDir($this->_path, $recursive, $options);

        $toReturn = array();

        foreach ($content as $entityName => $entityPath) {
            if ($this->_fs->isDir($entityPath)) {
                $toReturn[$entityName] = new Folder($entityPath, $this->_fs);
            } else {
                $toReturn[$entityName] = new File($entityPath, $this->_fs);
            }
        }

        return $toReturn;
    }
}