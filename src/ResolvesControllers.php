<?php

namespace Thermometer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Thermometer\Views\BackdropView;

trait ResolvesControllers
{

    protected $resolvers = [];

    public function bind($class, $resolver)
    {
        $this->resolvers[$class] = $resolver;
    }

    public function make($class)
    {

        if (array_key_exists($class, $this->resolvers)) {
            return $this->resolvers[$class]($this);
        }

        $reflector = new ReflectionClass($class);

        $constructorMethod = $reflector->getConstructor();
        $constructorParameters = $constructorMethod->getParameters();

        $args = array_map(fn ($parameter) => $this->parseParameter($parameter), $constructorParameters);

        return new $class(...$args);
    }

    protected function parseParameter(ReflectionParameter $parameter)
    {
        if (!$parameter->hasType() && !$parameter->isDefaultValueAvailable()) {
            throw new InvalidArgumentException("Controller constructor must not have typeless arguments!");
        } else if (!$parameter->hasType()) {
            return $parameter->getDefaultValue();
        }

        $typeParameter = $parameter->getType();
        if (!($typeParameter instanceof ReflectionNamedType)) {
            throw new InvalidArgumentException("This parameter should not be unnamed!");
        }

        $type = $typeParameter->getName();
        if ((new ReflectionClass($type))->isSubclassOf(BackdropView::class)) {
            return new $type($this->screen->getWidth(), $this->screen->getHeight());
        }

        return $this->make($type);
    }
}