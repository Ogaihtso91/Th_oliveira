<?php

namespace App\Console\Commands;

use App\Award;
use App\ContentManager;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class incompleteRegisterEmailSender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:incomplete-social-tecnology-register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cadastro de tecnologia social incompleto';

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
        /**  */
        $yesterday = Carbon::now()->subDay(1)->format('yy-m-d');
        /** Busca premios com data de registros validos */
        $awards = Award::query()->has('categoryAwards')
            ->where(function ($q) {
                $q->where('registrationsStartDate', '<=', Carbon::now()->format('yy-m-d'))
                    ->where('registrationsEndDate', '>=', Carbon::now()->format('yy-m-d'));
            })->get();

        /** inscrições incompletas */
        $subscriptions = \App\ContentManager::query()
            ->where('type', 1)->where('status',0)->whereNotNull('registration')
            ->whereDate('created_at', $yesterday)->get();

        /** filtrando as inscrições pela premiação valida*/
        $subscriptions = $subscriptions->filter( function ($value, $key) use ($awards) {
            $arr = $value->getArrayNewValuesAttribute();
            return collect($awards)->where( 'id', $arr->award_id ) ;
        });

        foreach($subscriptions as $subscription) {
            /** Envia o email de inscrição incompleta */
            Mail::to($subscription->institution->email)
                ->send( new \App\Mail\SendIncompleteSubscriptionAlertMail( $subscription ) );
        }

    }
}
