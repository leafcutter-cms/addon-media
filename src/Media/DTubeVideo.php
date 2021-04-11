<?php
namespace Leafcutter\Addons\Leafcutter\Media\Media;

class DTubeVideo extends AbstractMedia
{
    protected $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function srcHash(): string
    {
        return md5(get_called_class().$this->id);
    }

    public function aspectRatio(): float
    {
        return 9 / 16;
    }

    protected function html(): string
    {
        return '<iframe width="100%" height="100%" loading="lazy" src="https://emb.d.tube/#!/' . $this->id . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true"></iframe>';
    }

    public function classes(): array
    {
        return ['media-video', 'media-dtube'];
    }
}
