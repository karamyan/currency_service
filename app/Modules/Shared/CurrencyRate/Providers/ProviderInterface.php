<?php

namespace App\Modules\Shared\CurrencyRate\Providers;

use App\Modules\Shared\CurrencyRate\DTO\CurrencyDTO;
use App\Modules\Shared\CurrencyRate\Enums\CurrencyEnum;
use Illuminate\Support\Collection;

/**
 * ProviderInterface.
 */
interface ProviderInterface
{
    /**
     * @return Collection
     */
    public function getRates(): Collection;

    /**
     * @param CurrencyEnum $currency
     * @return CurrencyDTO
     */
    public function getRate(CurrencyEnum $currency): CurrencyDTO;
}
