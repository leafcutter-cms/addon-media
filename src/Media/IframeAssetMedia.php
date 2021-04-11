<?php
namespace Leafcutter\Addons\Leafcutter\Media\Media;

use HtmlObjectStrings\GenericTag;
use Leafcutter\Assets\AssetInterface;
use Leafcutter\Leafcutter;

class IframeAssetMedia extends AbstractMedia
{
    protected $source;

    public function __construct(AssetInterface $source)
    {
        $this->source = $source;
    }

    public function srcHash(): string
    {
        return $this->source->hash();
    }

    public function aspectRatio(): float
    {
        return 0;
    }

    public function color(): ?string
    {
        return "transparent";
    }

    public function html(): string
    {
        $iframe = new GenericTag();
        $iframe->tag = 'iframe';
        $iframe->attr('style', 'height:' . Leafcutter::get()->addon('media-embedding')->config('max-height') . 'vh;width:100%;');
        $iframe->attr('src', $this->source->publicUrl());
        return $iframe->string();
    }

    public function classes(): array
    {
        return ['media-iframe'];
    }
}
