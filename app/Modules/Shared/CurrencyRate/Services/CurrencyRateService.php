<?php
declare(strict_types=1);

namespace App\Modules\Shared\CurrencyRate\Services;

use App\Modules\Shared\CurrencyRate\DTO\CurrencyDTO;
use App\Modules\Shared\CurrencyRate\Enums\CurrencyEnum;
use App\Modules\Shared\CurrencyRate\Enums\ProviderEnum;
use App\Modules\Shared\CurrencyRate\Exceptions\ClassNotFoundException;
use App\Modules\Shared\CurrencyRate\Providers\ProviderFactory;
use App\Repositories\CurrencyRepository;

/**
 * CurrencyRateService service class.
 */
class CurrencyRateService implements CurrencyRateServiceInterface
{
    /**
     * @var CurrencyEnum
     */
    private CurrencyEnum $defaultCurrency = CurrencyEnum::KGS;

    /**
     * @var CurrencyDTO
     */
    private static CurrencyDTO $currentRate;

    /**
     * @param CurrencyRepository $currencyRepository
     * @param ProviderEnum $defaultProvider
     */
    public function __construct(private readonly CurrencyRepository $currencyRepository, private ProviderEnum $defaultProvider = ProviderEnum::nbkr)
    {
    }

    /**
     * @param ProviderEnum $defaultProvider
     * @return void
     */
    public function setDefaultProvider(ProviderEnum $defaultProvider): void
    {
        $this->defaultProvider = $defaultProvider;
    }

    /**
     * @return void
     */
    public function syncToDb(): void
    {
        $rateDTO = self::$currentRate;

        if (!is_null($rateDTO)) {
            $this->currencyRepository->updateOrCreate([
                'code' => $rateDTO->code
            ], $rateDTO->toArray());
        }
    }

    /**
     * @param CurrencyEnum $currency
     * @return float
     * @throws ClassNotFoundException
     */
    public function getCurrentRate(CurrencyEnum $currency): float
    {

        if ($currency !== $this->defaultCurrency) {
            $provider = ProviderFactory::getProvider($this->defaultProvider);

            self::$currentRate = $provider->getRate($currency);
        } else {
            self::$currentRate = new CurrencyDTO(
                1,
                strtolower($currency->value),
                '1',
                now()->format('Y-m-d'),
            );
        }

        $this->syncToDb();

        return self::$currentRate->value;
    }
}
