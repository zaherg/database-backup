<?php namespace Backup\Classes;

use Backup\Traits\Backup as BackupHelper;
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
    use BackupHelper;

    protected $options = [];
    protected static $exclude = ['information_schema','performance_schema','Database', 'mysql'];

    /**
     * BackupClass constructor.
     * @param mixed $options : mostly the configuration file content.
     * @param SymfonyStyle $consoleOutput
     */
    public function __construct($options, SymfonyStyle $consoleOutput)
    {
        $this->options = $options;
        $this->consoleOutput = $consoleOutput;
        $this->className = '\\Backup\\Classes\\Drivers\\'.ucwords($this->options->adapter->default).'Backup';
    }

    public function run($databaseName)
    {
        if ($databaseName === 'all') {

            $names = $this->getDatabases();

            $names->each(function ($item) {
                (new $this->className($this->options, $this->consoleOutput))
                    ->backup($item);
            });

        } else {
            (new $this->className($this->options, $this->consoleOutput))
                ->backup($databaseName);
        }

        return true;
    }

    public function listAll()
    {
        return (new $this->className($this->options, $this->consoleOutput))->listAll();
    }

    protected function getDatabases()
    {
        $commandTemplate = 'mysql -h%s -u%s -p%s -P%d -e "SHOW DATABASES;" | awk \'{print $1;}\' ';

        $databases = sprintf(
            $commandTemplate,
            $this->options->database_server->host,
            $this->options->database_server->username,
            $this->options->database_server->password,
            $this->options->database_server->port
        );

        return collect((new Process())->getOutput($databases))
            ->reject(function ($item) {
                return in_array($item, self::$exclude, true) || $item === '';
            });
    }
}
