<?php 

namespace Thermometer\Views;

class PortraitView extends BackdropView
{

    protected $rowOffset = 80;

    public function viewFile(): string
    {
        return 'views/build/status_portrait.GRAY';
    }

    public function annotate(): void
    {

        foreach ($this->sections as $index => $section) {
            $this->addString($section['value'] . ' ' . $section['unit'], 10, $index * $this->rowOffset + 52, 32);
            $this->addString($section['name'], 10, $index * $this->rowOffset + 64, 12);
        }

        // Refresh time
        $this->addString($this->date, $this->width / 2, 240 - 5, 10, 0, self::ANCHOR_CENTER);
    }
}
