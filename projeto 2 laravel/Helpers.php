<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Helpers
{
    /**
     * Mask some value
     * @param Mixed $val
     * @param String $mask
     * @return String maskared value
     */
	public static function mask($val, $mask) {
		$maskared = '';
		$k = 0;

		for($i = 0; $i<=strlen($mask)-1; $i++) {
			if($mask[$i] == '#') {
				if(isset($val[$k]))
					$maskared .= $val[$k++];
			} else {
				if(isset($mask[$i]))
					$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}

	/**
     * Call Youtube API to retrieve video information
     * @param String $video_url
     * @return Json video information
     */
	public static function retrieve_youtube_information(String $video_url) {

		$request_url = "https://www.youtube.com/oembed?url={$video_url}&format=json";

		$client = new \GuzzleHttp\Client;

		try {
			$response = $client->get($request_url);
		} catch (\Exception $e) {
			return null;
		}

		// You need to parse the response body
		// This will parse it into an array
		return json_decode($response->getBody(), true);
	}

	/**
     * Summarize some string when it's lenght is more than specific value
     * @param String $string
     * @param Integer $length
     * @return String
     */
	public static function summarize_string(String $string, Int $length) {

		if (strlen($string) > ($length + 3)) {
			$string = substr($string, 0, $length)."...";
		}

		return $string;
	}

	/**
     * Return elapsed time of specific date
     * @param DateTime $time
     * @return String
     */
	public static function time_past($time) {

		$now = strtotime(date('m/d/Y H:i:s'));
		$time = strtotime($time);
		$diff = $now - $time;

		$seconds = $diff;
		$minutes = round($diff / 60);
		$hours = round($diff / 3600);
		$days = round($diff / 86400);
		$weeks = round($diff / 604800);
		$months = round($diff / 2419200);
		$years = round($diff / 29030400);

		if ($seconds <= 60) return "1 min atrás";
		else if ($minutes <= 60) return $minutes==1 ? '1 min atrás':$minutes.' min atrás';
		else if ($hours <= 24) return $hours==1 ? '1 hrs atrás':$hours.' hrs atrás';
		else if ($days <= 7) return $days==1 ? '1 dia atras':$days.' dias atrás';
		else if ($weeks <= 4) return $weeks==1 ? '1 semana atrás':$weeks.' semanas atrás';
		else if ($months <= 12) return $months == 1 ? '1 mês atrás':$months.' meses atrás';
		else return $years == 1 ? 'um ano atrás':$years.' anos atrás';
	}

	/**
     * Format Date
     * @param String $string
     * @return String
     */
	public static function format_date($string) {

		$aux_date = explode("/", $string);

		if (count($aux_date) < 3) return null;
		return $aux_date[2]."/".$aux_date[1]."/".$aux_date[0];
	}

	/**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @param  string|null  $language
     * @return string
     */
    public static function slug($title, $separator = '-', $language = 'en')
    {
        $title = $language ? Str::ascii($title, $language) : $title;

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace @ with the word 'at'
        $title = str_replace('@', 'a', $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

	/**
     * Generate Unique Friendly Url for Modules Items
     * @param Array $data
     * @param Illuminate\Database\Eloquent\Model $model_obj
     * @return String
     */
	public static function generate_unique_friendly_url($data, $model_obj) {

        $check_seo_url = $data['seo_url'];
        $count = 0;
        do {

            // Altera o nome se encontrou alguma
            if($count > 0) $check_seo_url = $data['seo_url']."-".$count;

            // Cria objeto para fazer a busca no banco
            $valida_friendly_url = $model_obj::where('seo_url', $check_seo_url);

            // Se está editando, exclui da consulta este objeto
            if (!empty($data['id'])) {
                $valida_friendly_url->where('id', '!=', $data['id']);
            }

            // Contador para concatenar ao nome
            $count++;

        } while (!empty($valida_friendly_url->withTrashed()->first()->id));

        // Atualiza o SEO Url com o valor de acordo com a validação
        return $check_seo_url;
	}

	/**
     * Create File Name Without Accents
     * @param String $string
     * @return String
     */
	public static function create_file_name_from_existing_name($string) {

        // Trata acentos
        $chars_Accent = self::get_accents_chars();
    	$chars_no_Accent = self::get_no_accents_chars();

        return str_replace($chars_Accent, $chars_no_Accent, $string);
	}

    /**
     * Make Full Text Search Where
     * @param $query
     * @param $table
     * @param $columns
     * @param $key_join
     * @param $terms
     * @param bool $limit
     * @param bool $exact_match
     * @return String
     */
    public static function make_fulltext_where(&$query, $table, $columns, $key_join, $terms, $limit = false, $exact_match = false) {

        // Remove caracteres especiais e espaços duplicados
        if (is_array($terms)) {
            $searchValues = $terms;
        } else {
            $searchValues = preg_replace('/[^\s\p{L}\d]/u', '', $terms);
            $searchValues = preg_split('/\s+/', $searchValues, -1, PREG_SPLIT_NO_EMPTY);
        }

        // Verifica se tem alguma palavra relevante para busca
        if (count($searchValues) <= 0) return false;

        // Monta a tabela de rankeamento
        $whereFullText = "CONTAINSTABLE(
                            {$table},
                            ({$columns}),";

        // Verifica se vai buscar pelos termos exatos ou pelo valor aproximado
        if ($exact_match) {

            // Adiciona aspas nos termos
            $searchValues = array_map(function($value) {
                return "\"{$value}\"";
            }, $searchValues);

            // Finaliza cláusula de join com os termos
            $whereFullText .= "'ISABOUT(".implode(",", $searchValues).")') AS search_rank";

        } else {

            // Percorre as palavras para montar a condição
            $whereClauses = [];
            foreach ($searchValues as $keyword) {
                if (strlen($keyword) >= 3) {
                    $whereClauses[] = "\"{$keyword}*\" OR FORMSOF(INFLECTIONAL, \"{$keyword}\")";
                }
            }

            // Finaliza cláusula de join
            $whereFullText .= "'".implode(" OR ", $whereClauses)."'".($limit ? ",".$limit : '').") AS search_rank";
        }


        // Adiciona ao objeto o join e os valores dos parâmetros
        $query->join(DB::raw($whereFullText), $key_join, '=', 'search_rank.key');

        $query->orderBy('search_rank.rank', 'DESC');

    }

    /**
     * Get User IP
     * @return String
     */
    public static function get_user_ip(){
        foreach (array(
                    'HTTP_CLIENT_IP',
                    'HTTP_X_FORWARDED_FOR',
                    'HTTP_X_FORWARDED',
                    'HTTP_X_CLUSTER_CLIENT_IP',
                    'HTTP_FORWARDED_FOR',
                    'HTTP_FORWARDED',
                    'REMOTE_ADDR'
                ) as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false){
                        return $ip;
                    }
                }
            }
        }
        return '';
    }

    /**
     * Retrieve Youtube Home Gallery Videos
     * @return array
     */
    public static function get_home_gallery_videos() {
        return [
            [ 'title' => 'Você sabe o que é tecnologia social?',                                                'id' => 'TTl9mpKupew' ],
            [ 'title' => 'Tecnologia Social Dessanilizador Solar',                                              'id' => 'r__2c8ZvT0o' ],
            [ 'title' => 'Tecnologia Social Escola Nacional Florestan Fernandes',                               'id' => 'yrY_a14wkbk' ],
            [ 'title' => 'Tecnologia Social Um Litro de Luz - Poste de Luz Solar',                              'id' => 's0-e_RgPn3k' ],
            [ 'title' => 'Tecnologia Social Comunidade que Sustenta Agricultura - CSA',                         'id' => 'CNnzdb952eA' ],
            [ 'title' => 'Tecnologia Social Rede de Agroecologia Povos da Mata',                                'id' => 'XxV0nja5svA' ],
            [ 'title' => 'Tecnologia Social Fast Food da Política',                                             'id' => 'U2Ilhx6Tiww' ],
            [ 'title' => 'Tecnologia Social Sistemas Agroflorestais para Composição de Reserva Legal - SAFs',   'id' => 'Tg-X_oLa_1U' ],
            [ 'title' => 'Tecnologia Social Rede Bodega',                                                       'id' => 'ADvDffmcdPU' ],
            [ 'title' => 'Tecnologia Social Águas de Valor e Sabor do Semiárido',                               'id' => 'V6rWDzDcjsM' ],
            [ 'title' => 'Tecnologia Social Caminos de la Villa - Argentina',                                   'id' => 'ieHkBx1TgXM' ],
            [ 'title' => 'Tecnologia Social Escuelas Sostenibles - El Salvador',                                'id' => 'sP1_XUxlQUM' ],
            [ 'title' => 'Tecnologia Social Arte na Palha Crioula: Banco de Milhos Crioulos',                   'id' => '1Y4Aasm0LEQ' ],
            [ 'title' => 'Tecnologia Social Tecnologia Assistiva de Baixo Custo para Pessoas com Deficiência',  'id' => 'c3blYKoX82A' ],
            [ 'title' => 'Tecnologia Social Escola de Comunicação',                                             'id' => 'm12L1GsiK7Q' ],
            [ 'title' => 'Tecnologia Social Ciclorrotas',                                                       'id' => 'fWqyVzdNPgA' ],
            [ 'title' => 'Tecnologia Social De Olho na Água',                                                   'id' => '84YJrg7vJ2w' ],
            [ 'title' => 'Tecnologia Social Noosfero',                                                          'id' => 'hWKABGrHIDE' ],
            [ 'title' => 'Tecnologia Social Uma Sintonia Diferente',                                            'id' => 'THpLNIVe1Q0' ],
            [ 'title' => 'Tecnologia Social Grupo nÓs',                                                         'id' => 'CT72hF7LntQ' ],
            [ 'title' => 'Tecnologia Social Banco União Sampaio',                                               'id' => 'JFYEPYSylVg' ],
            [ 'title' => 'Tecnologia Social Mi Huerta - Argentina',                                             'id' => '8_1ZVMWWySc' ],
        ];
    }

	/**
     * Get accents chars in order to replace to non accents
     * @return array
     */
	protected static function get_accents_chars() {
		return array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	}

	/**
     * Get non accents chars in order to replace
     * @return array
     */
	protected static function get_no_accents_chars() {
		return array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	}

    /**
     * Temp Function
     * @return Boolean
     */
    public static function check_notification_destiny($email) {
        return in_array($email, [
            'edvardenz@fbb.org.br',
            'mello80@gmail.com',
            'luiz.mello@fbb.org.br',
            'samuel.vieira@fbb.org.br',
            'samuel@fbb.org.br',
            'fabricioaraujo@fbb.org.br',
        ]);
    }

    public static function generete_registration_code() {
        // gera numero ramdom
        $digits = mt_rand(100000, 999999);
        // concatena com o ano
        $registration_code = date('Y').''.str_pad($digits,6, "0", STR_PAD_LEFT);

        return $registration_code;
    }
}
?>
