<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Class ModelUtils
 * @package App\Utils
 */
class ModelUtils
{
    const DOMAINS = ['mots', 'expressions', 'proverbes', 'blagues'];
    const ENTITIES = ['mot' => 'word', 'expression' => 'expression', 'proverbe' => 'proverb', 'blague' => 'joke'];

    /**
     * @param string $domain
     * @return string
     */
    public static function getEntityByDomain(string $domain): string
    {
        $domain = \substr($domain, 0, -1);
        $entity = self::ENTITIES[$domain];

        return \ucfirst($entity);
    }
}