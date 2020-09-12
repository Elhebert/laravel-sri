<?php

namespace Elhebert\SubresourceIntegrity\Console;

use Illuminate\Console\Command;
use \Elhebert\SubresourceIntegrity\Contracts\SriCacheManager;

class SriClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sri:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the sri hash cache file';

    /**
     * Create a new config clear command instance.
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
     * @return void
     */
    public function handle(SriCacheManager $sriCache)
    {
        $sriCache->clear();

        $this->info('Sri cache cleared!');
    }
}
