<?php

namespace App\Runner;

use FrameworkX\App;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\Io\LazyConnection;
use Src\common\TestController;
use Src\common\TestRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dotenv\Dotenv;
use React\MySQL\Factory;
use React\Http\Message\Response as Response;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = $this->port = $input->getOption("port");
        $profile = $this->profile = $input->getOption("profile");
        $rootDir = $this->rootDir;

        $output->writeln("port is {$port} and profile is {$profile}");
        $output->writeln(__DIR__);
        $output->writeln($this->rootDir);

        $dotEnv = Dotenv::createImmutable($rootDir . "/envs/{$profile}", ".env");
        $dotEnv->load();

        $this->env = $_ENV;

        $this->setPortInEnvironment($this->port);

        $connection = $this->getDbConnectionViaEnv();

        $this->container = $container = $this->createLeagueContainer();

        $container->add(LazyConnection::class, $connection);
        $this->setRepositories($container);
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
        $container->add(TestRepository::class)
            ->addArgument(LazyConnection::class);
    }

    private function setControllers(Container $container): void
    {
        $container->add(TestController::class)
            ->addArgument(TestRepository::class);
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
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME']
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