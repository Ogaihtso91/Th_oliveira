<?php


namespace App\Services\Admin\SiteConfiguration;


use App\Http\Requests\Admin\SiteConfiguration\SaveSiteConfigurationRequest;
use App\Repositories\Repository\AboutUs\CreateAboutUsRepository;
use App\Repositories\Repository\AboutUs\GetAboutUsRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SiteConfigurationService
{
    private const SECONDS_TO_SLEEP = 10;
    private const LOGO_NAME = "site_logo";
    private const LOGO_KEY = 'logo';
    private const CUSTOM_CONF_LOGO = 'custom_configuration.logo';
    private const BANNER_PANEL_NAME = "banner_panel";
    private const BANNER_PANEL_KEY = 'banner_panel';
    private const CUSTOM_CONF_BANNER_PANEL = 'custom_configuration.banner_panel';
    private const BANNER_SECTION_ONE_KEY = 'about_banner_section_one';
    private const BANNER_SECTION_THREE_KEY = 'about_banner_section_three';
    private const BANNER_SECTION_FIVE_KEY = 'about_banner_section_five';

    private $createAboutUsRepository, $getAboutUsRepository;

    public function __construct()
    {
        $this->createAboutUsRepository = new CreateAboutUsRepository();
        $this->getAboutUsRepository = new GetAboutUsRepository();
    }

    /**
     * @param SaveSiteConfigurationRequest $request
     * @return array
     */
    private function defineArrayWithConfigurationValues(SaveSiteConfigurationRequest $request): array
    {
        return [
            'about_video' => $request->search_panel_video,
            'seo.home.title' => $request->home_title,
            'seo.home.description' => $request->home_description,
            'seo.home.keywords' => $request->home_keywords,
            'seo.about.title' => $request->about_title,
            'seo.about.description' => $request->about_description,
            'seo.about.keywords' => $request->about_keywords,
            'seo.events.title' => $request->events_title,
            'seo.events.description' => $request->events_description,
            'seo.events.keywords' => $request->events_keywords,
            'seo.events.detail' => $request->events_detail,
            'seo.blog.title' => $request->blog_title,
            'seo.blog.description' => $request->blog_description,
            'seo.blog.keywords' => $request->blog_keywords,
            'seo.blog.detail' => $request->blog_detail,
            'seo.socialtecnology.detail' => $request->socialtecnology_detail,
            'seo.socialtecnology.ods-title' => $request->socialtecnology_ods_title,
            'seo.socialtecnology.ods-description' => $request->socialtecnology_ods_description,
            'seo.socialtecnology.theme-title' => $request->socialtecnology_theme_title,
            'seo.socialtecnology.theme-description' => $request->socialtecnology_theme_description,
            'seo.signup.title' => $request->signup_title,
            'seo.signup.description' => $request->signup_description,
            'seo.user-profile.title' => $request->user_detail_title,
            'seo.user-profile.description' => $request->user_detail_description,
            'seo.institution-profile.title' => $request->institution_detail_title,
            'seo.institution-profile.description' => $request->institution_detail_description,
        ];
    }

    private function uploadConfigurationImage($image, string $valueCurrentImage, string $imageName)
    {
        $currentImage = config($valueCurrentImage);

        if (!empty($currentImage)) Storage::delete($currentImage);

        $extension = $image->extension();

        $imageName = "$imageName.$extension";

        return $image->storeAs('', $imageName) ? $imageName : null;
    }

    private function updateConfigurationFile(array $arrayConfigUpdate)
    {
        \Config::write('custom_configuration', $arrayConfigUpdate);
    }

    private function clearCacheAndSleep(int $secondsToSleep): void
    {
        artisan_config_clear();
        artisan_config_cache();
        sleep($secondsToSleep); // Time to update the configuration file
    }

    public function updateConfigurationFileData(SaveSiteConfigurationRequest $request): bool
    {
        $arrayConfigUpdate = $this->defineArrayWithConfigurationValues($request);

        $logoImageReceived = $request->hasfile(self::LOGO_KEY) && $request->file(self::LOGO_KEY)->isValid();

        $bannerImageReceived = $request->hasfile(self::BANNER_PANEL_KEY) && $request->file(self::BANNER_PANEL_KEY)->isValid();

        if ($logoImageReceived) {
            $logoName = $this->uploadConfigurationImage($request->logo, self::CUSTOM_CONF_LOGO, self::LOGO_NAME);

            if (empty($logoName)) return false;

            $arrayConfigUpdate = Arr::add($arrayConfigUpdate, self::LOGO_KEY, $logoName);
        }

        if ($bannerImageReceived) {
            $bannerName = $this->uploadConfigurationImage($request->banner_panel, self::CUSTOM_CONF_BANNER_PANEL, self::BANNER_PANEL_NAME);

            if (empty($bannerName)) return false;

            $arrayConfigUpdate = Arr::add($arrayConfigUpdate, self::BANNER_PANEL_KEY, $bannerName);
        }

        $this->updateConfigurationFile($arrayConfigUpdate);

        $this->clearCacheAndSleep(self::SECONDS_TO_SLEEP);

        return true;
    }

    public function savePageDataAboutUs(SaveSiteConfigurationRequest $request): bool
    {
        $bannerSectionOneReceived = $request->hasfile(self::BANNER_SECTION_ONE_KEY) && $request->file(self::BANNER_SECTION_ONE_KEY)->isValid();
        $bannerSectionThreeReceived = $request->hasfile(self::BANNER_SECTION_THREE_KEY) && $request->file(self::BANNER_SECTION_THREE_KEY)->isValid();
        $bannerSectionFiveReceived = $request->hasfile(self::BANNER_SECTION_FIVE_KEY) && $request->file(self::BANNER_SECTION_FIVE_KEY)->isValid();

        $sectionOneBannerPath = null;
        $sectionThreeBannerPath = null;
        $sectionFiveBannerPath = null;

        if ($bannerSectionOneReceived) {
            $sectionOneBannerPath = $this->uploadBanner($request->about_banner_section_one, self::BANNER_SECTION_ONE_KEY);
            if (empty($sectionOneBannerPath)) return false;
        }

        if ($bannerSectionThreeReceived) {
            $sectionThreeBannerPath = $this->uploadBanner($request->about_banner_section_three, self::BANNER_SECTION_THREE_KEY);
            if (empty($sectionThreeBannerPath)) return false;
        }

        if ($bannerSectionFiveReceived) {
            $sectionFiveBannerPath = $this->uploadBanner($request->about_banner_section_five, self::BANNER_SECTION_FIVE_KEY);
            if (empty($sectionFiveBannerPath)) return false;
        }

        $aboutUsCreatedOrUpdated = $this->createAboutUsRepository->updateOrCreate($request, $sectionOneBannerPath, $sectionThreeBannerPath, $sectionFiveBannerPath);

        $this->clearCacheAndSleep(self::SECONDS_TO_SLEEP);

        return (empty($aboutUsCreatedOrUpdated)) ? false : true;
    }

    private function uploadBanner($image, string $name)
    {
        $extension = $image->extension();

        $nameToSave = "$name.$extension";

        return $image->storeAs('about_us', $nameToSave);
    }
}
