<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        //
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $event->menu->add('ADMINISTRAÇÃO');
            $event->menu->add([
                'text'          => 'Linha do Tempo',
                'url'           => 'admin/timeline',
                'icon'          => 'line-chart',
                'label'         => \App\ContentManager::whereNull('read_at')->count(),
                'label_color'   => 'danger timeline-counter',
            ]);
            $event->menu->add([
                'text' => 'Agenda',
                'url'  => 'admin/agenda',
                'icon' => 'calendar'
            ]);
            $event->menu->add([
                'text' => 'Blog',
                'url'  => 'admin/blog',
                'icon' => 'newspaper-o',
            ]);
            /*$event->menu->add([
                'text' => 'Depoimentos',
                'url'  => 'admin/depoimentos',
                'icon' => 'comment',
            ]);*/
            $event->menu->add([
                'text' => 'Tecnologia Social',
                'url'  => 'admin/tecnologia-social',
                'icon' => 'gears',
            ]);
            $event->menu->add([
                'text' => 'Instituições',
                'url'  => 'admin/instituicao',
                'icon' => 'building',
            ]);
            $event->menu->add('PREMIAÇÕES');
            $event->menu->add([
                'text' => 'Dashboard',
                'url'  => 'admin/administracao',
                'icon' => 'line-chart',
            ]);
            $event->menu->add([
                'text' => 'Premiação',
                'url'  => 'admin/premiacoes',
                'icon' => 'newspaper-o',
            ]);
            $event->menu->add([
                'text' => __('front.evaluation_step.evaluations_title'),
                'url'  => 'admin/avaliacoes',
                'icon' => 'building',
            ]);
            $event->menu->add([
                'text' => 'Auditoria de Etapa Final',
                'url'  => route('admin.award.audition.index'),
                'icon' => 'list-alt',
            ]);
            $event->menu->add('CONFIGURAÇÕES DE CONTA');
            $event->menu->add([
                'text' => 'Administradores',
                'url'  => 'admin/administradores',
                'icon' => 'user',
            ]);
            $event->menu->add([
                'text' => 'Grupo de Usuários e Permissões',
                'url'  => 'admin/permissoes',
                'icon' => 'user',
            ]);
            $event->menu->add([
                'text' => 'Usuários Participantes',
                'url'  => 'admin/usuarios',
                'icon' => 'user',
            ]);
            $event->menu->add('CONFIGURAÇÕES DO SITE');
            // $event->menu->add([
            //     'text' => 'Mensagem Automática',
            //     'url'  => 'admin/mensagem-automatica',
            //     'icon' => 'gears',
            // ]);
            $event->menu->add([
                'text' => 'Configurações do Site',
                'url'  => 'admin/configuracao',
                'icon' => 'gears',
            ]);
            $event->menu->add([
                'text' => 'Formulários Customizados',
                'url'  => route('admin.customStepForm.index'),
                'icon' => 'gears',
            ]);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
