<?php 

namespace Thermometer\Views;

class SystemInfoView extends BackdropView
{

    protected $rowOffset = 80;

    public function viewFile(): string
    {
        return 'views/build/systeminfo_portrait.GRAY';
    }

    public function annotate(): void
    {
        $text = implode("\n\n", array_map(fn ($value, $key) => "{$key}:\n{$value}", $this->info, array_keys($this->info)));
        $this->addString($text, 10, 20, 14);
    }
}
