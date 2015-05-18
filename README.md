# CakePHP Data Plugin
[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-data.svg?branch=2.x)](https://travis-ci.org/dereuromark/cakephp-data)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.4-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-data/license.png)](https://packagist.org/packages/dereuromark/cakephp-data)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-data/d/total.png)](https://packagist.org/packages/dereuromark/cakephp-data)

A CakePHP 2.x Plugin containing several useful data models that can be used in many projects.

Please note: New functionality has been tested against 2.x (current master and dev) only. Please upgrade if possible.

## How to include
Installing the Plugin is pretty much as with every other CakePHP Plugin.

* Put the files in `APP/Plugin/Data`
* Make sure you have `CakePlugin::load('Data')` or `CakePlugin::loadAll()` in your bootstrap

That's it. It should be up and running.

### Possible Dependencies

- Tools plugin
- CakeDC search plugin (optional, if you want basic filtering)

## Disclaimer
Use at your own risk. Please provide any fixes or enhancements via issue or better pull request.
Some classes are still from 1.2 (and are merely upgraded to 2.x) and might still need some serious refactoring.
If you are able to help on that one, that would be awesome.

### License
Licensed under [The MIT License](http://www.opensource.org/licenses/mit-license.php)
unless specified otherwise (in the classes).

### TODOs

* Better test coverage
