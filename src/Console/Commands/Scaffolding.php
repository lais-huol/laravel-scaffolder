<?php

namespace LAIS\Scaffold\Console\Commands;

use LAIS\Scaffold\Console\Commands\Makes\MakeMigration;
use LAIS\Scaffold\Console\Commands\Makes\MakeModel;
use LAIS\Scaffold\Console\Commands\Makes\MakeController;
use LAIS\Scaffold\Console\Commands\Makes\MakePlurals;
use LAIS\Scaffold\Console\Commands\Makes\MakeRoute;
use LAIS\Scaffold\Console\Commands\Makes\MakeView;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\RuntimeException;

class Scaffolding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scaffold {model} {--schema=} {--p|plural=} {--s|singular=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um CRUD completo';

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
     * @string
     */
    public $singular;

    /**
     * @string
     */
    public $plural;

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
        $this->schema = $this->option('schema');
        $this->plural = $this->option('plural');
        $this->singular = $this->option('singular');

        if(is_null($this->singular) or is_null($this->plural) or is_null($this->schema))
        {
            throw new RuntimeException('Todos os argumentos são obrigatórios --schema --plural --singular');
        }

        // Start Scaffold
        $this->info('Configurando ' . $this->modelName . '...');
        $this->info('Configurando ' . $this->schema . '...');
        $this->makePlurals();
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

    /**
     * Generate config for plurals
     */
    public function makePlurals()
    {
        new MakePlurals($this, $this->files);
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
