<?php namespace Backup\Classes\Drivers;

use Backup\Traits\Backup;
use Spatie\Dropbox\Client;
use League\Flysystem\Filesystem;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Symfony\Component\Console\Style\SymfonyStyle;

class DropboxBackup
{
    use Backup;

    protected $consoleOutput;
    private $options;

    public function __construct($options, SymfonyStyle $consoleOutput)
    {
        $this->options = $options;
        $this->consoleOutput = $consoleOutput;

        $this->filesystem = new Filesystem(new DropboxAdapter(new Client($this->options->dropbox->authKey)));
    }

    public function backup($databaseName)
    {
        (new LocalBackup($this->options, $this->consoleOutput))->backup($databaseName);

        $fileName = $databaseName.'.sql.gz';
        $this->consoleOutput->text('<info>[INFO]</info> Please wait while uploading '.
            "<comment>{$fileName}</comment> to dropbox.");

        $stream = fopen("{$this->options->path}/{$fileName}", 'r+');
        $this->filesystem->putStream("backup/{$this->getDate()}/{$fileName}", $stream);

        if (is_resource($stream)) {
            fclose($stream) ;
        }

        $this->removeBackupDirectory();

        $this->consoleOutput->text('<info>[INFO]</info> File '.
            "<comment>{$fileName}</comment> has been uploaded to dropbox successfully.");
    }

    public function listAll()
    {
        $allItems = $this->retriveItems('/backup');

        $this->consoleOutput->section('You have the following files listed in your Dropbox backup');
        $this->consoleOutput->listing($allItems);
    }
}
