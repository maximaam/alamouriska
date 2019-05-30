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
    const ENTITY_DOMAIN = ['mots' => 'word', 'expressions' => 'expression', 'proverbes' => 'proverb', 'blagues' => 'joke'];

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

    /**
     * @param string $entity
     * @return string
     */
    public static function getDomainByEntity(string $entity): string
    {
        $domains = \array_flip(self::ENTITY_DOMAIN);

        return $domains[$entity];
    }
}