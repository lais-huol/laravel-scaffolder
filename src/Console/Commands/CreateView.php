<?php

namespace LAIS\Scaffold\Console\Commands;

use Illuminate\Console\Command;

class CreateView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the views from your models';

    /**
     * The name of the view passed by argument.
     *
     * @var string
     */
    protected $viewName;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->viewName = $this->argument('view');
        $this->modelName = $this->ask('Enter the name of the model: ');
        $this->createDirectories();
        $this->exportViews();
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir(base_path('resources/views/layouts'))) {
            mkdir(base_path('resources/views/layouts'), 0755, true);
        }

        if (! is_dir(base_path('resources/views/'.$this->viewName))) {
            mkdir(base_path('resources/views/'.$this->viewName), 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $view) {
            copy(
                __DIR__.'/stubs/views/'.$view.'.stub',
                base_path('resources/views/'.$this->viewName.'/'.$view.'.blade.php')
            );
        }

        copy(
            __DIR__.'/stubs/views/layouts/app.stub',
            base_path('resources/views/layouts/app.blade.php')
        );
    }
}
