<?php

namespace App\Runner;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dotenv\Dotenv;

class AppRunner extends Command
{
    protected static string $baseDefaultName = 'start';


    private  string $rootDir;

    protected string $port;
    protected string $profile;

    private array $env;

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
        
        var_dump($this->env);

        return Command::SUCCESS;
    }
}