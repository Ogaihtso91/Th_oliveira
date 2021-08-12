<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SocialTecnology;

class UpdateFullText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:fulltext';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update fulltext columns in datatables';

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
        print "Starting update\n\n";

        // Get All Social Tecnologies
        $socialtecnologies = SocialTecnology::withTrashed()->get();

        // Run into social tecnologies to update
        foreach ($socialtecnologies as $socialtecnology_item) {

            print "\"{$socialtecnology_item->socialtecnology_name}\" - Start Updating\n";

            // Updating theme fulltextindex
            print "\"{$socialtecnology_item->socialtecnology_name}\" - Setting Theme Fulltext \"{$socialtecnology_item->themes->implode('name', ',')}\"\n";
            $socialtecnology_item->fulltext_themes = $socialtecnology_item->themes->implode('name', ',');

            print "\"{$socialtecnology_item->socialtecnology_name}\" - Setting Keywords Fulltext \"{$socialtecnology_item->keywords->implode('name', ',')}\"\n";
            $socialtecnology_item->fulltext_keywords = $socialtecnology_item->keywords->implode('name', ',');

            print "\"{$socialtecnology_item->socialtecnology_name}\" - Setting Institution Fulltext \"{$socialtecnology_item->institution->institution_name}\"\n";
            $socialtecnology_item->fulltext_institution = $socialtecnology_item->institution->institution_name;

            $socialtecnology_item->save();

            print "\"{$socialtecnology_item->socialtecnology_name}\" - Updated\n\n";
        }

        print "All Fulltext was updated\n\n";
    }
}
