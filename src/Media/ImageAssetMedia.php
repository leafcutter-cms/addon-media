<?php
namespace Leafcutter\Addons\Leafcutter\Media\Media;

use HtmlObjectStrings\GenericTag;
use Leafcutter\Images\ImageAsset;
use Leafcutter\Leafcutter;

class ImageAssetMedia extends AbstractMedia
{
    protected $image;
    protected $color;
    protected $hash;

    public function __construct(ImageAsset $image)
    {
        $this->hash = $image->hash();
        $this->image = Leafcutter::get()->images()->get($image->url());
        $this->info = getimagesize($image->outputFile());
    }

    public function thumbnail(): string
    {
        return $this->image->preset('thumbnail')->publicUrl();
    }

    public function srcHash(): string
    {
        return $this->hash;
    }

    public function color(): ?string
    {
        if (!$this->color) {
            $image = $this->image->crop(1, 1);
            $im = imagecreatefromstring($image->content());
            $rgb = imagecolorat($im, 0, 0);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $this->color = "rgb($r,$g,$b)";
        }
        return $this->color;
    }

    public function height(): ?int
    {
        return $this->info[1];
    }

    public function width(): ?int
    {
        return $this->info[0];
    }

    public function aspectRatio(): float
    {
        return $this->height() / $this->width();
    }

    protected function html(): string
    {
        //generate srcset
        $width = $this->width();
        $srcset = [];
        while ($width >= 100) {
            $image = $this->image->fit($width * $this->aspectRatio(), $width);
            $srcset[] = $image->publicUrl() . ' ' . $width . 'w';
            $width -= 100;
        }
        //build img tag
        $img = new GenericTag();
        $img->tag = 'img';
        $img->selfClosing = true;
        $img->attr('src', $this->image->default()->publicUrl());
        $img->attr('style', 'width:100%;height:auto;');
        $img->attr('srcset', implode(',', $srcset));
        $img->attr('loading', 'lazy');
        if ($this->alt) {
            $img->attr('alt', $this->alt);
        }
        return $img->string();
    }

    public function classes(): array
    {
        return ['media-image'];
    }
}
