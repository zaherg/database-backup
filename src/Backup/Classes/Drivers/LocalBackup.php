<?php namespace Backup\Classes\Drivers;

use Backup\Classes\Process;
use Symfony\Component\Console\Style\SymfonyStyle;

class LocalBackup
{
    protected $consoleOutput;
    private $options;

    public function __construct($options, SymfonyStyle $consoleOutput)
    {
        $this->options = $options;
        $this->consoleOutput = $consoleOutput;
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
                "tar -cvf {$databaseName}.sql.tar {$databaseName}.sql",
                "rm -f  {$databaseName}.sql"
            ];
            (new Process())->execute(implode(' && ', $commands), $this->consoleOutput);
        }
    }
}
