<?php

namespace Fenos\Notifynder\Artisan;

use Fenos\Notifynder\Contracts\NotifynderGroup;
use Fenos\Notifynder\Parsers\ArtisanOptionsParser;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PushCategoryToGroup extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifynder:push:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Associate the categories to a group';

    /**
     * @var NotifynderGroup
     */
    private $notifynderGroup;

    /**
     * @var ArtisanOptionsParser
     */
    private $artisanOptionsParser;

    /**
     * Create a new command instance.
     *
     * @param  NotifynderGroup                              $notifynderGroup
     * @param  ArtisanOptionsParser                         $artisanOptionsParser
     * @return \Fenos\Notifynder\Artisan\PushCategoryToGroup
     */
    public function __construct(NotifynderGroup $notifynderGroup,
        ArtisanOptionsParser $artisanOptionsParser)
    {
        parent::__construct();
        $this->notifynderGroup = $notifynderGroup;
        $this->artisanOptionsParser = $artisanOptionsParser;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $arguments = $this->getArgumentsConsole();

        $categoryGroup = array_shift($arguments);
        $arguments = $arguments[0];
        $categories = explode(',', $arguments);

        $groupCategories = $this->notifynderGroup
            ->addMultipleCategoriesToGroup($categoryGroup, $categories);

        if ($groupCategories) {
            foreach ($groupCategories->categories as $category) {
                $this->info("Category {$category->name} has been associated to the group {$groupCategories->name}");
            }
        } else {
            $this->error('The name must be a string with dots as namespaces');
        }
    }

    /**
     * @return array|string
     */
    public function getArgumentsConsole()
    {
        $names = $this->argument('name');

        $categories = $this->option('categories');

        $categories = $this->artisanOptionsParser->parse($categories);

        array_unshift($categories, $names);

        return $categories;
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

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['categories', null, InputOption::VALUE_OPTIONAL, 'notifynder.name', []],
        ];
    }
}
