<?php

namespace Fenos\Notifynder\Artisan;

use Fenos\Notifynder\Contracts\NotifynderGroup;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class CreateGroup extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifynder:create:group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store a new notifynder group in the DB.';

    /**
     * @var NotifynderGroup
     */
    private $notifynderGroup;

    /**
     * Create a new command instance.
     *
     * @param  NotifynderGroup                    $notifynderGroup
     * @return \Fenos\Notifynder\Artisan\CreateGroup
     */
    public function __construct(NotifynderGroup $notifynderGroup)
    {
        parent::__construct();
        $this->notifynderGroup = $notifynderGroup;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $nameGroup = $this->argument('name');

        if (! $this->notifynderGroup->addGroup($nameGroup)) {
            $this->error('The name must be a string with dots as namespaces');

            return false;
        }
        $this->info("Group {$nameGroup} has Been created");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'user.post.add'],
        ];
    }
}
