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

/**
 * Mime Types
 *
 * Registry class used to store extensions
 * and their associated mime types.
 *
 * @package     FireFS
 * @author      Nana Axel <ax.lnana@outlook.com>
 */
abstract class MimeTypes
{
    private static $_mimes = array(
        '3g2'   =>  'video/3gpp2',
        '3gp'   =>  array('video/3gp', 'video/3gpp'),
        '7zip'  =>  array('application/x-compressed', 'application/x-zip-compressed', 'application/zip', 'multipart/x-zip'),
        'aac'   =>  'audio/x-acc',
        'ac3'   =>  'audio/ac3',
        'ai'    =>  array('application/pdf', 'application/postscript'),
        'aif'   =>  array('audio/x-aiff', 'audio/aiff'),
        'aifc'  =>  'audio/x-aiff',
        'aiff'  =>  array('audio/x-aiff', 'audio/aiff'),
        'au'    =>  'audio/x-au',
        'avi'   =>  array('video/x-msvideo', 'video/msvideo', 'video/avi', 'application/x-troff-msvideo'),
        'bin'   =>  array('application/macbinary', 'application/mac-binary', 'application/octet-stream', 'application/x-binary', 'application/x-macbinary'),
        'bmp'   =>  array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
        'cdr'   =>  array('application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw', 'image/cdr', 'image/x-cdr', 'zz-application/zz-winassoc-cdr'),
        'cer'   =>  array('application/pkix-cert', 'application/x-x509-ca-cert'),
        'class' =>  'application/octet-stream',
        'cpt'   =>  'application/mac-compactpro',
        'crl'   =>  array('application/pkix-crl', 'application/pkcs-crl'),
        'crt'   =>  array('application/x-x509-ca-cert', 'application/x-x509-user-cert', 'application/pkix-cert'),
        'csr'   =>  'application/octet-stream',
        'css'   =>  'text/css',
        'csv'   =>  array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain'),
        'dcr'   =>  'application/x-director',
        'der'   =>  'application/x-x509-ca-cert',
        'dir'   =>  'application/x-director',
        'dll'   =>  'application/octet-stream',
        'dms'   =>  'application/octet-stream',
        'doc'   =>  array('application/msword', 'application/vnd.ms-office'),
        'docx'  =>  array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword', 'application/x-zip'),
        'dot'   =>  array('application/msword', 'application/vnd.ms-office'),
        'dotx'  =>  array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/msword'),
        'dvi'   =>  'application/x-dvi',
        'dxr'   =>  'application/x-director',
        'eml'   =>  'message/rfc822',
        'eps'   =>  'application/postscript',
        'exe'   =>  array('application/octet-stream', 'application/x-msdownload'),
        'f4v'   =>  'video/mp4',
        'flac'  =>  'audio/x-flac',
        'gif'   =>  'image/gif',
        'gpg'   =>  'application/gpg-keys',
        'gtar'  =>  'application/x-gtar',
        'gz'    =>  'application/x-gzip',
        'gzip'  =>  'application/x-gzip',
        'hqx'   =>  array('application/mac-binhex40', 'application/mac-binhex', 'application/x-binhex40', 'application/x-mac-binhex40'),
        'htm'   =>  'text/html',
        'html'  =>  'text/html',
        'ical'  =>  'text/calendar',
        'ico'   =>  array('image/x-icon', 'image/x-ico', 'image/vnd.microsoft.icon'),
        'ics'   =>  'text/calendar',
        'jar'   =>  array('application/java-archive', 'application/x-java-application', 'application/x-jar', 'application/x-compressed'),
        'jpe'   =>  array('image/jpeg', 'image/pjpeg'),
        'jpeg'  =>  array('image/jpeg', 'image/pjpeg'),
        'jpg'   =>  array('image/jpeg', 'image/pjpeg'),
        'js'    =>  'text/javascript',
        'json'  =>  array('application/json', 'text/json'),
        'kdb'   =>  'application/octet-stream',
        'kml'   =>  array('application/vnd.google-earth.kml+xml', 'application/xml', 'text/xml'),
        'kmz'   =>  array('application/vnd.google-earth.kmz', 'application/zip', 'application/x-zip'),
        'lha'   =>  'application/octet-stream',
        'log'   =>  array('text/plain', 'text/x-log'),
        'lzh'   =>  'application/octet-stream',
        'm3u'   =>  'text/plain',
        'm4a'   =>  'audio/x-m4a',
        'm4u'   =>  'application/vnd.mpegurl',
        'mid'   =>  'audio/midi',
        'midi'  =>  'audio/midi',
        'mif'   =>  'application/vnd.mif',
        'mov'   =>  'video/quicktime',
        'movie' =>  'video/x-sgi-movie',
        'mp2'   =>  'audio/mpeg',
        'mp3'   =>  array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
        'mp4'   =>  'video/mp4',
        'mpe'   =>  'video/mpeg',
        'mpeg'  =>  'video/mpeg',
        'mpg'   =>  'video/mpeg',
        'mpga'  =>  'audio/mpeg',
        'oda'   =>  'application/oda',
        'ogg'   =>  'audio/ogg',
        'p10'   =>  array('application/x-pkcs10', 'application/pkcs10'),
        'p12'   =>  'application/x-pkcs12',
        'p7a'   =>  'application/x-pkcs7-signature',
        'p7c'   =>  array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
        'p7m'   =>  array('application/pkcs7-mime', 'application/x-pkcs7-mime'),
        'p7r'   =>  'application/x-pkcs7-certreqresp',
        'p7s'   =>  'application/pkcs7-signature',
        'pdf'   =>  array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
        'pem'   =>  array('application/x-x509-user-cert', 'application/x-pem-file', 'application/octet-stream'),
        'pgp'   =>  'application/pgp',
        'php'   =>  array('application/x-httpd-php', 'application/php', 'application/x-php', 'text/php', 'text/x-php', 'application/x-httpd-php-source'),
        'php3'  =>  'application/x-httpd-php',
        'php4'  =>  'application/x-httpd-php',
        'phps'  =>  'application/x-httpd-php-source',
        'phtml' =>  'application/x-httpd-php',
        'png'   =>  array('image/png',  'image/x-png'),
        'ppt'   =>  array('application/powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-office', 'application/msword'),
        'pptx'  =>  array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/x-zip', 'application/zip'),
        'ps'    =>  'application/postscript',
        'psd'   =>  array('application/x-photoshop', 'image/vnd.adobe.photoshop'),
        'qt'    =>  'video/quicktime',
        'ra'    =>  'audio/x-realaudio',
        'ram'   =>  'audio/x-pn-realaudio',
        'rar'   =>  array('application/x-rar', 'application/rar', 'application/x-rar-compressed'),
        'rm'    =>  'audio/x-pn-realaudio',
        'rpm'   =>  'audio/x-pn-realaudio-plugin',
        'rsa'   =>  'application/x-pkcs7',
        'rtf'   =>  'text/rtf',
        'rtx'   =>  'text/richtext',
        'rv'    =>  'video/vnd.rn-realvideo',
        'sea'   =>  'application/octet-stream',
        'shtml' =>  'text/html',
        'sit'   =>  'application/x-stuffit',
        'smi'   =>  'application/smil',
        'smil'  =>  'application/smil',
        'so'    =>  'application/octet-stream',
        'srt'   =>  array('text/srt', 'text/plain'),
        'sst'   =>  'application/octet-stream',
        'svg'   =>  array('image/svg+xml', 'application/xml', 'text/xml'),
        'swf'   =>  'application/x-shockwave-flash',
        'tar'   =>  'application/x-tar',
        'text'  =>  'text/plain',
        'tgz'   =>  array('application/x-tar', 'application/x-gzip-compressed'),
        'tif'   =>  'image/tiff',
        'tiff'  =>  'image/tiff',
        'txt'   =>  'text/plain',
        'vcf'   =>  'text/x-vcard',
        'vlc'   =>  'application/videolan',
        'vtt'   =>  array('text/vtt', 'text/plain'),
        'wav'   =>  array('audio/x-wav', 'audio/wave', 'audio/wav'),
        'wbxml' =>  'application/wbxml',
        'webm'  =>  'video/webm',
        'wma'   =>  array('audio/x-ms-wma', 'video/x-ms-asf'),
        'wmlc'  =>  'application/wmlc',
        'wmv'   =>  array('video/x-ms-wmv', 'video/x-ms-asf'),
        'word'  =>  array('application/msword', 'application/octet-stream'),
        'xht'   =>  'application/xhtml+xml',
        'xhtml' =>  'application/xhtml+xml',
        'xl'    =>  'application/excel',
        'xls'   =>  array('application/vnd.ms-excel', 'application/msexcel', 'application/x-msexcel', 'application/x-ms-excel', 'application/x-excel', 'application/x-dos_ms_excel', 'application/xls', 'application/x-xls', 'application/excel', 'application/download', 'application/vnd.ms-office', 'application/msword'),
        'xlsx'  =>  array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/vnd.ms-excel', 'application/msword', 'application/x-zip'),
        'xml'   =>  array('application/xml', 'text/xml', 'text/plain'),
        'xsl'   =>  array('application/xml', 'text/xsl', 'text/xml'),
        'xspf'  =>  'application/xspf+xml',
        'z'     =>  'application/x-compress',
        'zip'   =>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/s-compressed', 'multipart/x-zip'),
        'zsh'   =>  'text/x-scriptzsh'
    );

    public static function exists(string $extension): bool
    {
        return array_key_exists($extension, self::$_mimes);
    }

    public static function register(string $extension, string $mime)
    {
        if (self::exists($extension))
        {
            if (is_array(self::$_mimes[$extension]))
                array_push(self::$_mimes[$extension], $mime);
            else
                self::$_mimes[$extension] = array(self::$_mimes[$extension], $mime);
        }
        else
        {
            self::$_mimes[$extension] = $mime;
        }
    }

    public static function remove(string $extension)
    {
        if (self::exists($extension))
            unset(self::$_mimes[$extension]);
    }

    public static function &get()
    {
        return self::$_mimes;
    }
}