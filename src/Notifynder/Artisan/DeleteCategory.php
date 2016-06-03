<?php

namespace Fenos\Notifynder\Artisan;

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
     * @var \\Fenos\Notifynder\Contracts\NotifynderCategory
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
        $identifier = $this->argument('identifier');

        if ($this->isIntegerValue($identifier)) {
            $delete = $this->notifynderCategory->delete($identifier);
        } else {
            $delete = $this->notifynderCategory->deleteByName($identifier);
        }

        if (! $delete) {
            $this->error('Category Not found');

            return false;
        }
        $this->info('Category has been deleted');
    }

    public function isIntegerValue($identifier)
    {
        return preg_match('/[0-9]/', $identifier);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['identifier', InputArgument::REQUIRED, '1 - nameCategory'],
        ];
    }
}
