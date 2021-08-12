<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CustomStepForm;
use App\CustomStepFields;
use App\CustomFieldTypes;
use App\SocialTecnology;

class CustomStepFormController extends Controller
{
    /**
     * route('admin.customStepForm.index')
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customStepForm = CustomStepForm::all();
        return view('admin.customStepForm.index', compact('customStepForm'));
    }

    /**
     * route('admin.customStepForm.create')
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // TODO: avaliar mudança para enum
        $fieldTypes = CustomFieldTypes::query()->orderBy('name')->get();

        return view('admin.customStepForm.create', compact('fieldTypes'));
    }

    /**
     * route('admin.customStepForm.store')
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'custom_fields' => 'required|array',
            'custom_fields.*.customFieldType_id' => 'required',
            'custom_fields.*.title' => 'required|string'
        ]);

        $customStepForm = new CustomStepForm();

        $customStepForm->title = $validated['title'];
        // gerar slug automatico
        $customStepForm->name = str_slug($validated['title'], '-');

        $collectCustomStepFields = collect();

        foreach($validated['custom_fields'] as $field) {
            $collectCustomStepFields->push(new CustomStepFields($field));
        }

        $customStepForm->save();
        $customStepForm->stepFields()->saveMany($collectCustomStepFields);

        return redirect(route('admin.customStepForm.index'))->with('message', 'Formulário customizado cadastrado com sucesso!');
    }

    /**
     * route('admin.customStepForm.show')
     * Display the specified resource.
     *
     * @param  CustomStepForm $customStepForm
     * @return \Illuminate\Http\Response
     */
    public function show(CustomStepForm $customStepForm)
    {
        $social_tecnology = SocialTecnology::first();
        $customFields = $customStepForm->stepFields()->where('status', 1)->orderBy('field_position', 'asc')->get() ?? [];
        return view('admin.customStepForm.show', compact('social_tecnology','customStepForm', 'customFields'));
    }

    /**
     * route('admin.customStepForm.edit')
     * Show the form for editing the specified resource.
     *
     * @param  CustomStepForm $customStepForm
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomStepForm $customStepForm)
    {
        // TODO: avaliar mudança para enum
        $fieldTypes = CustomFieldTypes::all();

        $fields = $customStepForm->stepFields;

        return view('admin.customStepForm.edit', compact('fields', 'customStepForm', 'fieldTypes'));
    }

    /**
     * route('admin.customStepForm.update')
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CustomStepForm $customStepForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomStepForm $customStepForm)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'custom_fields' => 'required|array',
            'custom_fields.*.customFieldType_id' => 'required',
            'custom_fields.*.title' => 'required|string',
            'remove_form_fields' => 'nullable'
        ]);

        $customStepForm->title = $validated['title'];
        $customStepForm->name = str_slug($validated['title'], '-');;

        // remove fields from a form
        if(!empty($validated['remove_form_fields'])) {
            $formFields = explode(',', $validated['remove_form_fields']);

            foreach ($formFields as $field) {
                if(!empty($field)) {
                    CustomStepFields::find($field)->delete();
                }
            }
        }

        $collectCustomStepFields = collect();
        // iteração para adicionar ou atualizar os os campos do formulario
        if(!empty($validated['custom_fields'])) {
            foreach($validated['custom_fields'] as $field) {
                if(isset($field['id']) && !is_null($field['id']) ){
                    $x = CustomStepFields::find($field['id']);
                    unset($field['id']);
                    $x->update($field);
                }else{
                    $collectCustomStepFields->push(new CustomStepFields($field));
                }
            }
        }

        if( $customStepForm->save() && $customStepForm->stepFields()->saveMany($collectCustomStepFields) ){
            return redirect(route('admin.customStepForm.index'))->with('message', 'Formulário editado com sucesso!');
        }

        return redirect(route('admin.customStepForm.index'))->withErros('falha ao editar o formulário');
    }

    /**
     * route('admin.customStepForm.destroy')
     * Remove the specified resource from storage.
     *
     * @param  CustomStepForm $customStepForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomStepForm $customStepForm)
    {
        // todo verificar se o delete on cascade está funcionando corretamente
        $customStepForm->delete();

        return redirect(route('admin.customStepForm.index'));
    }

    public function testeOrder()
    {
        $customStepForm = CustomStepForm::all();
        return view('admin.customStepForm.view_basic', compact('customStepForm'));
    }

    public function fieldOrder (CustomStepForm $customStepForm)
    {
        $stepFields = $customStepForm->stepFields()->orderBy('field_position', 'asc')->get();
        $fieldTypes = CustomFieldTypes::all();
        return view('admin.customStepForm.field_order', compact('customStepForm', 'stepFields', 'fieldTypes'));
    }

    public function fieldOrderStore (Request $request, CustomStepForm $customStepForm)
    {
        $fieldList = explode(',',$request->stepList);

        foreach ($fieldList as $key => $value) {
            // buscamos o campo no formulario
            $field = $customStepForm->stepFields()->where('id', $value)->first();

            $field->field_position = ($key+1);
            $field->save();
        }

        return redirect(route('admin.customStepForm.index'))->with('message', 'Campos customizados ordenado com sucesso!');
    }
}
