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

    protected function getDate($format = 'Y-m-d')
    {
        return Carbon::now()->format($format);
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

    private function retriveItems($directory)
    {
        $content = $this->filesystem->listContents($directory, true);
        $items = [];

        foreach ($content as $item) {
            $items[] = $item['path'];
        }

        return collect($items)
            ->reject(function ($item) {
                return !strpos($item, '.gz');
            })->toArray();
    }
}
