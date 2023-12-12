<?php

namespace artiden\exchange\providers\bin;

use artiden\exchange\providers\bin\BinProviderInterface;
use artiden\exchange\providers\bin\exceptions\BinDataUnavailableException;

class BinlistProvider implements BinProviderInterface {
    /**
     * @var string
     */
    private string $serviceUrl;

    /**
     * Class constructor
     */
    public function __construct() {
        // In general, we could make this class more general and pass an URL.
        // But for this example, as for me, it be OK as is.
        $this->serviceUrl = 'https://lookup.binlist.net/';
    }

    /**
     * {@inheritDoc}
     */
    public function getBinData(string $bin): object {
        $url = sprintf(
            '%s%s',
            $this->serviceUrl,
            $bin
        );

        $fileContent = file_get_contents($url);
        if (!$fileContent) {
            throw new BinDataUnavailableException('Unable to get URL content');
        }

        try {
            return json_decode($fileContent, null, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new BinDataUnavailableException($exception->getMessage());
        }
    }
}
