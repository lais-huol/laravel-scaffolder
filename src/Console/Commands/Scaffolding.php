<?php

namespace LAIS\Scaffold\Console\Commands;

use LAIS\Scaffold\Console\Commands\Makes\MakeMigration;
use LAIS\Scaffold\Console\Commands\Makes\MakeModel;
use LAIS\Scaffold\Console\Commands\Makes\MakeController;
use LAIS\Scaffold\Console\Commands\Makes\MakeRoute;
use LAIS\Scaffold\Console\Commands\Makes\MakeView;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Scaffolding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {model} {--schema=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold your MVC';

    /**
     * The name of the model.
     *
     * @var string
     */
    protected $modelName;

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = ['create', 'edit', 'index', 'show'];

    /**
     * The schema if exists.
     *
     * @var string
     */
    protected $schema;

    /**
     * The files.
     *
     * @var string
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->modelName = $this->argument('model');
        $this->schema    = $this->option('schema');

        // Start Scaffold
        $this->info('Configuring ' . $this->modelName . '...');
        $this->info('Configuring ' . $this->schema . '...');
        $this->makeMigration();
        $this->makeModel();
        $this->makeController();
        $this->makeView();
        $this->makeRoute();
    }

    protected function makeRoute()
    {
        new MakeRoute($this, $this->files);
    }

    /**
     * Generate the migration.
     */
    protected function makeMigration()
    {
        new MakeMigration($this, $this->files);
    }

    /**
     * Generate the model.
     */
    protected function makeModel()
    {
        new MakeModel($this, $this->files);
    }

    /**
     * Generete the controller
     */
    protected function makeController()
    {
        new MakeController($this, $this->files);
    }

    /**
     * Generate the views
     */
    protected function makeView()
    {
        new MakeView($this, $this->files);
    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
