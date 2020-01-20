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
composer require global viktor/differ:*
```
or use function from library in your project
```
\Differ\Analyzer\genDiff($path1, $path2, $format)
```

<B>Installation example</B>  
[![asciicast](https://asciinema.org/a/axOOK5tVmRCDbkeuadLApDmL5.svg)](https://asciinema.org/a/axOOK5tVmRCDbkeuadLApDmL5)

<B>Usage examples</B>  
<B>Flat JSON</B>  
[![asciicast](https://asciinema.org/a/KCvkMrRyo6LSooRFaTwE9r0g8.svg)](https://asciinema.org/a/KCvkMrRyo6LSooRFaTwE9r0g8)
<B>Flat YAML</B>  
[![asciicast](https://asciinema.org/a/lPOXX9wjnxBeQrjl3lMorja0K.svg)](https://asciinema.org/a/lPOXX9wjnxBeQrjl3lMorja0K)
<B>Recursive JSON</B>  
[![asciicast](https://asciinema.org/a/yJTLFCIbvTvmUJwqo2FhFq4SH.svg)](https://asciinema.org/a/yJTLFCIbvTvmUJwqo2FhFq4SH)
<B>Recursive YAML</B>
[![asciicast](https://asciinema.org/a/qioOK4Hf3GNCA2rEqIxRgZvKk.svg)](https://asciinema.org/a/qioOK4Hf3GNCA2rEqIxRgZvKk)
<B>Output in JSON and plain format</B>  
[![asciicast](https://asciinema.org/a/FYVvxUUkddNQmJjmJ9dBVP22p.svg)](https://asciinema.org/a/FYVvxUUkddNQmJjmJ9dBVP22p)




P.S. I have faced one trouble during writing project. I have ubuntu 18.04, and there is alreday gendiff file which can be called from any place (global). It is located in /usr/bin/gendiff. If you install my gendiff and will try to run it, you will/may run /usr/bin/gendiff.
So, if you want to globally run my gendiff, you need to change order of PATHs in $PATH. I was able to do this by modifying file ~/.bashrc. There is a line like "PATH=$PATH" or "PATH=$PATH:$HOME/.composer/vendor/bin" if you already added composer bin path. Modify it like this "PATH=$HOME/.composer/vendor/bin:$PATH", reboot, and problem will be solved.
It is possible that after some time /usr/bin/gendiff will be dicovered first. Reboot will help.
