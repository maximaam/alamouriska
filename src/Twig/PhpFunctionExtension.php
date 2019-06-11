<?php

declare(strict_types=1);

namespace App\Twig;

use App\Utils\ModelUtils;
use App\Utils\PhpUtils;
use Symfony\Component\HttpFoundation\HeaderBag;
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
            new TwigFunction('is_mobile', [$this, 'isMobile']),
        ];
    }

    /**
     * @param string $class
     * @return string
     * @throws \ReflectionException
     */
    public function domainByEntity(string $class): string
    {
        return ModelUtils::getDomainByEntity(\strtolower(PhpUtils::getClassName($class)));
    }

    /**
     * @param HeaderBag $headers
     * @return bool
     */
    public function isMobile(HeaderBag $headers): bool
    {
        $needles = ['mobile', 'iphone', 'android', 'windows phone'];
        $userAgent = \strtolower($headers->get('User-Agent'));

        foreach ($needles as $needle) {
            if (\strpos($userAgent, $needle)) {
                return true;
            }
        }

        return false;
    }
}
