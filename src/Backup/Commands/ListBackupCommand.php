<?php namespace Backup\Commands;

use Backup\Classes\Backup;
use Backup\Traits\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListBackupCommand extends Command
{
    use Helper;

    protected $consoleOutput;

    protected function configure()
    {
        $this->setName('db:list')
             ->setDescription('This command will list all the files in the backup directory.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->consoleOutput = $this->getIo($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new Backup($this->read(), $this->consoleOutput))->listAll();
    }
}
