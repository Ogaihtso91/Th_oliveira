<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/*
 * Robots SEO Meta Tag Options
 * Ref. https://developers.google.com/search/reference/robots_meta_tag?hl=pt-br
 */
final class SeoRobots extends Enum
{
	/* Não há restrições para a indexação ou a veiculação. Observação: essa diretiva é o valor padrão e não terá efeito se for listada explicitamente. */
    const All = 'all';
    /* Não exibir esta página e não exibir um link "Em cache" nos resultados da pesquisa. */
    const NoIndex = 'noindex';
    /* 	Não seguir os links nesta página. */
    const NoFollow = 'nofollow';
    /* 	Equivalente a noindex, nofollow. */
    const None = 'none';
    /* 	Não exibir um link "Em cache" nos resultados da pesquisa. */
    const NoArchive = 'noarchive';
    /* 	Não exibir um snippet de texto ou uma visualização de vídeo nos resultados da pesquisa para esta página. Uma miniatura estática (se disponível) continuará visível. */
    const NoSnippet = 'nosnippet';
    /*	Não oferecer uma tradução desta página nos resultados da pesquisa. */
    const NoTranslate = 'notranslate';
    /* 	Não indexar imagens nesta página. */
    const NoImageIndex = 'noimageindex';
    /* Não exibir esta página nos resultados da pesquisa após a data/hora especificada. A data/hora precisa ser especificada no formato RFC 850. */
    const UnavaliableAfter = 'unavailable_after';
}
