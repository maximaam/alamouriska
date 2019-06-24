<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Class ModelUtils
 * @package App\Utils
 */
class ModelUtils
{
    const ENTITY_DOMAIN = [
        'mots-algeriens' => 'word',
        'expressions-algeriennes' => 'expression',
        'proverbes-algeriens' => 'proverb',
        'blagues-algeriennes' => 'joke',
        'blogs-algeriens' => 'blog'
    ];

    /**
     * @param string $domain
     * @return string
     */
    public static function getEntityByDomain(string $domain): string
    {
        return \ucfirst(self::ENTITY_DOMAIN[$domain]);
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

    /**
     * @param $post
     * @return string
     * @throws \ReflectionException
     */
    public static function getDomainByPost($post): string
    {
        $domains = \array_flip(self::ENTITY_DOMAIN);
        $entity = PhpUtils::getClassName($post);

        return $domains[strtolower($entity)];
    }
}