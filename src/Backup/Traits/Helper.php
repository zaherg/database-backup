<?php namespace Backup\Traits;

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
}
