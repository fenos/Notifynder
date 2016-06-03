<?php

namespace Fenos\Notifynder\Artisan;

use Fenos\Notifynder\Contracts\NotifynderCategory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CreateCategory extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifynder:create:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and store a new notifynder category';

    /**
     * @var \\Fenos\Notifynder\Contracts\NotifynderCategory
     */
    private $notifynderCategory;

    /**
     * Create a new command instance.
     *
     * @param  NotifynderCategory                    $notifynderCategory
     * @return \Fenos\Notifynder\Artisan\CreateCategory
     */
    public function __construct(NotifynderCategory $notifynderCategory)
    {
        parent::__construct();

        $this->notifynderCategory = $notifynderCategory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $name = $this->argument('name');
        $text = $this->argument('text');

        $createCategory = $this->notifynderCategory->add($name, $text);

        if (! $createCategory) {
            $this->error('The category has been not created');

            return false;
        }

        $this->info("Category $createCategory->name has been created");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of the category.'],
            ['text', InputArgument::REQUIRED, 'Text of the category.'],
        ];
    }
}
