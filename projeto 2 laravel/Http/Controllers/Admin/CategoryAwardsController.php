<?php

namespace App\Http\Controllers\Admin;

use App\Award;
use App\CategoryAward;
use App\Filesystem\Storage;
use App\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CategoryAwardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function index(Award $award)
    {
        // criando parametros para breadcrumb
        $breadcrumb_params = Collection::make([
            'award' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.award').$award->name,
                'link' => [
                    'name' => 'admin.awards.index',
                    'params' => [
                        'award' => $award->id
                    ]
                ],
            ],
            'category_awards' => [
                'active' => true,
                'label' => __('front.award.breadcrumb.category_awards'),
                
            ]
        ]);


        $categoryAwards = $award->categoryAwards;

        return view('admin.awards.categoryAwards.index', compact('categoryAwards','award', 'breadcrumb_params'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function create(Award $award)
    {
        //
        return view('admin.awards.categoryAwards.create', compact('award'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Award $award)
    {
        $categoryAward  = new CategoryAward();
        $categoryAward->name                = $request->name;
        $categoryAward->description         = $request->description;
        $categoryAward->categoryAwardType   = $request->categoryAwardType;
        $categoryAward->isCertificationType   = $request->isCertificationType;
        if(!empty($request['main_image'])) {
            foreach ($request['main_image'] as $main_image)
            {
                if(!empty($main_image))
                {
                    // Pega o nome do arquivo
                    $fileName = $main_image->getClientOriginalName();
                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);
                    // Recupera a extensão do arquivo
                    $extension = $main_image->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('awards/'.$award->id.'/category/images/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    $main_image->storeAs('awards/'.$award->id."/category/images", $fileName);
                }
            }
        $categoryAward->image = $fileName;
        }

        $award->categoryAwards()->save($categoryAward);
        /*Tarefa  4474 marcio.rosa*/
        return redirect()->route('admin.awards.categoryAwards.index', [$award->id,$categoryAward->id])
                ->with('message', 'Categoria de premiação cadastrada.');
        /**return redirect()->route('admin.awards.categoryAwards.show',[$award->id,$categoryAward->id])
                ->with('message', 'Categoria de premiação cadastrada.');*/
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Award  $award
     * @param  \App\CategoryAward  $categoryAward
     * @return \Illuminate\Http\Response
     */
    public function show(Award $award, CategoryAward $categoryAward)
    {

        // criando parametros para breadcrumb
        $breadcrumb_params = Collection::make([
            'award' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.award').$award->name,
                'link' => [
                    'name' => 'admin.awards.index',
                    'params' => [
                        'award' => $award->id
                    ]
                ],
            ],
            'category_awards' => [
                'active' => false,
                'label' => __('front.award.breadcrumb.category_awards').$categoryAward->name,
                'link' => [
                    'name' => 'admin.awards.categoryAwards.index',
                    'params' => [
                        'award' => $award->id,
                        'category_award' => $categoryAward->id,
                    ]
                ],
            ],
        ]);

        return view('admin.awards.categoryAwards.show', compact('categoryAward', 'breadcrumb_params'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Award  $award
     * @param  \App\CategoryAward  $categoryAward
     * @return \Illuminate\Http\Response
     */
    public function edit(Award $award, CategoryAward $categoryAward)
    {
        $categoryAward = $award->categoryAwards->find($categoryAward);
        return view('admin.awards.categoryAwards.edit', compact('categoryAward'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Award  $award
     * @param  \App\CategoryAward  $categoryAward
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Award  $award, CategoryAward $categoryAward)
    {
        $categoryAward->name                = $request->name;
        $categoryAward->description         = $request->description;
        $categoryAward->categoryAwardType   = $request->categoryAwardType;
        $categoryAward->isCertificationType   = $request->isCertificationType;
        if(!empty($request['main_image'])) {
            foreach ($request['main_image'] as $main_image)
            {
                if(!empty($main_image))
                {
                    // Pega o nome do arquivo
                    $fileName = $main_image->getClientOriginalName();
                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);
                    // Recupera a extensão do arquivo
                    $extension = $main_image->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('awards/'.$award->id.'/category/images/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    $main_image->storeAs('awards/'.$award->id."/category/images", $fileName);
                }
            }
        $categoryAward->image = $fileName;
        }


        $award->categoryAwards()->save($categoryAward);

        return redirect()->route('admin.awards.categoryAwards.index',$award->id)
                ->with('message', 'Categoria de premiação atualizada.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Award  $award
     * @param  \App\CategoryAward  $categoryAward
     * @return \Illuminate\Http\Response
     */
    public function destroy(Award  $award, CategoryAward $categoryAward)
    {
        $award->categoryAwards->find($categoryAward)->delete();
        return redirect()->route('admin.awards.categoryAwards.index',$award->id)
                    ->with('message', 'Categoria de premiação excluída.');
    }
}
