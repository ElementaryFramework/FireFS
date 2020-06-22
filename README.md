<center>

![FireFS Logo](assets/banner.png)

</center>

# FireFS

[![downloads](https://img.shields.io/packagist/dt/elementaryframework/fire-fs?style=for-the-badge&logo=packagist)](https://packagist.org/packages/elementaryframework/fire-fs)
[![downloads](https://img.shields.io/packagist/v/elementaryframework/fire-fs?style=for-the-badge&logo=packagist)](https://packagist.org/packages/elementaryframework/fire-fs)
[![downloads](https://img.shields.io/github/repo-size/ElementaryFramework/FireFS?style=for-the-badge&logo=github)](https://github.com/ElementaryFramework/FireFS)
[![downloads](https://img.shields.io/github/license/ElementaryFramework/FireFS?style=for-the-badge&logo=github)](https://github.com/ElementaryFramework/FireFS/blob/master/LICENSE)

Manage your file system easily, through php

**FireFS** is a library allowing you to write/read/delete files and folders of your file system, safely and easily.

It can be used for *web* applications as well for *console* applications, without any requirements.

## Example

```php
<?php

use ElementaryFramework\FireFS\FireFS;

// Create a new file system instance at the given path
$fs = new FireFS("./app"); // /root/var/www/htdocs/app/

// Check if the path "/root/var/www/htdocs/app/images/" exists
if ($fs->exists("images")) {
    // Change the working directory to the images folder
    $fs->setWorkingDirectory("./images");

    // Create a new file in the working directory
    $fs->mkfile("./logo.png"); // /root/var/www/htdocs/app/images/logo.png

    // Read file from the file system root path
    $logo = $fs->read("logo.png"); // /root/var/www/htdocs/app/logo.png

    // Write into the created file
    $fs->write("./logo.png", $logo); // /root/var/www/htdocs/app/images/logo.png

    // Delete the old file
    $fs->delete("logo.png"); // /root/var/www/htdocs/app/logo.png
}

// Change the working directory to the file system root path
$fs->setWorkingDirectory("./");

// Create a "blog" directory
$fs->mkdir("blog"); // /root/var/www/htdocs/app/blog/

// Move "images" folder from "app" to "app/blog"
$fs->move("images", "blog/images");

// And more !
```

## Features

- Easy file system management ;
- Object Oriented file system entities management, through [Folder](https://github.com/ElementaryFramework/FireFS/blob/master/src/FireFS/Entities/Folder.php) and [File](https://github.com/ElementaryFramework/FireFS/blob/master/src/FireFS/Entities/File.php) classes ;
- Receive events of what happen to your file system (created, modified, deleted events) and execute a specific action with the [file system listener](https://github.com/ElementaryFramework/FireFS/blob/master/src/FireFS/Listener/IFileSystemListener.php) ;
- Run a [file system watcher](https://github.com/ElementaryFramework/FireFS/blob/master/src/FireFS/Watcher/FileSystemWatcher.php) to watch for files changes in **real time** (recommended for console applications or long running PHP processes)

## Installation

You can install **FireFS** in your project with [composer](http://getcomposer.org):

```sh
composer require elementaryframework/fire-fs
```

Once installed, you can access the **FireFS** api through the `ElementaryFramework\FireFS` namespace.

## How to use

New to **FireFS** ? From console to web apps, you can read the [wiki](https://github.com/ElementaryFramework/FireFS/wiki) to know how to use this
library into your project.

### Additional resources and tutorials

- [How to create a file watcher with PHP and the Elementary Framework](https://dev.to/na2axl/how-to-create-a-file-watcher-with-php-and-the-elementary-framework-1po) written by @na2axl on dev.to

## Donate

Liking Elementary Framework? Help us continue to maintain it and provide you better support and awesome functionalities with a small donation!

[![Donate PayPal Button](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AGAQAC3W4ZRMA&source=url)

## License

&copy; 2018-2020 Aliens Group.

Licensed under MIT ([read license](https://github.com/ElementaryFramework/FireFS/blob/master/LICENSE)).
