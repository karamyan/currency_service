<?php
declare(strict_types=1);

namespace App\Modules\Shared\CurrencyRate\Providers;

use App\Modules\Shared\CurrencyRate\DTO\CurrencyDTO;
use App\Modules\Shared\CurrencyRate\Enums\CurrencyEnum;
use App\Modules\Shared\CurrencyRate\Exceptions\CurrencyNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Nbkr currency provider class.
 */
class Nbkr implements ProviderInterface
{
    /**
     * @var array|string[]
     */
    private array $endpoints = [
        'daily' => '/XML/daily.xml',
        'weekly' => '/XML/weekly.xml'
    ];

    /**
     * @var string
     */
    private string $period = 'daily';

    /**
     * @var
     */
    public static $rates;

    /**
     * @param Collection $rates
     * @return void
     */
    public function setRates(Collection $rates): void
    {
        self::$rates = $rates;
    }

    /**
     * @param string $period
     * @return void
     */
    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    /**
     * @param array $configs
     */
    public function __construct(private readonly array $configs)
    {
    }

    /**
     * @throws RequestException
     */
    public function getRates(): Collection
    {
        $response = Http::get($this->configs['api_url'] . $this->endpoints[$this->period]);

        if ($response->status() != ResponseAlias::HTTP_OK) {
            $response->throw();
        }

        $responseBody = $response->body();
        $responseBody = $this->xmlToArray($responseBody);

        return $this->mappingData($responseBody);
    }

    /**
     * @throws RequestException
     * @throws CurrencyNotFoundException
     */
    public function getRate(CurrencyEnum $currency): CurrencyDTO
    {
        $data = self::$rates;

        if (is_null($data)) {
            $daily = $this->getRates();
            $data = $daily;

            if (!$daily->get(strtolower($currency->value))) {
                $this->setPeriod('weekly');
                $weekly = $this->getRates();

                $data = $weekly->merge($daily);
            }

            $this->setRates($data);
        }

        if (is_null($data->get(strtolower($currency->value)))) {
            throw new CurrencyNotFoundException($currency->value . '  => Rate not found');
        }

        return $data->get(strtolower($currency->value));
    }

    /**
     * Convert xml string to array.
     *
     * @param string $xmlstring
     * @return array
     */
    private function xmlToArray(string $xmlstring): array
    {
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json, true);
    }

    /**
     * Collect data to DTO object.
     *
     * @param $currencies
     * @return Collection
     */
    private function mappingData($currencies): Collection
    {
        $date =  Carbon::createFromFormat('d.m.Y',  $currencies['@attributes']['Date']);

        $data = collect();
        foreach ($currencies['Currency'] as $currency) {
            $nominal = $currency['Nominal'];
            $value = str_replace(",", ".", $currency['Value']);

            if ($nominal != 1) {
                $value =  bcdiv((string)$value, $nominal, 10);
            }

            $data->put(strtolower($currency['@attributes']['ISOCode']),
                new CurrencyDTO(
                    $this->convertToFloat($value),
                    strtolower($currency['@attributes']['ISOCode']),
                    $currency['Nominal'],
                    $date->format('Y-m-d'),
                )
            );
        }

        return $data;
    }

    /**
     * Convert string number to float.
     *
     * @param string|int|float $amount
     * @return float
     */
    private function convertToFloat(string|int|float $amount): float
    {
        $precision = 10;
        $floatValue = bcadd($amount, "0", $precision);

        return (float)$floatValue;
    }
}
