<?php


namespace App\Repositories\Repository\AboutUs;


use App\AboutUs;
use App\Http\Requests\Admin\SiteConfiguration\SaveSiteConfigurationRequest;

class CreateAboutUsRepository
{
    public function updateOrCreate(SaveSiteConfigurationRequest $request, $pathBannerSectionOne, $pathBannerSectionThree, $pathBannerSectionFive)
    {
        $checksIfRecordAlreadyExists = ['id' => $request->about_us_id];

        $data = [
            'text_one_section_one' => $request->about_text_one_section_one,
            'text_one_section_two' => $request->about_text_one_section_two,
            'text_two_section_two' => $request->about_text_two_section_two,
            'text_one_section_three' => $request->about_text_one_section_three,
            'text_one_section_four' => $request->about_text_one_section_four,
            'text_two_section_four' => $request->about_text_two_section_four,
            'text_one_section_five' => $request->about_text_one_section_five,
            'text_one_section_six' => $request->about_text_one_section_six,
            'text_two_section_six' => $request->about_text_two_section_six,
            'text_three_section_six' => $request->about_text_three_section_six,
        ];

        if (!empty($pathBannerSectionOne)) {
            $data['banner_section_one'] = $pathBannerSectionOne;
        }

        if (!empty($pathBannerSectionThree)) {
            $data['banner_section_three'] = $pathBannerSectionThree;
        }

        if (!empty($pathBannerSectionFive)) {
            $data['banner_section_five'] = $pathBannerSectionFive;
        }

        return AboutUs::updateOrCreate($checksIfRecordAlreadyExists, $data);
    }
}
