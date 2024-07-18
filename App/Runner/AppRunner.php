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
use React\MySQL\Io\LazyConnection;
use ReflectionClass;
use Src\common\Attributes\Controller;
use Src\common\Attributes\Repository;
use Src\common\Attributes\Service;
use Src\common\TestController;
use Src\common\TestRepository;
use Src\User\Controller\UserController;
use Src\User\Repository\UserRepository;
use Src\User\Repository\UserRepositoryImpl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use React\MySQL\Factory;
use React\Http\Message\Response as Response;
use Symfony\Component\Dotenv\Dotenv;

use function React\Async\await;

class AppRunner extends Command
{
    protected static string $baseDefaultName = 'start';


    private  string $rootDir;

    protected string $port;
    protected string $profile;

    private array $env;

    private App $app;
    private Container $container;

    private array $classes = [];
    private array $controllers = [];
    private array $services = [];
    private array $repositories = [];

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

        $classCheck = function(\ReflectionAttribute $attribute, string $className, string $targetAttribute)
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

        foreach($classes as $class)
        {
            if (str_contains($class, "Src") && class_exists($class))
            {
                //echo $class.PHP_EOL;
                $reflectionClass = new ReflectionClass($class);
                $attributes = $reflectionClass->getAttributes();

                foreach($attributes as $attribute)
                {
                    if ($classCheck($attribute, $class, Controller::class))
                    {
                        $this->controllers[] = $class;
                    }
                    else if ($classCheck($attribute, $class, Service::class))
                    {
                        $this->services[] = $class;
                    }
                    else if ($classCheck($attribute, $class, Repository::class))
                    {
                        $this->repositories[] = $class;
                    }
                }
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

        $this->setRepositories($container);
        $this->setServices($container);
        $this->setControllers($container);

        $this->app = new App(new \FrameworkX\Container($container));
        $this->settingRouter();

        $this->app->run();

        return Command::SUCCESS;
    }

    private function settingRouter(): void
    {
        $app = $this->app;

        $app->get("/test/{name}", [TestController::class, 'home']);
        $app->get('/', function ()  {
            $db = $this->container->get(LazyConnection::class);
            $result = await($db->query("SELECT NOW() as now "));
            $data = $result->resultRows[0];
            var_dump($data);
            echo "select test : {$data['now']}";
            echo PHP_EOL;
            return Response::plaintext(
                "Hello world!\n"
            );
        });

        $app->get('/users/{name}', function (ServerRequestInterface $request) {
            return Response::plaintext(
                "Hello " . $request->getAttribute('name') . "!\n"
            );
        });
    }

    private function setRepositories(Container $container): void
    {
        foreach ($this->repositories as $className)
        {
            $reflectionClass = new ReflectionClass($className);
            $repositoryAttribute = $reflectionClass->getAttributes(Repository::class)[0];
            $arguments = $repositoryAttribute->getArguments();

            $interfaceName = $arguments[0];
            $implementName = $arguments[1];

            $container->add($interfaceName, $implementName);
        }
    }

    private function setServices(Container $container): void
    {
        foreach ($this->services as $className)
        {
            $reflectionClass = new ReflectionClass($className);
            $repositoryAttribute = $reflectionClass->getAttributes(Service::class)[0];
            $arguments = $repositoryAttribute->getArguments();

            $interfaceName = $arguments[0];
            $implementName = $arguments[1];

            $container->add($interfaceName, $implementName);
        }
    }

    private function setControllers(Container $container): void
    {
        foreach ($this->controllers as $className)
        {
            $reflectionClass = new ReflectionClass($className);
            $repositoryAttribute = $reflectionClass->getAttributes(Controller::class)[0];
            $arguments = $repositoryAttribute->getArguments();

            $interfaceName = $arguments[0];
            $implementName = $arguments[1];

            $container->add($interfaceName, $implementName);
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

}