<?php

namespace App\Http\Controllers\Admin;

use App\Award;
use App\CategoryAward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminitrationController extends Controller
{
    protected $award;

    public function __construct(Award $award)
    {
        $this->award = $award;
    }

    /**
     * Rota para o painel administrativo
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $awards = $this->award->orderBy('registrationsStartDate','desc')->get();
        // alterando o modo de pegar o premio, usando a data de inscricao mais recente como criterio
        $latestAward = $this->award->orderBy('registrationsStartDate','desc')->first();

        // SE existir atribuimos a categoria
        // SENÂO passamos a premiação encontrada.
        if ($latestAward) {
            $categoryAward = $latestAward->categoryAwards;
        }else {
            $categoryAward = $latestAward;
        }

        // Chama a view
        return view('admin.administration.index', compact('awards', 'latestAward', 'categoryAward'));
    }
}
