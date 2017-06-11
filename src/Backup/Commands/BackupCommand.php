<?php namespace Backup\Commands;

use Backup\Classes\BackupClass;
use Backup\Traits\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Stopwatch\Stopwatch;

class BackupCommand extends Command
{
    use Helper;

    protected $tools = 'mysqldump';
    protected $stopWatch;
    protected $consoleOutput;

    protected function configure()
    {
        $this->setName('run')
            ->setDescription('You can use this command to backup your database')
            ->addOption('database', 'd', InputOption::VALUE_OPTIONAL,
                'Set the name for the database to backup', 'all');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->stopWatch = new Stopwatch();
        $this->consoleOutput = $this->getIo($input, $output);

        if (!$this->validateTools($this->tools)) {
            $this->consoleOutput->error($this->getDateTime().
                "Please make sure that you have installed '{$this->tools}' locally.");
            exit;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->stopWatch->start('backup');

        $this->consoleOutput->section('<info>[INFO]</info> The process will start now.');

        (new BackupClass($this->read()))->run();

        $endBackup = $this->stopWatch->stop('backup');

        $this->consoleOutput->text('<info>[INFO]</info> The process executed in <comment>' .
            $endBackup->getDuration()/60 .' min</comment>.');

        $this->consoleOutput->success($this->getDateTime().' ＼（＾ ＾）／ everything was successfully executed.');
    }

    private function validateTools($tools)
    {
        return null !== (new ExecutableFinder())->find($tools);
    }
}
