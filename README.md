# Differ (php-project-lvl2)

<a href="https://codeclimate.com/github/vitek-mo/php-project-lvl2/maintainability"><img src="https://api.codeclimate.com/v1/badges/19837d8b3c4664864a5c/maintainability" /></a>
<a href="https://codeclimate.com/github/vitek-mo/php-project-lvl2/test_coverage"><img src="https://api.codeclimate.com/v1/badges/19837d8b3c4664864a5c/test_coverage" /></a>
<a href="https://travis-ci.org/vitek-mo/php-project-lvl2"><img src="https://travis-ci.org/vitek-mo/php-project-lvl2.svg?branch=master" /></a>

<B>Description</B>  
Compare difference between configuration files.
Supported files types:
 - JSON
 - YAML
File should have appropriate extension to correctly interpret file type it - "json" or "yml".

<B>Installation</B>
Can be installed globally with composer using
```
composer require global viktor/differ:dev-master
```
or use function from library in your project
```
\Differ\Analyzer\genDiff($path1, $path2, $format)
```

P.S. I have faced one trouble during writing project. I have ubuntu 18.04, and there is alreday gendiff file which can be called from any place (global). It is located in /usr/bin/gendiff. If you install my gendiff and will try to run it, you will/may run /usr/bin/gendiff.
So, if you want to globally run my gendiff, you need to change order of PATHs in $PATH. I was able to do this by modifying file ~/.bashrc. There is a line like "PATH=$PATH" or "PATH=$PATH:$HOME/.composer/vendor/bin" if you already added composer bin path. Modify it like this "PATH=$HOME/.composer/vendor/bin:$PATH", reboot, and problem will be solved.
It is possible that after some time /usr/bin/gendiff will be dicovered first. Reboot will help.
