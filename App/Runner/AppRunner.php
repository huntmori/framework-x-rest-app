<?php

namespace App\Runner;

use FrameworkX\App;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\Io\LazyConnection;
use ReflectionClass;
use ReflectionException;
use Src\Common\Attributes\Controller;
use Src\Common\Attributes\Injection;
use Src\Common\Attributes\Repository;
use Src\Common\Attributes\Route;
use Src\Common\Attributes\Service;
use Src\Common\TestController;
use Src\User\Controller\UserController;
use Src\User\Controller\UserControllerImpl;
use Src\User\Repository\UserRepositoryImpl;
use Src\User\Service\UserService;
use Src\User\Service\UserServiceImpl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use React\MySQL\Factory;
use React\Http\Message\Response as Response;
use Symfony\Component\Dotenv\Dotenv;

use function React\Async\await;

/**
 * @method callTest(int[] $array, \stdClass $param)
 */
class AppRunner extends Command
{
    protected static string $baseDefaultName = 'start';


    private  string $rootDir;

    protected string $port;
    protected string $profile;

    private array $env;

    private App $app;
    private Container $container;

    private array $controllers = [];

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
        parent::__construct(self::$baseDefaultName);
    }

    protected function configure(): void
    {
        $this->setDescription("server run")
            ->addOption(
                "port",
                "p",
                InputOption::VALUE_OPTIONAL,
                "listen port",
                9090)
            ->addOption(
                "profile",
                null,
                InputOption::VALUE_OPTIONAL,
                "server profile",

                "local"
            )
            ->setName(self::$baseDefaultName);
    }

    protected function appClassesScan(string $dir = null): void
    {
        if(is_null($dir)) {
            $dir = $this->rootDir;
        }

        $adapter = new LocalFilesystemAdapter($dir);
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
                require $this->rootDir. DIRECTORY_SEPARATOR . $file->path();
            }
        });

        $classes = get_declared_classes();
        foreach($classes as $class)
        {
            $reflectClass = new ReflectionClass($class);
            $injectionAttributes = $reflectClass->getAttributes(Injection::class);

            if(count($injectionAttributes) > 0) {
                echo "$class has ".count($injectionAttributes)." injection attribute.".PHP_EOL;
                $attributeArguments = $injectionAttributes[0]->getArguments();
                $key = $attributeArguments[0];
                $concrete = $attributeArguments[1];
                $arguments = $attributeArguments[2];

                $this->container->add($key, $concrete)
                    ->addArguments($arguments);
            }

        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = $this->port = $input->getOption("port");
        $profile = $this->profile = $input->getOption("profile");
        $rootDir = $this->rootDir;

        $output->writeln("port is {$port} and profile is {$profile}");
        $output->writeln(__DIR__);
        $output->writeln($this->rootDir);

        $dotenv = new Dotenv();
        $envPath = $this->rootDir."/envs/".$this->profile."/.env";

        $dotenv->load($envPath);

        $this->env = $_ENV;

        $this->setPortInEnvironment($this->port);

        $connection = $this->getDbConnectionViaEnv();

        $this->container = $container = $this->createLeagueContainer();
        $container->add(LazyConnection::class, $connection);
        $this->appClassesScan();
        //$this->bootStrap($container);

        $this->app = new App(new \FrameworkX\Container($container));
        $this->settingRouter($this->app);
        $this->test();

        $this->app->run();

        return Command::SUCCESS;
    }

    /**
     * @throws ReflectionException
     */
    private function settingRouter(App $app = null): void
    {
        $app = $app ??  $this->app;

        $this->controllers[] = [
            UserController::class,
            UserControllerImpl::class
        ] ;
//
//        $app->get("/test/{name}", [TestController::class, 'home']);
//        $app->get('/', function ()  {
//            $db = $this->container->get(LazyConnection::class);
//            $result = await($db->query("SELECT NOW() as now "));
//            $data = $result->resultRows[0];
//            var_dump($data);
//            echo "select test : {$data['now']}";
//            echo PHP_EOL;
//            return Response::plaintext(
//                "Hello world!\n"
//            );
//        });
//
//        $app->get('/users/{name}', function (ServerRequestInterface $request) {
//            return Response::plaintext(
//                "Hello " . $request->getAttribute('name') . "!\n"
//            );
//        });

        foreach ($this->controllers as $className)
        {
            echo "className : ".$className[0].PHP_EOL;
            $reflectionClass = new ReflectionClass($className[0]);
            $reflectionMethods = $reflectionClass->getMethods();

            echo '$classAttributes'.PHP_EOL;
            $classAttributes = $reflectionClass->getAttributes(Controller::class);

            if(count($classAttributes) > 0)
            {
                $classAttributeInstance = $classAttributes[0];
                $attributeArguments = $classAttributeInstance->getArguments();
                $interfaceName = $attributeArguments[0];
                $implementName  = $attributeArguments[1];

                foreach($reflectionMethods as $method)
                {
                    $routeAttributes = $method->getAttributes(Route::class);
                    foreach($routeAttributes as $attribute)
                    {
                        $instance = $attribute->newInstance();
                        $arguments = $attribute->getArguments();
                        $methods = [ $arguments[0] ];
                        $route = $arguments[1];

                        $methodStr = strtolower($arguments[0]);
                        $app->{$methodStr}($route, $interfaceName, $method->getName());
                    }
                }
            }
        }
    }


    private function setRepositories(Container $container): void
    {
        //var_dump($this->repositories);
//        foreach ($this->repositories as $className)
//        {
//            $reflectionClass = new ReflectionClass($className);
//            $attributes = $reflectionClass->getAttributes(Repository::class);
//
//            if(count($attributes) > 0)
//            {
//                $repositoryAttribute = $attributes[0];
//                $arguments = $repositoryAttribute->getArguments();
//
//                $interfaceName = $arguments[0];
//                $implementName = $arguments[1];
//
//                $container->add($interfaceName, $implementName);
//            }
//        }
    }

    /**
     * @throws ReflectionException
     */
    private function setServices(Container $container): void
    {
        foreach ($this->services as $className)
        {
            $reflectionClass = new ReflectionClass($className);
            $attributes = $reflectionClass->getAttributes(Service::class);

            if (count($attributes) > 0)
            {
                $arguments = $$attributes[0]->getArguments();

                $interfaceName = $arguments[0];
                $implementName = $arguments[1];

                $container->add($interfaceName, $implementName);
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    private function setControllers(Container $container): void
    {
        foreach ($this->controllers as $className)
        {
            $reflectionClass = new ReflectionClass($className);
            echo "Attribute";
            $attributes = ($reflectionClass->getAttributes(Controller::class));

            if (count($attributes) > 0)
            {
                $controllerAttribute = $reflectionClass->getAttributes(Controller::class)[0];
                $arguments = $controllerAttribute->getArguments();

                $interfaceName = $arguments[0];
                $implementName = $arguments[1];

                $container->add($interfaceName, $implementName);
            }
        }
    }

    private function buildCredentialString(
        string $user,
        string $pass,
        string $host,
        string $dbname
    ): string
    {
        return "{$user}:{$pass}@{$host}/{$dbname}";
    }

    private function setPortInEnvironment(int $port): void
    {
        putenv("X_LISTEN=127.0.0.1:{$port}");
    }

    private function getDbConnection(
        string $user,
        string $pass,
        string $host,
        string $dbname
    ): LazyConnection
    {
        $credentialString = $this->buildCredentialString($user, $pass, $host, $dbname);

        return (new Factory())
            ->createLazyConnection($credentialString);
    }

    protected function getDbConnectionViaEnv(): LazyConnection
    {
        return $this->getDbConnection(
            getenv("DB_USER"),
            getenv("DB_PASS"),
            getenv("DB_HOST"),
            getenv("DB_NAME"),
        );
    }

    private function createLeagueContainer(): Container
    {
        $container = new Container();
        $container->delegate(
            new ReflectionContainer()
        );
        return $container;
    }

    public function bootStrap(Container $container): void
    {
        $container->add(UserRepository::class, UserRepositoryImpl::class)
            ->addArgument($container->get(LazyConnection::class));
        $container->add(UserService::class, UserServiceImpl::class)
            ->addArgument($container->get(UserRepository::class));
        $container->add(UserController::class, UserControllerImpl::class)
            ->addArgument($container->get(UserService::class));

        $this->controllers[] = [
            UserController::class,
            UserControllerImpl::class
        ] ;
        //$this->test();
    }

    public function test(): void
    {
        $this->app->get("/test/{name}", [TestController::class, 'home']);

        foreach ($this->controllers as $item)
        {
            $interfaceName = $item[0];
            $implementName = $item[1];
            echo $interfaceName. " => ". $implementName.PHP_EOL;

            $reflectionClass = new ReflectionClass($implementName);
            $methods = $reflectionClass->getMethods();

            foreach($methods as $method)
            {
                $attributes = $method->getAttributes(Route::class);

                foreach($attributes as $attribute)
                {
                    $attributeArguments = $attribute->getArguments();
                    $routeMethod = $attributeArguments[0];
                    $route = $attributeArguments[1];

                    $mappingMethod = [ $routeMethod ];
                    $controller = $this->container->get($interfaceName);

                    $this->app->map(
                        $mappingMethod,
                        $route,
                        [
                            $controller,
                            $method->getName()
                        ]
                    );
                    echo "[".date("Y-m-d H:i:s:v")."] [$routeMethod]"
                        .$route." is routed.".PHP_EOL;
                }
            }
        }
    }
}