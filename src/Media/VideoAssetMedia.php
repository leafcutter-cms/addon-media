<?php
namespace Leafcutter\Addons\Leafcutter\Media\Media;

use HtmlObjectStrings\GenericTag;
use Leafcutter\Assets\AssetInterface;

class VideoAssetMedia extends AbstractMedia
{
    protected $source;

    public function __construct(AssetInterface $source)
    {
        $this->source = $source;
    }

    public function aspectRatio(): float
    {
        return 9 / 16;
    }

    public function color(): ?string
    {
        return "#000";
    }

    public function html(): string
    {
        $video = new GenericTag();
        $video->tag = 'video';
        $video->attr('style', 'height:100%;width:100%;');
        $video->attr('controls', true);
        $video->attr('class', 'video-js vjs-leafcutter-theme');
        $video->attr('id', 'video-' . $this->source->hash());
        $video->attr('preload', 'auto');
        $video->attr('data-setup', '{}');
        // add primary source
        $video->content .= '<source src="' . $this->source->publicUrl() . '" type="' . $this->source->mime() . '" />';
        return '<!-- theme_package:library/videojs -->' . $video->string();
    }

    public function classes(): array
    {
        return ['media-video', 'media-local-video'];
    }
}
