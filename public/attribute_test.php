<?php
require __DIR__. "/../vendor/autoload.php";

use Src\common\Attributes\Controller;
use Src\common\Attributes\Service;
use Src\common\Attributes\Repository;

// 특정 디렉토리 경로
$directory = __DIR__ . "/../src";

// 디렉토리 하위의 모든 PHP 파일 검색 함수
function findPhpFilesRecursive($dir) {
    $files = [];
    $iterator = new RecursiveDirectoryIterator($dir);
    $recursiveIterator = new RecursiveIteratorIterator($iterator);

    foreach ($recursiveIterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

// 디렉토리 하위의 모든 PHP 파일 경로 배열 얻기
$phpFiles = findPhpFilesRecursive($directory);
var_dump($phpFiles);

// 각 PHP 파일에서 클래스 및 attribute 검색
foreach ($phpFiles as $file) {
    // 파일을 include 또는 require하여 클래스를 로드
    require_once $file;
}

$classes = get_declared_classes();

$controllers = [];
$services = [];
$repositories = [];

foreach($classes as $className) {

    //$reflectionClass = new ReflectionClass($className);
//    var_dump($className);
    // 클래스가 정의되어 있는지 확인
    if (class_exists($className)) {
        // 클래스 리플렉션 생성
//        var_dump($className);
        $reflectionClass = new ReflectionClass($className);

        // 클래스의 attribute 목록 가져오기
        $attributes = $reflectionClass->getAttributes();
        var_dump($className);

        $function = function(ReflectionAttribute $attribute, string $className, string $targetAttribute)
        {
            $attributeName = $attribute->getName();
            $exclude = [
                Controller::class,
                Service::class,
                Repository::class
            ];

            return (!in_array($className,$exclude))
                && (str_contains($className, "Src"))
                && (str_contains($targetAttribute, $attributeName));
        };

       foreach ($attributes as $attribute)
       {
           $attributeName = $attribute->getName();

            $search = false;
           if($function($attribute, $className, Controller::class)) {
               $controllers[] = $className;
               $search = true;
           }

           if($function($attribute, $className, Service::class)) {
               $services[] = $className;
               $search = true;
           }

           if($function($attribute, $className, Repository::class)) {
               $repositories[] = $className;
               $search = true;
           }

           if($search) {
               echo $className."==>searched_attribute_name________";
               var_dump($attribute->getName());

               echo $className."==>searched_attribute_arguments________";
               var_dump($attribute->getArguments());

               echo $className."==>searched_attribute_target________";
               var_dump($attribute->getTarget());
           }

       }

    }
}
echo "====Controllers";
var_dump($controllers);

echo "====Services";
var_dump($services);

echo "====Repositories";
var_dump($repositories);