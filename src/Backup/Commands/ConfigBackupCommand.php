<?php namespace Backup\Commands;

use Backup\Traits\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ConfigBackupCommand extends Command
{
    use Helper;

    protected $consoleOutput;

    protected function configure()
    {
        $this->setName('config:backup')
            ->setDescription('This command will make backup from your current configuration file');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->consoleOutput = $this->getIo($input, $output);
        if (!$this->isConfigured()) {
            $this->consoleOutput->error('This command will work only if you have already configured the backup.');
            exit;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = new Filesystem();
        $fileSystem->copy(getcwd().'/config.yml', getcwd() .'/config.copy.yml');
        $this->consoleOutput->success('We have just backup your configuration file successfully.');
    }
}
