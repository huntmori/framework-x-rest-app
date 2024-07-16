<?php
//
//use React\EventLoop\Loop;
//use React\Filesystem\Factory;
//use React\Filesystem\Node\DirectoryInterface;
//use React\Filesystem\Node\NodeInterface;
//
//require __DIR__.'/../vendor/autoload.php';
//
//$filesystem = Factory::create();
//
//$ls = static function (string $dir) use (&$ls, $filesystem): void {
//    $filesystem->detect($dir)->then(function (DirectoryInterface $directory) {
//        return $directory->ls();
//    })->then(static function (array $nodes) use (&$ls) {
//        foreach ($nodes as $node) {
//            assert($node instanceof NodeInterface);
//            echo $node->path() . $node->name(), ': ', get_class($node), PHP_EOL;
//            if ($node instanceof DirectoryInterface) {
//                $ls($node->path() . $node->name());
//            }
//        }
//    })->then(null, function (Throwable $throwable) {
//        echo $throwable;
//    });
//};
//
//$ls(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');
//
//Loop::run();
//
//echo '----------------------------', PHP_EOL, 'Done listing directory', PHP_EOL;

require __DIR__ . '/../vendor/autoload.php';

use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FilesystemException;

$adapter = new LocalFilesystemAdapter(__DIR__ . '/..');
$fileSystem = new Filesystem($adapter);
$contents = null;
try {
    $contents = $fileSystem->listContents('/src', true);
} catch (FilesystemException $e) {
    echo $e->getMessage();
}

$files = $contents->toArray();

$result = [];
array_walk($files, function ($file) use (&$result) {
   if($file instanceof FileAttributes && str_ends_with($file->path(), ".php")) {
       $result[] = $file;
   }
});

array_walk($result, function (FileAttributes $file) {
    echo $file->path().PHP_EOL;
});