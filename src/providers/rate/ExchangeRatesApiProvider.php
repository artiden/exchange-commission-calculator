<?php

namespace artiden\exchange\providers\rate;

use artiden\exchange\providers\rate\exceptions\RatesUnavailableException;
use artiden\exchange\providers\rate\exceptions\UnsupportedCurrencyException;
use artiden\exchange\providers\rate\RateProviderInterface;

class ExchangeRatesApiProvider implements RateProviderInterface {
    /**
     * @var string
     */
    private string $scheme;

    public function __construct(
        private string $apiUri,
        private string $accessKey,
        private bool $httpsSupported = false
    ) {
        $this->scheme = 'https';
        if (!$this->httpsSupported) {
            $this->scheme = 'http';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRate(string $currency): float {
        $currency = mb_strtoupper($currency);
        $ratesUrl = $this->buildUrl();
        if (!$ratesData = file_get_contents($ratesUrl)) {
            throw new RatesUnavailableException('Cannot get data from API');
        }

        try {
            $rates = json_decode($ratesData, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RatesUnavailableException($exception->getMessage());
        }

        // If we have some errors in the API response, it means we can't return rates
        if (isset($rates['success']) && !$rates['success']) {
            throw new RatesUnavailableException($rates['error']['info']);
        }

        if (!isset($rates['rates']) || !array_key_exists($currency, $rates['rates'])) {
            throw new UnsupportedCurrencyException(sprintf(
                'Unsupported currency: %s',
                $currency
            ));
        }

        return floatval($rates['rates'][$currency]);
    }

    /**
     * Building an URL to get currency rate
     *
     * @return string
     */
    private function buildUrl(): string {
        return sprintf(
            '%s://%s/latest?access_key=%s',
            $this->scheme,
            $this->apiUri,
            $this->accessKey,
        );
    }
}