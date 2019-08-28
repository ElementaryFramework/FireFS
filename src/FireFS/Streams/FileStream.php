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

namespace ElementaryFramework\FireFS\Streams;

use ElementaryFramework\Core\Events\WithEvents;
use ElementaryFramework\Core\Streams\Events\StreamEvent;
use ElementaryFramework\Core\Streams\IReadableStream;
use ElementaryFramework\Core\Streams\ISeekableStream;
use ElementaryFramework\Core\Streams\IWritableStream;
use ElementaryFramework\FireFS\Exceptions\FileStreamException;

/**
 * File Stream
 *
 * Class used to read and write files through a stream for
 * async I/O under FireFS
 *
 * @package    FireFS
 * @subpackage Streams
 * @author     Axel Nana <ax.lnana@outlook.com>
 */
class FileStream implements IReadableStream, IWritableStream, ISeekableStream
{
    use WithEvents;

    /**
     * Stream handle.
     *
     * @var resource
     */
    private $_handle;

    /**
     * The path to the file handled by this stream.
     *
     * @var string
     */
    private $_path;

    /**
     * The list of readable streams piped to this one.
     *
     * @var array
     */
    private $_pipedStreams = array();

    /**
     * Defines if the stream is closed or not.
     *
     * @var boolean
     */
    private $_closed;

    /**
     * Defines if the stream is paused or not.
     *
     * @var boolean
     */
    private $_paused;

    /**
     * Creates a new instance of the FileStream class.
     *
     * @param string  $path   The path to the file.
     * @param boolean $append Defines if the stream cursor start at
     *                        the end (true) or at the beginning (false)
     *                        of the file.
     */
    public function __construct(string $path, bool $append)
    {
        if (($result = fopen($path, $append ? "a+" : "w+")) !== false) {
            $this->_handle = $result;
        } else {
            throw new FileStreamException("Unable to create the file stream.", $path);
        }

        $this->_path = $path;
        $this->_closed = false;
        $this->_paused = false;

        $this->on(
            StreamEvent::EVENT_PIPE,
            [$this, "_onPipe"]
        );
    }

    private function _onPipe(IReadableStream $source)
    {
        $closure = \Closure::bind(function($data) {
            if (!$this->_paused) {
                $this->write($data);
            }
        }, $this);

        $source->on(
            StreamEvent::EVENT_DATA,
            $closure
        );
    }

    /**
     * @inheritDoc
     */
    public function close() : void
    {
        if ($this->_closed)
            return;

        if ($this->_closed = fclose($this->_handle)) {
            $this->emit(StreamEvent::EVENT_CLOSE);
        } else {
            $this->emit(
                StreamEvent::EVENT_ERROR,
                new FileStreamException("Unable to close the stream.", $this->_path)
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function isReadable() : bool
    {
        return !$this->_closed;
    }

    /**
     * @inheritDoc
     */
    public function isWritable() : bool
    {
        return !$this->_closed;
    }

    /**
     * @inheritDoc
     */
    public function pause() : void
    {
        if ($this->_paused)
            return;

        $this->_paused = true;
    }

    /**
     * @inheritDoc
     */
    public function resume() : void
    {
        if (!$this->_paused)
            return;

        $this->_paused = false;
    }

    /**
     * @inheritDoc
     */
    public function pipe(IWritableStream $destination, bool $autoEnd = true) : IReadableStream
    {
        if (!$this->isReadable())
            return null;

        if (!$destination->isWritable())
            $this->pause();

        if ($autoEnd) {
            $this->on(
                StreamEvent::EVENT_END,
                function ($data) use (&$destination) {
                    $destination->end($data);
                }
            );
        }

        $destination->emit(
            StreamEvent::EVENT_PIPE,
            $this
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function read(int $length) : void
    {
        if ($this->isReadable()) {
            $data = fread($this->_handle, $length);

            if ($data === false) {
                $this->emit(
                    StreamEvent::EVENT_ERROR,
                    new FileStreamException("Unable to read the stream.", $this->_path)
                );
            } else {
                $this->emit(
                    StreamEvent::EVENT_DATA,
                    $data
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function write($data) : bool
    {
        if (!$this->isWritable())
            return false;

        if (fwrite($this->_handle, $data) === false)
            return false;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function end($data = null) : void
    {
        if ($data !== null)
            $this->write($data);

        $this->close();

        $this->emit(
            StreamEvent::EVENT_END,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function seekTo(int $offset, int $whence = SEEK_SET) : void
    {
        if (!$this->_closed) {
            fseek($this->_handle, $offset, $whence);
        }
    }

    /**
     * @inheritDoc
     */
    public function seekToStart() : void
    {
        $this->seekTo(0);
    }

    /**
     * @inheritDoc
     */
    public function seekToEnd() : void
    {
        $this->seekTo(0, SEEK_END);
    }

    /**
     * Closes the stream when the instance is being destructed.
     */
    function __destruct()
    {
        $this->close();
    }
}
