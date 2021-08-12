<?php

namespace App\Http\Controllers\Admin;

use App\Award;
use App\AwardFile;
use App\AwardVideo;
use App\AwardImages;
use App\Filesystem\Storage;
use App\Helpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\View\View;
use Validator;

class AwardsController extends Controller
{
    /* CUSTOM VALIDATION MESSAGES */
    protected $messages = [
        'formated_registrationsStartDate.date' => 'As informãções de Data e Hora de início do evento não estão corretas.',
        'formated_registrationsEndDate.date' => 'As informãções de Data e Hora de término do evento não estão corretas.',
        'formated_startDate.date' => 'As informãções de Data e Hora de início do evento não estão corretas.',
        'formated_endDate.date' => 'As informãções de Data e Hora de término do evento não estão corretas.',
    ];

    /**
     * Validate Events Form
     * @param   Illuminate\Http\Request $request
     * @return  Validator
     */
    protected function validator(Request $request)
    {
        // Cria o objeto de validação
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'flagUniqueEntry'=>'sometimes',
            'formated_registrationsStartDate'=>'required|date',
            'formated_registrationsEndDate'=>'required|date',
            'formated_startDate'=>'required|date',
            'formated_endDate'=>'required|date',
            'add_images' => 'sometimes|nullable',
            'main_image' => 'sometimes|nullable',
        ], $this->messages);

        // Valida se as datas estão corretas
        $validation->after(function ($validator) {

            // Valida se uma data é maior que a outra
            $validated_data = $validator->getData();
            $failed_fields = $validator->failed();

            if (!isset($failed_fields['formated_registrationsStartDate']) && !isset($failed_fields['formated_registrationsEndDate'])
            && !isset($failed_fields['formated_startDate']) && !isset($failed_fields['formated_endDate']) ) {

                // Concatena os valores em uma variável datetime para comparação
                $registrationsStartDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_registrationsStartDate']);
                $registrationsEndDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_registrationsEndDate']);
                $startDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_startDate']);
                $endDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_endDate']);
                // Verifica se está a data inicial e maior que a data final
                if ($registrationsStartDate > $registrationsEndDate) {
                        // Adiciona mensagem no validador
                        $validator->errors()->add('formated_registrationsStartDate', 'Início do periodo de inscrições não pode ser posterior ao fim.');
                }
                if ($startDate > $endDate) {
                    // Adiciona mensagem no validador
                    $validator->errors()->add('formated_startDate', 'Início do periodo de vigência não pode ser posterior ao fim.');
                }
            }
        });
        return $validation;
    }


    protected function editvalidator(Request $request)
    {
        // Cria o objeto de validação
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'flagUniqueEntry'=>'sometimes',
            'formated_registrationsStartDate'=>'required|date',
            'formated_registrationsEndDate'=>'required|date',
            'formated_startDate'=>'required|date',
            'formated_endDate'=>'required|date',
        ], $this->messages);

        // Valida se as datas estão corretas
        $validation->after(function ($validator) {

            // Valida se uma data é maior que a outra
            $validated_data = $validator->getData();
            $failed_fields = $validator->failed();

            if (!isset($failed_fields['formated_registrationsStartDate']) && !isset($failed_fields['formated_registrationsEndDate'])
            && !isset($failed_fields['formated_startDate']) && !isset($failed_fields['formated_endDate']) ) {

                // Concatena os valores em uma variável datetime para comparação
                $registrationsStartDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_registrationsStartDate']);
                $registrationsEndDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_registrationsEndDate']);
                $startDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_startDate']);
                $endDate = Carbon::createFromFormat('Y/m/d', $validated_data['formated_endDate']);
                // Verifica se está a data inicial e maior que a data final
                if ($registrationsStartDate > $registrationsEndDate) {
                        // Adiciona mensagem no validador
                        $validator->errors()->add('formated_registrationsStartDate', 'Início do periodo de inscrições não pode ser posterior ao fim.');
                }
                if ($startDate > $endDate) {
                    // Adiciona mensagem no validador
                    $validator->errors()->add('formated_startDate', 'Início do periodo de vigência não pode ser posterior ao fim.');
                }
            }
        });
        return $validation;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Pesquisa todas premiações e envia para listagem na pagina
        $awards = Award::orderBy('registrationsStartDate','desc')->get();
        return view('admin.awards.index', compact('awards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.awards.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {

        $award = new Award();

        // Altera o formato da data no request para validação
        $request_inputs = $request->all();

        // Cria a data formatada para validar
        $request_inputs['formated_registrationsStartDate'] = empty($request->registrationsStartDate) ? $request->registrationsStartDate : \App\Helpers::format_date($request->registrationsStartDate);
        $request_inputs['formated_registrationsEndDate']   = empty($request->registrationsEndDate) ? $request->registrationsEndDate : \App\Helpers::format_date($request->registrationsEndDate);
        $request_inputs['formated_startDate']   = empty($request->startDate) ? $request->startDate : \App\Helpers::format_date($request->startDate);
        $request_inputs['formated_endDate']   = empty($request->endDate) ? $request->endDate : \App\Helpers::format_date($request->endDate);


        // Coloca os valores formatados no request para validação
        $request->replace($request_inputs);


        // Valida os campos  Tarefa 4480 Causando erro nas views
        $validated = $this->validator($request)->validate();

        $award->name = $request->name;
        $award->description = $request->description ;
        $award->registrationsStartDate  = Carbon::createFromFormat('Y/m/d', $request->formated_registrationsStartDate)->format('Y-m-d');
        $award->registrationsEndDate    = Carbon::createFromFormat('Y/m/d', $request->formated_registrationsEndDate)->format('Y-m-d');
        $award->startDate               = Carbon::createFromFormat('Y/m/d', $request->formated_startDate)->format('Y-m-d');
        $award->endDate                 = Carbon::createFromFormat('Y/m/d', $request->formated_endDate)->format('Y-m-d');

        $award->flagUniqueEntry = $request->flagUniqueEntry ?? 1;
        $award->allowForProfitSocialTechnologies = $request->allowForProfitSocialTechnologies ?? 1;
        $award->terms = $request->terms;

        $award->save();

        // Verifica se há novos vídeos adicionados
        if(!empty($request['videos'])) {
            foreach ($request['videos'] as $video_item) {
                if(!empty($video_item))
                    AwardVideo::create([
                        'award_id' => $award->id,
                        'video_url' => $video_item
                    ]);
            }
        }
        //  dd($request->file('add_files'));

        // ANEXOS
        if(!empty($request['add_files'])) {
            foreach ($request['add_files'] as $file_item)
            {
                if(!empty($file_item) && $file_item->isValid())
                {
                    // Pega o nome do arquivo
                    $fileName = $file_item->getClientOriginalName();
                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);
                    // Recupera a extensão do arquivo
                    $extension = $file_item->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('awards/'.$award->id.'/files/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($file_item->storeAs('awards/'.$award->id."/files", $fileName))
                    {
                        AwardFile::create([
                            'award_id' => $award->id,
                            'file' => $fileName
                        ]);
                    }
                }
            }
        }

        if(!empty($request['main_image'])) {
            foreach ($request['main_image'] as $main_image)
            {
                if(!empty($main_image) && $main_image->isValid())
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
                    while (Storage::exists('awards/'.$award->id.'/images/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($main_image->storeAs('awards/'.$award->id."/images", $fileName))
                    {
                        AwardImages::create([
                            'award_id' => $award->id,
                            'image' => $fileName,
                            'main' => '1'
                        ]);
                    }
                }
            }

        }

        if(!empty($request['add_images'])) {
            foreach ($request['add_images'] as $add_images)
            {
                if(!empty($add_images) && $add_images->isValid())
                {
                    // Pega o nome do arquivo
                    $fileName = $add_images->getClientOriginalName();
                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);
                    // Recupera a extensão do arquivo
                    $extension = $add_images->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('awards/'.$award->id.'/images/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($add_images->storeAs('awards/'.$award->id."/images", $fileName))
                    {
                        AwardImages::create([
                            'award_id' => $award->id,
                            'image' => $fileName,
                            'main' => '0'
                        ]);
                    }
                }
            }
        }

        $award->save();

        // dd( $award->toArray(), $award->files->toArray() , $award->videos->toArray()  );

        return redirect()->route('admin.awards.index')->with('message', 'Premiação Cadastrada.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Award  $award
     * @return Response
     */
    public function show(Award $award)
    {
        $images = $award->images;
        $files = $award->files;
        $videos = $award->videos;
        return view('admin.awards.show', compact('award', 'files', 'videos', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Award  $award
     * @return Response
     */
    public function edit(Award $award)
    {
        $mainImage = $award->mainImage();
        $images = $award->images;
        $files = $award->files;
        $videos = $award->videos;
        return view('admin.awards.edit', compact('award','files','videos', 'images', 'mainImage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Award  $award
     * @return Response
     */
    public function update(Request $request, Award $award)
    {
        // Altera o formato da data no request para validação
        $request_inputs = $request->all();

        // Cria a data formatada para validar
        $request_inputs['formated_registrationsStartDate'] = empty($request->registrationsStartDate) ? $request->registrationsStartDate : \App\Helpers::format_date($request->registrationsStartDate);
        $request_inputs['formated_registrationsEndDate']   = empty($request->registrationsEndDate) ? $request->registrationsEndDate : \App\Helpers::format_date($request->registrationsEndDate);
        $request_inputs['formated_startDate']   = empty($request->startDate) ? $request->startDate : \App\Helpers::format_date($request->startDate);
        $request_inputs['formated_endDate']   = empty($request->endDate) ? $request->endDate : \App\Helpers::format_date($request->endDate);

        // Coloca os valores formatados no request para validação
        $request->replace($request_inputs);

        // Valida os campos
        $validated = $this->editvalidator($request)->validate();

        $award->name = $request->name ;
        $award->description = $request->description ;
        $award->registrationsStartDate  = Carbon::createFromFormat('Y/m/d', $request->formated_registrationsStartDate)->format('Y-m-d');
        $award->registrationsEndDate    = Carbon::createFromFormat('Y/m/d', $request->formated_registrationsEndDate)->format('Y-m-d');
        $award->startDate               = Carbon::createFromFormat('Y/m/d', $request->formated_startDate)->format('Y-m-d');
        $award->endDate                 = Carbon::createFromFormat('Y/m/d', $request->formated_endDate)->format('Y-m-d');

        $award->flagUniqueEntry = $request->flagUniqueEntry ?? 1;
        $award->allowForProfitSocialTechnologies = $request->allowForProfitSocialTechnologies ?? 1;
        $award->terms = $request->terms;

        $award->save();

        /******************/
        /******VIDEOS******/
        /******************/
        // Verifica se há algum vídeo para excluir
        if(!empty($request['remove_videos'])){
            $arr_rm_videos = explode(',', $request['remove_videos']);
            foreach ($arr_rm_videos as $video_rm_item) {
                if(!empty($video_rm_item))
                AwardVideo::find($video_rm_item)->delete();
            }
        }

        // Verifica se há novos vídeos adicionados
        if(!empty($request['videos'])) {
            foreach ($request['videos'] as $video_item) {
                if(!empty($video_item))
                    AwardVideo::create([
                        'award_id' => $award->id,
                        'video_url' => $video_item
                    ]);
            }
        }

        /******************/
        /******ANEXOS******/
        /******************/

        // Verifica se há algum arquivo para excluir
        if(!empty($request['remove_files']))
        {
            $arr_rm_files = explode(',', $request['remove_files']);
            foreach ($arr_rm_files as $file_rm_item)
            {
                if(!empty($file_rm_item))
                {
                    // Busca o objeto para excluir o arquivo
                    $file_obj = AwardFile::find($file_rm_item);
                    // Exclui do banco de dados
                    $file_obj->delete();
                }
            }
        }

        if(!empty($request['add_files']))
        {
            foreach ($request['add_files'] as $file_item)
            {
                if(!empty($file_item) && $file_item->isValid())
                {
                    // Pega o nome do arquivo
                    $fileName = $file_item->getClientOriginalName();
                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);
                    // Recupera a extensão do arquivo
                    $extension = $file_item->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('awards/'.$award->id.'/files/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($file_item->storeAs('awards/'.$award->id."/files", $fileName))
                    {
                        AwardFile::create([
                            'award_id' => $award->id,
                            'file' => $fileName
                        ]);
                    }
                }
            }
        }

        /*****************************/
        /******Imagem Principal******/
        /****************************/
        // Verifica se há alguma imagem principal para excluir
        if(!empty($request['remove_image_main'])){
            $arr_rm_image_main = explode(',', $request['remove_image_main']);
            foreach ($arr_rm_image_main as $image_rm_item) {
                if(!empty($image_rm_item))
                 AwardImages::find($image_rm_item)->delete();
            }
        }


          if(!empty($request['main_image'])) {
            foreach ($request['main_image'] as $main_image)
            {
                if(!empty($main_image) && $main_image->isValid())
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
                    while (Storage::exists('awards/'.$award->id.'/images/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($main_image->storeAs('awards/'.$award->id."/images", $fileName))
                    {
                        AwardImages::create([
                            'award_id' => $award->id,
                            'image' => $fileName,
                            'main' => '1'
                        ]);
                    }
                }
            }

        }


        /*******************************/
        /******Imagem da Galeria ******/
        /*****************************/
        // Verifica se há algum vídeo para excluir
        if(!empty($request['remove_images_gallery'])){
            $arr_rm_images_gellery = explode(',', $request['remove_images_gallery']);
            foreach ($arr_rm_images_gellery as $image_rm_item) {
                if(!empty($image_rm_item))
                 AwardImages::find($image_rm_item)->delete();
            }
        }

        if(!empty($request['add_images'])) {
            foreach ($request['add_images'] as $add_images)
            {
                if(!empty($add_images) && $add_images->isValid())
                {
                    // Pega o nome do arquivo
                    $fileName = $add_images->getClientOriginalName();
                    // Remove os acentos do nome do arquivo
                    $fileName = Helpers::create_file_name_from_existing_name($fileName);
                    // Recupera a extensão do arquivo
                    $extension = $add_images->getClientOriginalExtension();
                    // Verifica se já existe arquivo com o nome
                    $aux_name = 0;
                    $ori_fileName = str_replace('.'.$extension, '', $fileName);
                    while (Storage::exists('awards/'.$award->id.'/images/'.$fileName)) {
                        $aux_name++;
                        $fileName = $ori_fileName.'('.$aux_name.").".$extension;
                    }
                    // Salva a imagem no banco
                    if($add_images->storeAs('awards/'.$award->id."/images", $fileName))
                    {
                        AwardImages::create([
                            'award_id' => $award->id,
                            'image' => $fileName,
                            'main' => '0'
                        ]);
                    }
                }
            }
        }

        $award->save();
        return redirect()->route('admin.awards.index')->with('message', 'Premiação atualizada.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Award  $award
     * @return Response
     */
    public function destroy(Award $award)
    {
        $award->delete();
        return redirect()->route('admin.awards.index')->with('message', 'Premiação Excluída.');
    }
}
