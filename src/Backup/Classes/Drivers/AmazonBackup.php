<?php namespace Backup\Classes\Drivers;

use Backup\Traits\Backup;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Style\SymfonyStyle;

class AmazonBackup
{
    use Backup;

    private $consoleOutput;
    private $filesystem;
    private $options;

    public function __construct($options, SymfonyStyle $consoleOutput)
    {
        $this->options = $options;
        $this->consoleOutput = $consoleOutput;

        $this->setupFlySystem();
    }

    public function backup($databaseName)
    {
        (new LocalBackup($this->options, $this->consoleOutput))->backup($databaseName);

        $fileName = $databaseName.'.sql.gz';
        $this->consoleOutput->text('<info>[INFO]</info> Please wait while uploading '.
            "<comment>{$fileName}</comment> to Amazon S3.");

        $stream = fopen("{$this->options->path}/{$fileName}", 'r+');
        $this->filesystem->putStream("{$this->getDate()}/{$fileName}", $stream);

        if (is_resource($stream)) {
            fclose($stream) ;
        }

        $this->removeBackupDirectory();

        $this->consoleOutput->text('<info>[INFO]</info> File '.
            "<comment>{$fileName}</comment> has been uploaded to dropbox successfully.");
    }

    public function listAll()
    {
        $allItems = $this->retriveItems('/');

        $this->consoleOutput->section('You have the following files listed in your S3 backup');
        $this->consoleOutput->listing($allItems);
    }

    private function setupFlySystem()
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => $this->options->amazon->key,
                'secret' => $this->options->amazon->secret
            ],
            'region' => $this->options->amazon->region,
            'version' => 'latest',
        ]);

        $this->filesystem = new Filesystem(new AwsS3Adapter($client, $this->options->amazon->bucket));
    }
}
