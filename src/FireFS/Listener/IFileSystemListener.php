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

namespace ElementaryFramework\FireFS\Listener;

use ElementaryFramework\FireFS\Events\FileSystemEvent;


/**
 * File System Listener Interface
 *
 * Describes a file system listener class.
 *
 * @package    FireFS
 * @subpackage Listener
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
interface IFileSystemListener
{
    /**
     * Action called on any event.
     *
     * @param FileSystemEvent $event The raised event.
     *
     * @return boolean true to propagate the event, false otherwise.
     */
    function onAny(FileSystemEvent $event): bool;

    /**
     * Action called when a "create" event occurs on
     * the file system.
     *
     * @param FileSystemEvent $event The raised event.
     *
     * @return void
     */
    function onCreated(FileSystemEvent $event);

    /**
     * Action called when a "modify" event occurs on
     * the file system.
     *
     * @param FileSystemEvent $event The raised event.
     *
     * @return void
     */
    function onModified(FileSystemEvent $event);

    /**
     * Action called when a "delete" event occurs on
     * the file system.
     *
     * @param FileSystemEvent $event The raised event.
     *
     * @return void
     */
    function onDeleted(FileSystemEvent $event);
}