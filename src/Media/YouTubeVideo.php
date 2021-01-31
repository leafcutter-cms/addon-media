<?php
namespace Leafcutter\Addons\Leafcutter\Media\Media;

class YouTubeVideo extends AbstractMedia
{
    protected $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function aspectRatio(): float
    {
        return 9 / 16;
    }

    protected function html(): string
    {
        return '<iframe width="100%" height="100%" loading="lazy" src="https://www.youtube.com/embed/' . $this->id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true">test</iframe>';
    }

    public function classes(): array
    {
        return ['media-video', 'media-youtube'];
    }
}
