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
        $this->addString("Hallo Welt", 10, 240 - 14);
    }
}
