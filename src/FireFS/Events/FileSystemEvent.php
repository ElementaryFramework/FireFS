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

namespace ElementaryFramework\FireFS\Events;

/**
 * File System Event
 *
 * Represent an event occurred in the file system manager.
 *
 * @package    FireFS
 * @subpackage Exceptions
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
abstract class FileSystemEvent
{
    /**
     * Represent an unknown event.
     */
    const EVENT_UNKNOWN = 0;

    /**
     * Represent a "create" event.
     */
    const EVENT_CREATE = 1;

    /**
     * Represent a "modify" event.
     */
    const EVENT_MODIFY = 2;

    /**
     * Represent a "delete" event.
     */
    const EVENT_DELETE = 3;

    /**
     * The path to the entity which raised the event.
     *
     * @var string
     */
    protected $_path;

    /**
     * The type of the raised event.
     *
     * @var integer
     */
    protected $_eventType;

    /**
     * Creates a new file system event
     *
     * @param integer $type The event type.
     * @param string  $path The path to the file system entity
     *                      which raised the event
     */
    public function __construct(int $type, string $path)
    {
        $this->_eventType = $type;
        $this->_path = $path;
    }

    /**
     * Returns the type of this event.
     *
     * @return integer
     */
    public function getEventType(): int
    {
        return $this->_eventType;
    }

    /**
     * Returns the path of the entity which raised
     * this event.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }
}