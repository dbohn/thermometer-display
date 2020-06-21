<?php

namespace Thermometer\Controllers;

use Thermometer\Responses\RedirectResponse;
use Thermometer\Views\SystemInfoView;

class SystemInfoController implements Controller
{

    protected SystemInfoView $view;

    public function __construct(SystemInfoView $view)
    {
        $this->view = $view;
    }

    public function tick()
    {

        $viewData = [
            'info' => [
                'Hostname' => gethostname(),
                'WiFi Network' => trim(`iwgetid -r`),
                'IPv4' => $this->parseIPv4Address(`hostname -I`),
            ],
        ];

        return $this->view->with($viewData);
    }

    protected function parseIPv4Address($ip)
    {
        $fragments = explode(" ", $ip);
        return $fragments[0];
    }

    public function onPrimaryPressed()
    {
        return new RedirectResponse(PortraitViewController::class);
    }

    public function onSecondaryPressed()
    {
        echo "Secondary Button pressed" . PHP_EOL;
    }

    public function onPreviousPressed()
    {
        echo "Previous Button pressed" . PHP_EOL;
    }

    public function onNextPressed()
    {
        echo "Next Button pressed" . PHP_EOL;
    }
}
