<?php
printf("Executing php_cs!!!\n\n");
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->filter(function (SplFileInfo $file) {
        $path = $file->getPathname();

        switch (true) {
            case (strrpos($path, '/bin/')):
                return false;
            case (strrpos($path, '/test/Bootstrap.php')):
                return false;
            case (strrpos($path, '/vendor/')):
                return false;
            default:
                return true;
        }
    });