<?php

namespace LAIS\Scaffold\Console\Commands\Makes;


use LAIS\Scaffold\Console\Commands\Scaffolding;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Arr;
use Doctrine\Common\Inflector\Inflector;

class MakePlurals
{
    protected $scaffolding;
    protected $files;

    public function __construct(Scaffolding $scaffolding, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffolding = $scaffolding;
        $this->start();
    }

    protected function start()
    {
        if(!Config::has('plurals.irregular.' . strtolower($this->scaffolding->singular)))
        {
            //Execute
            file_put_contents(base_path('config/plurals.php'), $this->createPlurals(), FILE_TEXT);
            $this->scaffolding->info('Plural criado com sucesso');
        }
    }

    protected function createPlurals()
    {
        $plurals = Config::get('plurals.irregular');
        $plurals = Arr::add($plurals, strtolower($this->scaffolding->singular), strtolower($this->scaffolding->plural));

        if(snake_case($this->scaffolding->singular) != strtolower($this->scaffolding->singular))
        {
            $plurals = Arr::add($plurals, snake_case($this->scaffolding->singular), snake_case($this->scaffolding->plural));
        }

        Inflector::rules('plural', array('irregular' => $plurals));

        $stub = $this->files->get(dirname(__DIR__) . '/stubs/plurals.stub');
        $this->replacePlurals($stub, $plurals);

        return $stub;
    }

    protected function replacePlurals(&$stub, $plurals)
    {
        $rules = '';
        foreach($plurals as $key => $value)
        {
            $rules .= "\t\t'" . strtolower($key) . "' => '" . strtolower($value) . "',\n";
        }

        $stub = str_replace('{{rules}}', $rules, $stub);

        return $this;
    }
}