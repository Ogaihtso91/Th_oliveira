<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpa o cache e gera os arquivos novamente';

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
        // Clear Files
        print "Cleaning View Cache\n";
        $exitCode = Artisan::call('view:clear');
        print "Cleaning Cache\n";
        $exitCode = Artisan::call('cache:clear');
        print "Cleaning Route Cache\n";
        $exitCode = Artisan::call('route:clear');
        print "Cleaning Config Cache\n";
        $exitCode = Artisan::call('config:clear');

        print ".\n.\n.\n";
        // Optimizing Cache
        print "Optimizing Cache\n";
        $exitCode = Artisan::call('optimize:clear');

        print ".\n.\n.\n";
        // Recreate Files
        print "Generating View Cache\n";
        $exitCode = Artisan::call('view:cache');
        print "Generating Route Cache\n";
        $exitCode = Artisan::call('route:cache');
        print "Generating Config Cache\n";
        $exitCode = Artisan::call('config:cache');

        print 'Finished'; //Return anything
    }
}
