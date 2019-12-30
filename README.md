# Differ (php-project-lvl2)

Compare difference between configuration files. Can be installed globally with composer using `composer require global viktor/differ:dev-master`, or use function from library in your project.

P.S. I have faced one trouble during writing project. I have ubuntu 18.04, and there is alreday gendiff file which can be called from any place (global). It is located in /usr/bin/gendiff. If you install my gendiff and will try to run it, you will/may run /usr/bin/gendiff.
So, if you want to globally run my gendiff, you need to change order of PATHs in $PATH. I was able to do this by modifying file ~/.bashrc. There is a line like "PATH=$PATH" or "PATH=$PATH:$HOME/.composer/vendor/bin" if you already added composer bin path. Modify it like this "PATH=$HOME/.composer/vendor/bin:$PATH", reboot, and problem will be solved.
