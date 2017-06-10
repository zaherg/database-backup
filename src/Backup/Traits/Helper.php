<?php namespace Backup\Traits;

use Carbon\Carbon;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

trait Helper
{
    protected function save(array $data)
    {
        file_put_contents(getcwd().'/config.yml', Yaml::dump($data));
    }

    protected function read()
    {
        $file = file_get_contents(getcwd().'/config.yml');
        return json_decode(json_encode(Yaml::parse($file)));
    }

    protected function getIo(InputInterface $input, OutputInterface $output)
    {
        return new SymfonyStyle($input, $output);
    }

    protected function getDateTime()
    {
        return ' ['.Carbon::now()->toDateTimeString().'] ';
    }
}
