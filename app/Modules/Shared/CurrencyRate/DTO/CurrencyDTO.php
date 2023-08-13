<?php
declare(strict_types=1);

namespace App\Modules\Shared\CurrencyRate\DTO;

/**
 * CurrencyDTO Data transfer object.
 */
class CurrencyDTO
{
    /**
     * @var float
     */
    public readonly float $value;

    /**
     * @var string
     */
    public readonly string $code;

    /**
     * @var string
     */
    public readonly string $nominal;

    /**
     * @var string
     */
    public readonly string $date;

    /**
     * @param float $value
     * @param string $code
     * @param string $nominal
     * @param string $date
     */
    public function __construct(float $value, string $code, string $nominal, string $date)
    {
        $this->value = $value;
        $this->code = $code;
        $this->nominal = $nominal;
        $this->date = $date;
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'value' => (string) $this->value,
            'code' => $this->code,
            'date' => $this->date,
        ];
    }
}
