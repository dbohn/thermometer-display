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
        $this->addString($this->sections[0]['value'] . ' ' . $this->sections[0]['unit'], 10, 52, 32);
        $this->addString($this->sections[0]['name'], 10, 64, 12);

        $this->addString($this->sections[1]['value'] . ' ' . $this->sections[0]['unit'], 10, 80 + 52, 32);
        $this->addString($this->sections[1]['name'], 10, 80 + 64, 12);
        // Refresh time
        $this->addString($this->date, $this->width / 2, 240 - 10, 10, 0, self::ANCHOR_CENTER);
    }
}
