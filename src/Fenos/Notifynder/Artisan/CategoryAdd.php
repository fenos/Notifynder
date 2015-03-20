<?php namespace Fenos\Notifynder\Artisan;

use Fenos\Notifynder\Categories\NotifynderCategory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CategoryAdd extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifynder:category-add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a notification category in the database';

    /**
     * @var \Fenos\Notifynder\Categories\NotifynderCategory
     */
    private $notifynderCategory;

    /**
     * Create a new command instance.
     *
     * @param  \Fenos\Notifynder\Categories\NotifynderCategory $notifynderCategory
     * @return \Fenos\Notifynder\Artisan\CategoryAdd
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

        $createCategory = $this->notifynderCategory->add(compact('name', 'text'));

        if ($createCategory) {
            $this->info("Category $createCategory->name has been created");
        } else {
            $this->error('The category has been not created check the information');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Name of the category.'),
            array('text', InputArgument::REQUIRED, 'Text of the category.'),
        );
    }
}
