<?php
namespace Leafcutter\Addons\Leafcutter\Media\Media;

use Leafcutter\Leafcutter;

class GalleryMedia extends AbstractMedia
{
    protected $html;
    protected $hash;
    protected $json;

    public function __construct(array $content)
    {
        $this->html = implode(PHP_EOL, array_map(
            function (AbstractMedia $media) {
                return $media->__toString();
            },
            $content
        ));
        $this->hash = md5($this->html);
        $json = array_values(array_map(
            function (AbstractMedia $media) {
                return [
                    'thumb' => $media->thumbnail(),
                    'height' => $media->height(),
                    'width' => $media->width(),
                    'ratio' => $media->aspectRatio(),
                    'color' => $media->color(),
                    'alt' => $media->alt(),
                    'caption' => $media->caption(),
                    'credit' => $media->credit(),
                    'html' => $media->__toString()
                ];
            },
            $content
        ));
        $json = Leafcutter::get()->assets()->getFromString(json_encode($json), null, 'json');
        $this->json = $json->publicUrl();
        $this->html = 
            '<!-- theme_package:library/media-embedding/gallery -->'.
            '<noscript><![CDATA[' . $this->html . ']]></noscript>';
    }

    public function srcHash(): string
    {
        return md5(get_called_class().$this->message);
    }

    public function height(): ?int
    {
        return null;
    }

    public function width(): ?int
    {
        return null;
    }

    public function aspectRatio(): float
    {
        return 0;
    }

    protected function html(): string
    {
        return $this->html;
    }

    public function __toString()
    {
        return '<div class="media-gallery" data-gallery-json="'.$this->json.'"><div class="gallery-content">'.parent::__toString().'</div></div>';
    }

    public function classes(): array{
        return ['media-gallery-wrapper'];
    }
}
