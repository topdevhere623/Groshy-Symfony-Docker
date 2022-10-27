<?php

declare(strict_types=1);

namespace Groshy\Routing;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/** Adds to and from query parameters to selected routes */
class DateParamRouter implements RouterInterface, WarmableInterface
{
    private array $routes = [
        'groshy_frontend_assettype_assets',
        'groshy_frontend_dashboard_dashboard',
        'groshy_frontend_position_position',
    ];

    public function __construct(
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack
    ) {
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        if (!is_null($this->requestStack->getCurrentRequest()) && in_array($name, $this->routes)) {
            $parameters['from'] = $this->requestStack->getCurrentRequest()->query->get('from');
            $parameters['to'] = $this->requestStack->getCurrentRequest()->query->get('to');
        }

        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function setContext(RequestContext $context)
    {
        $this->router->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->router->getRouteCollection();
    }

    public function match(string $pathinfo): array
    {
        return $this->router->match($pathinfo);
    }

    public function warmUp(string $cacheDir)
    {
        return [];
    }
}
