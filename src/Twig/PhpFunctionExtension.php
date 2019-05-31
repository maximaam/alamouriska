<?php

declare(strict_types=1);

namespace App\Twig;

use App\Utils\ModelUtils;
use App\Utils\PhpUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class PhpFunctionExtension
 *
 * Expose some php api functions to twig templates
 *
 * @package App\Twig
 */
class PhpFunctionExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('domain_by_entity', [$this, 'domainByEntity']),
        ];
    }

    /**
     * @param string $class
     * @return string
     * @throws \ReflectionException
     */
    public function domainByEntity(string $class): string
    {
        $entity = PhpUtils::getClassName($class);

        return ModelUtils::getDomainByEntity(\strtolower($entity));
    }
}
