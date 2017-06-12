<?php namespace Backup\Traits;

use Carbon\Carbon;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait Backup
{
    protected function getDirectoryContent($directory)
    {
        return (new Finder)->in($directory)->files()->getIterator();
    }

    protected function getDate()
    {
        return Carbon::now()->format('Y-m-d');
    }

    protected function getCwd()
    {
        return getcwd();
    }

    private function createBackupFolder()
    {
        $fileSystem = new FileSystem();
        $directoryPath =  "{$this->getCwd()}/backup/{$this->getDate()}";

        if (!$fileSystem->exists($directoryPath)) {
            $fileSystem->mkdir($directoryPath, 0755);
        }

        $this->options->path = $directoryPath;
    }

    private function remove($item)
    {
        (new Filesystem)->remove($item);
    }

    private function removeBackupDirectory()
    {
        (new Filesystem)->remove("{$this->getCwd()}/backup");
    }
}
