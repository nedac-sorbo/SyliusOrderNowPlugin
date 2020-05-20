<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NedacSyliusOrderNowPlugin extends Bundle
{
    use SyliusPluginTrait;
}
