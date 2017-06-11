<?php namespace Backup\Classes;

use Carbon\Carbon;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BackupClass
 * -----------------
 * This class should receive the configuration and run the required commands to execute the backup.
 * After that it should move the file based on the adapter value.
 *
 * @package Backup\Classes
 */
class Backup
{
    protected $options = [];
    protected static $exclude = ['information_schema','performance_schema','Database'];

    /**
     * BackupClass constructor.
     * @param mixed $options : mostly the configuration file content.
     * @param SymfonyStyle $consoleOutput
     */
    public function __construct($options, SymfonyStyle $consoleOutput)
    {
        $this->options = $options;
        $this->consoleOutput = $consoleOutput;
        $this->createBackupFolder();
        $this->className = '\\Backup\\Classes\\Drivers\\'.ucwords($this->options->adapter->default).'Backup';
    }

    public function run($databaseName)
    {
        if ($databaseName === 'all') {
            $names = $this->getDatabases();
            $names->each(function ($item) {
                $class = new $this->className($this->options, $this->consoleOutput);
                $class->backup($item);
            });
        } else {
            (new $this->className($this->options, $this->consoleOutput))
                ->backup($databaseName);
        }

        return true;
    }

    protected function getDatabases()
    {
        $databases = "mysql -h{$this->options->database_server->host} -u{$this->options->database_server->username}".
            " -p{$this->options->database_server->password} -P{$this->options->database_server->port} ".
            '-e "SHOW DATABASES;" | awk \'{print $1;}\' ';

        return collect((new Process())->getOutput($databases))
            ->reject(function ($item) {
                return in_array($item, self::$exclude, true) || $item === '';
            });
    }

    private function createBackupFolder()
    {
        $fileSystem = new FileSystem();
        $date = Carbon::today()->format('Y-m-d');

        if (!$fileSystem->exists(getcwd().'/backup/'.$date)) {
            $fileSystem->mkdir(getcwd().'/backup/'.$date, 0755);
        }

        $this->options->path ="{$this->options->path}/{$date}";
    }
}
