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
        $command = "mysqldump -h{$this->options->database_server->host} -u{$this->options->database_server->username}".
            " -p{$this->options->database_server->password} -P{$this->options->database_server->port} ".
            "{$databaseName} > {$this->options->path}/{$databaseName}.sql";

        (new Process())->execute($command, $this->consoleOutput);

        if ($this->options->compress) {
            $commands =[
                "cd {$this->options->path}",
                "tar -zcf {$databaseName}.sql.gz {$databaseName}.sql",
                "rm -f  {$databaseName}.sql"
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
