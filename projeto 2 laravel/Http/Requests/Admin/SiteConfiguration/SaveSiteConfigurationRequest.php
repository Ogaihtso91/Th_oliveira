<?php

namespace App\Http\Requests\Admin\SiteConfiguration;

use Illuminate\Foundation\Http\FormRequest;

class SaveSiteConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Depois verificar os requisitos para estar autorizado para essa requisição e alterar a lógica aqui
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'home_title' => 'required',
            'home_description' => 'required',
            'home_keywords' => 'required',
            'about_title' => 'required',
            'about_description' => 'required',
            'about_keywords' => 'required',
            'events_title' => 'required',
            'events_description' => 'required',
            'events_keywords' => 'required',
            'events_detail' => 'required',
            'blog_title' => 'required',
            'blog_description' => 'required',
            'blog_keywords' => 'required',
            'blog_detail' => 'required',
            'socialtecnology_detail' => 'required',
            'socialtecnology_ods_title' => 'required',
            'socialtecnology_ods_description' => 'required',
            'socialtecnology_theme_title' => 'required',
            'socialtecnology_theme_description' => 'required',
            'signup_title' => 'required',
            'signup_description' => 'required',
            'user_detail_title' => 'required',
            'user_detail_description' => 'required',
            'institution_detail_title' => 'required',
            'institution_detail_description' => 'required',
            'search_panel_video' => 'string',
            'logo' => 'sometimes|nullable|image|mimes:jpeg,png|max:1024',
            'banner_panel' => 'sometimes|nullable|image|mimes:jpeg,png|max:2048',
            'about_text_one_section_one' => 'nullable|string',
            'about_text_one_section_two' => 'nullable|string',
            'about_text_two_section_two' => 'nullable|string',
            'about_text_one_section_three' => 'nullable|string',
            'about_text_one_section_four' => 'nullable|string',
            'about_text_two_section_four' => 'nullable|string',
            'about_text_one_section_five' => 'nullable|string',
            'about_text_one_section_six' => 'nullable|string',
            'about_text_two_section_six' => 'nullable|string',
            'about_text_three_section_six' => 'nullable|string',
            'about_banner_section_one' => 'sometimes|nullable|image|mimes:jpeg,png|max:2048',
            'about_banner_section_three' => 'sometimes|nullable|image|mimes:jpeg,png|max:2048',
            'about_banner_section_five' => 'sometimes|nullable|image|mimes:jpeg,png|max:2048',
            'about_us_id' => 'nullable|numeric|integer'
        ];
    }
}
