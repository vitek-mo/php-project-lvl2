# Differ (php-project-lvl2)

<a href="https://codeclimate.com/github/vitek-mo/php-project-lvl2/maintainability"><img src="https://api.codeclimate.com/v1/badges/19837d8b3c4664864a5c/maintainability" /></a>
<a href="https://codeclimate.com/github/vitek-mo/php-project-lvl2/test_coverage"><img src="https://api.codeclimate.com/v1/badges/19837d8b3c4664864a5c/test_coverage" /></a>
<a href="https://travis-ci.org/vitek-mo/php-project-lvl2"><img src="https://travis-ci.org/vitek-mo/php-project-lvl2.svg?branch=master" /></a>

Compare difference between configuration files. Can be installed globally with composer using
```
composer require global viktor/differ:dev-master
```
or use function from library in your project.

Global installation example
<a href="https://asciinema.org/a/TTmPm3nLSWRdWFPbf4EvVbSQx" target="_blank"><img src="https://asciinema.org/a/TTmPm3nLSWRdWFPbf4EvVbSQx.svg" /></a>

usage example:<br>
file1:
```
{
  "host": "hexlet.io",
  "timeout": 50,
  "proxy": "123.234.53.22"
}
```

file2:
```
{
  "timeout": 20,
  "verbose": true,
  "host": "hexlet.io"
}
```
```
gendiff file1 file2
{
    host: hexlet.io
  - proxy: 123.234.53.22
  + timeout: 20
  - timeout: 50
  + verbose: true
}
```

P.S. I have faced one trouble during writing project. I have ubuntu 18.04, and there is alreday gendiff file which can be called from any place (global). It is located in /usr/bin/gendiff. If you install my gendiff and will try to run it, you will/may run /usr/bin/gendiff.
So, if you want to globally run my gendiff, you need to change order of PATHs in $PATH. I was able to do this by modifying file ~/.bashrc. There is a line like "PATH=$PATH" or "PATH=$PATH:$HOME/.composer/vendor/bin" if you already added composer bin path. Modify it like this "PATH=$HOME/.composer/vendor/bin:$PATH", reboot, and problem will be solved.


[![asciicast](https://asciinema.org/a/roYuqTd22BcW728Eqx8jdLdT0.svg)](https://asciinema.org/a/roYuqTd22BcW728Eqx8jdLdT0)
