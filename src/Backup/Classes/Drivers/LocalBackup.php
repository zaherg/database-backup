<?php namespace Backup\Classes\Drivers;

use Backup\Traits\Backup;
use Backup\Classes\Process;
use Symfony\Component\Console\Style\SymfonyStyle;

class LocalBackup
{
    use Backup;

    protected $consoleOutput;
    private $options;

    public function __construct($options, SymfonyStyle $consoleOutput)
    {
        $this->options = $options;
        $this->consoleOutput = $consoleOutput;
        $this->createBackupFolder();
    }

    public function backup($databaseName)
    {

        $outputName = sprintf('%1$s-%2$s', $this->getDate('H:i:s'), $databaseName);

        $commandTemplate = 'mysqldump --column-statistics=0 -h%1$s -u%2$s -p%3$s -P%4$d %5$s > %6$s/%7$s.sql';

        $command = sprintf(
            $commandTemplate,
            $this->options->database_server->host,
            $this->options->database_server->username,
            $this->options->database_server->password,
            $this->options->database_server->port,
            $databaseName,
            $this->options->path,
            $outputName
        );

        if(!$this->options->database_server->password) {
            $command = str_replace('-p', '', $command);
        }

        (new Process())->execute($command, $this->consoleOutput);

        if ($this->options->compress) {
            $commands =[
                sprintf('cd %s', $this->options->path),
                sprintf('tar -zcf %1$s.sql.gz %1$s.sql', $outputName),
                sprintf('rm -f %s.sql', $outputName)
            ];
            (new Process())->execute(implode(' && ', $commands), $this->consoleOutput);
        }
    }


    public function listAll()
    {
        $content = $this->getDirectoryContent($this->getCwd().'/backup');
        $items = [];
        foreach ($content as $item) {
            $items[] = $item->getRealPath();
        }

        $this->consoleOutput->section('You have the following files listed in your local backup');
        $this->consoleOutput->listing($items);
    }
}
