<?php 

namespace Thermometer\Views;

class PortraitView extends BackdropView
{
    public function viewFile(): string
    {
        return 'views/build/status_portrait.GRAY';
    }

    public function annotate(): void
    {
        $this->addString(date("d.m.Y H:i"), 10, 240 - 14);
    }
}
