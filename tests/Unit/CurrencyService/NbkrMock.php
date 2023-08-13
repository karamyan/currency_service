<?php

namespace Tests\Unit\CurrencyService;

use App\Modules\Shared\CurrencyRate\DTO\CurrencyDTO;
use App\Modules\Shared\CurrencyRate\Enums\CurrencyEnum;
use App\Modules\Shared\CurrencyRate\Exceptions\CurrencyNotFoundException;
use App\Modules\Shared\CurrencyRate\Providers\ProviderInterface;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class NbkrMock implements ProviderInterface
{
    public function getRates(): Collection
    {
        return collect();
    }

    public function getRate(CurrencyEnum $currency): CurrencyDTO
    {
        $data = $this->getFakeRates();

        if (is_null($data->get(strtolower($currency->value)))) {
            throw new CurrencyNotFoundException($currency->value . '  => Rate not found');
        }

        return $data->get(strtolower($currency->value));
    }

    private function getFakeRates(): Collection
    {
        $rates = collect();

        $rates->put('kgs', new CurrencyDTO(
            1,
            'KGR',
            '1',
            now()->format('Y-m-d'),
        ));

        $rates->put('rub', new CurrencyDTO(
            1,
            'RUB',
            '1',
            now()->format('Y-m-d'),
        ));

        $rates->put('usd', new CurrencyDTO(
            880.20,
            'USD',
            '10',
            now()->format('Y-m-d'),
        ));

        return $rates;
    }
}
