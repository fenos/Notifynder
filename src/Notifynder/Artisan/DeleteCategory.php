<?php namespace Fenos\Notifynder\Artisan;

use Fenos\Notifynder\Contracts\NotifynderCategory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DeleteCategory extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifynder:delete:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a notifynder category by ID or Name given';

    /**
     * @var \Fenos\Notifynder\Categories\NotifynderCategory
     */
    private $notifynderCategory;

    /**
     * Create a new command instance.
     *
     * @param  NotifynderCategory                       $notifynderCategory
     * @return \Fenos\Notifynder\Artisan\DeleteCategory
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
        $indentifier = $this->argument('identifier');

        if ($this->isIntegerValue($indentifier)) {
            $delete = $this->notifynderCategory->delete($indentifier);
        } else {
            $delete = $this->notifynderCategory->deleteByName($indentifier);
        }

        if ($delete) {
            $this->info('Category has been deleted');
        } else {
            $this->error('Category Not found');
        }
    }

    public function isIntegerValue($indentifier)
    {
        return preg_match('/[0-9]/', $indentifier);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('identifier', InputArgument::REQUIRED, '1 - nameCategory'),
        );
    }
}
