<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Institution;

class CreateSeoUrlInstitution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:seo_url_institution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criando SEO-URL';

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
        $institutions = institution::withTrashed()->get();        
        foreach ($institutions as $institution) {
            $institution["seo_url"] = \App\Helpers::slug($institution['institution_name']);
            $institution["seo_url"] = \App\Helpers::generate_unique_friendly_url($institution, new Institution);
            $institution = $institution->update([
                'seo_url' => $institution['seo_url'],
            ]);           
        }
    }
}
