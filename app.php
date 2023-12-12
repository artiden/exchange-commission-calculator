<?php
require 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use artiden\exchange\CommissionCalculator;
use artiden\exchange\providers\bin\BinlistProvider;
use artiden\exchange\providers\transactions\TransactionsFileProvider;
use artiden\exchange\providers\rate\ExchangeRatesApiProvider;

$dotEnv = new Dotenv();
$dotEnv->loadEnv(__DIR__.'/.env.local');

$binProvider = new BinlistProvider();
$transactionsProvider = new TransactionsFileProvider($argv[1]);
$rateProvider = new ExchangeRatesApiProvider(
    $_ENV['EXCHANGE_RATES_API_URL'],
    $_ENV['EXCHANGE_API_KEY'],
    boolval($_ENV['EXCHANGE_SECURE'])
);

$commissionCalculator = new CommissionCalculator($transactionsProvider, $binProvider, $rateProvider);
$commissions = $commissionCalculator->getCommissions();
foreach ($commissions as $commission) {
    echo $commission . PHP_EOL;
}
