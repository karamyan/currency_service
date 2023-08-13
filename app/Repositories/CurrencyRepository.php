<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Currency;

/**
 * CurrencyRepository class.
 */
class CurrencyRepository extends BaseRepository
{
    /**
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->model = $currency;
    }
}
