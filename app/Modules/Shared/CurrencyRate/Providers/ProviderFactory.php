<?php
declare(strict_types=1);

namespace App\Modules\Shared\CurrencyRate\Providers;

use App\Modules\Shared\CurrencyRate\Enums\ProviderEnum;
use App\Modules\Shared\CurrencyRate\Exceptions\ClassNotFoundException;

class ProviderFactory
{
    /**
     * @param ProviderEnum $providerEnum
     * @return ProviderInterface
     * @throws ClassNotFoundException
     */
    public static function getProvider(ProviderEnum $providerEnum): ProviderInterface
    {
        $className = $providerEnum->value;

        if (!class_exists($className)) {
            throw new ClassNotFoundException("Class with namespace: $providerEnum->value does not found.");
        }

        $config = config('currency.providers.' . $providerEnum->name);

        return new $className($config);
    }
}
