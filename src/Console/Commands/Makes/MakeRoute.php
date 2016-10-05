<?php

namespace LAIS\Scaffold\Console\Commands\Makes;


use LAIS\Scaffold\Console\Commands\Scaffolding;
use Illuminate\Filesystem\Filesystem;

class MakeRoute
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

        //Execute
        file_put_contents(base_path('routes/web.php'), $this->createRoute(), FILE_APPEND);
        $this->scaffolding->info('Route created successfully');
    }

    protected function createRoute()
    {
        $stub = $this->files->get(dirname(__DIR__) . '/stubs/route.stub');
        $this->replaceRouteName($stub);

        return $stub;
    }

    protected function replaceRouteName(&$stub)
    {
        $routeName = str_plural(mb_strtolower($this->scaffolding->getModelName()));
        $controllerName = $this->scaffolding->getModelName();
        $stub = str_replace('{{routeName}}', $routeName, $stub);
        $stub = str_replace('{{controllerName}}', $controllerName, $stub);

        return $this;
    }
}