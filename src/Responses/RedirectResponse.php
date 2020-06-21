<?php

namespace Thermometer\Responses;

class RedirectResponse
{

    protected $destination;

    public function __construct($destinationController)
    {
        $this->destination = $destinationController;
    }

    public function getDestination()
    {
        return $this->destination;
    }
}
