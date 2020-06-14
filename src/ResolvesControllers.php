<?php

namespace Thermometer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionParameter;
use Thermometer\Views\BackdropView;

trait ResolvesControllers
{
    protected function initializeController($controller)
    {
        $reflector = new ReflectionClass($controller);

        $constructorMethod = $reflector->getConstructor();
        $constructorParameters = $constructorMethod->getParameters();

        $args = array_map(fn ($parameter) => $this->parseParameter($parameter), $constructorParameters);

        return new $controller(...$args);
    }

    protected function parseParameter(ReflectionParameter $parameter)
    {
        if (!$parameter->hasType() && !$parameter->isDefaultValueAvailable()) {
            throw new InvalidArgumentException("Controller constructor must not have typeless arguments!");
        } else if (!$parameter->hasType()) {
            return $parameter->getDefaultValue();
        }

        $type = $parameter->getType()->getName();

        if ((new ReflectionClass($type))->isSubclassOf(BackdropView::class)) {
            return new $type($this->screen->getWidth(), $this->screen->getHeight());
        }

        return new $type();
    }
}