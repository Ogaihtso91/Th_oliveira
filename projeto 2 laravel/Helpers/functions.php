<?php

use App\ContentManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

function create_error_log($exception, $request, $message = "Erro"): void
{
    Log::error($message, [
        'code' => $exception->getCode(),
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'full_url' => $request->fullUrl(),
        'ip' => $request->ip(),
    ]);
}

function AwardsActive($award_id)
    {
        // Verifica se o premio esta dentro da vigencia de cadastro
        $hoje = date('Y-m-d');
        return !empty(App\Award::where('registrationsEndDate', '>=', $hoje)->where('id', '=', $award_id)->first()) ?  true : false;
    }

function date_greater_than_or_equal_to_today($date): bool
{
    if ($date >= date('Y-m-d')) return true;

    return false;
}

function generate_registration_code() : string
{
    // gera numero ramdom
    $digits = mt_rand(100000, 999999);
    // concatena com o ano
    $registration_code = date('Y').''.str_pad($digits,6, "0", STR_PAD_LEFT);

    return $registration_code;
}

function generate_unique_registration_code() : string
{
    $registration_code = generate_registration_code();

    while(ContentManager::query()->where('registration', $registration_code)->first() != null)
    {
        $registration_code = generate_registration_code();
    }

    return $registration_code;
}

function ds(): string
{
    return DIRECTORY_SEPARATOR;
}

function artisan_config_clear(): void
{
    Artisan::call('config:clear');
}

function artisan_config_cache(): void
{
    Artisan::call('config:cache');
}

function status_error(): string
{
    return 'error';
}

function status_message(): string
{
    return 'message';
}

function error_message_uploading_image(): string
{
    return 'Falha ao fazer upload da imagem';
}

function percentage_in_relation_to_the_total(int $value, int $total)
{
    return ($value * 100) / $total;
}

function youtube_information(String $url)
{
    $request_url = "https://www.youtube.com/oembed?url={$url}&format=json";

    $client = new \GuzzleHttp\Client;

    try {
        $response = $client->get($request_url);
    } catch (\Exception $e) {
        return null;
    }

    return json_decode($response->getBody(), true);
}

/**
 * @param string $url
 * @param object $model
 * @return string
 */
function generate_unique_url($url, $model)
{
    $attemptUrl = str_slug($url, '-');
    $attemptCount = 0;

    do {
        if($attemptCount > 0) $attemptUrl = str_slug($url, '-') ."-". $attemptCount;

        $result = $model::where('seo_url', $attemptUrl);

        $attemptCount++;

    } while (!empty($result->withTrashed()->first()->id));

    return $attemptUrl;
}

function get_state_by_acronym(string $acronym)
{
    if (strlen($acronym) != 2) return null;

    $states = get_states();

    try {
        $state = $states[$acronym];

        return $state;
    } catch (\Throwable $throwable) {
        Log::debug("Error helper get_state_by_acronym", [
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
        ]);
    }

    return null;
}

function get_states(): array
{
    return [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    ];
}
