<?php

namespace App\Repositories;

class QuoteRepository
{

    private $fileRepository;
    private $routes;
    private $from;
    private $to;

    /**
     * QuoteRepository constructor.
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->routes = $this->fileRepository->read();
    }

    public function calculate(string $from, string $to)
    {
        $this->from = strtoupper($from);
        $this->to = strtoupper($to);
        $fromRoutes = $this->getRoutes('from');
        $toRoutes = $this->getRoutes('to');

        // Verifica se existe rota direta
        $directlyRoute = $this->getDirectlyRoute($fromRoutes);

        // verifica se existe alguma parada
        $stopsRoutes = $this->getStopsRoutes($fromRoutes, $toRoutes);

        if ($stopsRoutes) {
            if ($directlyRoute['price'] > $stopsRoutes['price']) {
                return $this->createRoutePrint($stopsRoutes);
            }
            return $this->createRoutePrint($directlyRoute);
        }

        return $this->createRoutePrint($directlyRoute);
    }

    private function getRoutes(string $type)
    {
        $routes = [];
        foreach ($this->routes as $route) {
            if ($route[$type] === $this->{$type}) {
                $routes[] = $route;
            }
        }

        return $routes;
    }

    private function getDirectlyRoute(array $routes)
    {
        $directlyRoute = [];
        foreach ($routes as $route) {
            if ($route['from'] === $this->from && $route['to'] === $this->to) {
                $directlyRoute[] = $route;
            }
        }

        return $this->getLowerstPrice($directlyRoute);
    }

    private function getLowerstPrice(array $array)
    {
        $lowest = null;
        foreach ($array as $value) {
            if (!$lowest) {
                $lowest = $value;
                continue;
            }

            if ($value['price'] < $lowest['price']) {
                $lowest = $value;
            }
        }

        return $lowest;
    }

    private function getStopsRoutes(array $fromRoutes, array $toRoutes)
    {
        $stops = null;
        foreach ($fromRoutes as $fromRoute) {
            foreach ($toRoutes as $toRoute) {
                if ($fromRoute['to'] === $toRoute['from']) {
                    $stops = $this->mergeStops($fromRoute, $toRoute);
                }
            }
        }

        return $stops;
    }

    private function mergeStops($fromRoute, $toRoute)
    {
        return [
            'from' => $fromRoute['from'],
            'stop' => $fromRoute['to'],
            'to' => $toRoute['to'],
            'price' => $fromRoute['price'] + $toRoute['price']
        ];
    }

    private function createRoutePrint($route)
    {
        $ret = [
            'route' => '',
            'price' => ''
        ];

        $ret['price'] = $route['price'];
        unset($route['price']);
        $ret['route'] = join(',', array_values($route));

        return $ret;
    }

}
