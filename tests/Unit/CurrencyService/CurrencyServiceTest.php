<?php
declare(strict_types=1);

namespace Tests\Unit\CurrencyService;

use App\Modules\Shared\CurrencyRate\Enums\CurrencyEnum;
use App\Modules\Shared\CurrencyRate\Enums\ProviderEnum;
use App\Modules\Shared\CurrencyRate\Exceptions\ClassNotFoundException;
use App\Modules\Shared\CurrencyRate\Exceptions\CurrencyNotFoundException;
use App\Modules\Shared\CurrencyRate\Services\CurrencyRateService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;
use Throwable;


class CurrencyServiceTest extends TestCase
{
    use CreatesApplication;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->app = $this->createApplication();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_is_valid_response(): void
    {
        $currencyRateService = app(CurrencyRateService::class);
        $currencyRateService->setDefaultProvider(ProviderEnum::nbkr_test);
        $result = $currencyRateService->getCurrentRate(CurrencyEnum::KGS);

        if (!is_float($result)) {
            $this->fail();
        }

        $this->assertTrue(true);
    }

    /**
     * @throws ClassNotFoundException
     * @throws Exception
     */
    public function test_is_valid_response_when_unknown_currency(): void
    {
        $currencyRateService = app(CurrencyRateService::class);
        $currencyRateService->setDefaultProvider(ProviderEnum::nbkr_test);

        try {
            $currencyRateService->getCurrentRate(CurrencyEnum::KGHS);
        } catch (CurrencyNotFoundException) {
            $this->assertTrue(true);
        } catch (Throwable) {
            $this->fail();
        }
    }
}
