<?php
namespace Leafcutter\Addons\Leafcutter\Media;

use Leafcutter\Addons\Leafcutter\Media\Media\AbstractMedia;
use Leafcutter\Addons\Leafcutter\Media\Media\DTubeVideo;
use Leafcutter\Addons\Leafcutter\Media\Media\ImageAssetMedia;
use Leafcutter\Addons\Leafcutter\Media\Media\MediaError;
use Leafcutter\Addons\Leafcutter\Media\Media\YouTubeVideo;
use Leafcutter\DOM\DOMEvent;
use Leafcutter\Images\ImageAsset;
use Leafcutter\Response;
use Leafcutter\URL;
use Symfony\Component\Yaml\Yaml;

class Addon extends \Leafcutter\Addons\AbstractAddon
{
    /**
     * Specify default config here. If it must include dynamic content, or
     * for some other reason can't be a constant, delete this constant and
     * override the method `getDefaultConfig()` instead.
     */
    const DEFAULT_CONFIG = [
        'max-height' => 80,
    ];

    /**
     * Check response content for <code> tags and inject CSS if
     * they are found
     */
    public function onResponsePageSet(Response $response)
    {
        if (strpos($response->content(), '<!--media-container-->') !== false) {
            $this->leafcutter->theme()->activate('library/media-embedding');
        }
    }

    public function onDOMElement_media(DOMEvent $event)
    {
        $media = trim($event->getNode()->textContent);
        $media = $this->parseMediaString($media);
        $event->setReplacement('<!--media-container-->' . $media);
    }

    public function parseMediaString(string $input): AbstractMedia
    {
        $input = preg_split('/[\r\n]+\-\-\-[\r\n]+/', $input);
        $media = $this->makeMediaFromString(array_shift($input)) ?? new MediaError('Media or handler not found');
        // get config
        if ($input) {
            $config = Yaml::parse($input[0]);
            $media->caption(@$config['caption']);
            $media->alt(@$config['alt']);
            $media->credit(@$config['credit']);
        }
        // build the media itself's output
        return $media;
    }

    /**
     * Make a Media object from Leafcutter Content (Assets/Pages)
     *
     * @param mixed $source
     * @return void
     */
    protected function makeMediaFromContent($source): ?AbstractMedia
    {
        return $this->leafcutter->events()->dispatchFirst(
            'onMediaContentSource', $source
        );
    }

    public function onMediaContentSource($source): ?AbstractMedia
    {
        if ($source instanceof ImageAsset) {
            return new ImageAssetMedia($source);
        }
        return null;
    }

    public function onMediaContentString(string $string): ?AbstractMedia
    {
        $url = new URL($string);
        switch ($url->host()) {
            case 'www.youtube.com':
                return new YouTubeVideo($url->query()['v']);
            case 'd.tube':
                return new DTubeVideo(preg_replace('@^\!/v/@', '', $url->fragment()));
        }
        return null;
    }

    /**
     * Make a Media object from a media spec string
     *
     * @param mixed $source
     * @return ?AbstractMedia
     */
    protected function makeMediaFromString(string $source): ?AbstractMedia
    {
        if ($media = $this->leafcutter->find($source)) {
            if ($media = $this->makeMediaFromContent($media)) {
                return $media;
            }
        }
        return $this->leafcutter->events()->dispatchFirst(
            'onMediaContentString', $source
        ) ?? null;
    }

    /**
     * Method is executed as the first step when this Addon is activated.
     *
     * @return void
     */
    public function activate(): void
    {
        $this->leafcutter->theme()->addDirectory(__DIR__ . '/../themes');
    }

    /**
     * Used after loading to give Leafcutter an array of event subscribers.
     * An easy way of rapidly developing simple Addons is to simply return [$this]
     * and put your event listener methods in this same single class.
     *
     * @return array
     */
    public function getEventSubscribers(): array
    {
        return [$this];
    }

    /**
     * Specify the names of the features this Addon provides. Some names may require
     * you to implement certain interfaces. Addon will also be available from
     * AddonProvider::get() by any names given here.
     *
     * @return array
     */
    public static function provides(): array
    {
        return ['media-embedding'];
    }

    /**
     * Specify an array of the names of features this Addon requires. Leafcutter
     * will attempt to automatically load the necessary Addons to provide these
     * features when this Addon is loaded.
     *
     * @return array
     */
    public static function requires(): array
    {
        return [];
    }

    /**
     * Return the canonical name of this plugin. Generally this should be the
     * same as the composer package name, so this example pulls it from your
     * composer.json automatically.
     *
     * @return string
     */
    public static function name(): string
    {
        if ($data = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true)) {
            return $data['name'];
        }
        return 'unknown/unknownaddon';
    }
}
