<?php
declare(strict_types=1);

namespace App\Modules\Shared\CurrencyRate\Enums;

use App\Modules\Shared\CurrencyRate\Providers\Nbkr;
use Tests\Unit\CurrencyService\NbkrMock;

/**
 * List of currency providers.
 */
enum ProviderEnum: string
{
    case nbkr = Nbkr::class;
    case nbkr_test = NbkrMock::class;
}
