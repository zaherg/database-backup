<?php namespace Backup\Classes;

/**
 * Class BackupClass
 * -----------------
 * This class should receive the configuration and run the required commands to execute the backup.
 * After that it should move the file based on the adapter value.
 *
 * @package Backup\Classes
 */
class BackupClass
{
    protected $options = [];

    /**
     * BackupClass constructor.
     * @param mixed $options : mostly the configuration file content.
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function run()
    {
        return true;
    }
}
