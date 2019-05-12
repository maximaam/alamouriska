<?php


namespace App\Utils;


class ModelUtils
{

    const DOMAINS = ['mots', 'locutions', 'proverbes', 'citations'];

    /**
     * @param string $domain
     * @return string
     */
    public static function getEntityByDomain(string $domain): string
    {
        return \substr(\ucfirst($domain), 0, -1);
    }

}