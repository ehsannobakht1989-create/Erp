<?php

declare(strict_types=1);

namespace App\Support\Values;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;
use JsonSerializable;
use OverflowException;

/** A DECIMAL(20,4) amount; currencies/rates remain editable company data. */
final readonly class Money implements JsonSerializable
{
    private BigDecimal $amount;

    public function __construct(string $amount, public string $currency)
    {
        if (! preg_match('/^[A-Z][A-Z0-9_]{1,11}$/D', $currency)) {
            throw new InvalidArgumentException('Invalid currency code.');
        }
        if (! preg_match('/^-?\d+(?:\.\d{1,4})?$/D', $amount)) {
            throw new InvalidArgumentException('Money requires a plain decimal string with at most four decimal places.');
        }
        $value = BigDecimal::of($amount)->toScale(4);
        if ($value->abs()->isGreaterThan('9999999999999999.9999')) {
            throw new OverflowException('Money exceeds DECIMAL(20,4).');
        }
        $this->amount = $value;
    }

    public function decimal(): string
    {
        return (string) $this->amount;
    }

    public function plus(self $other): self
    {
        $this->sameCurrency($other);

        return new self((string) $this->amount->plus($other->amount), $this->currency);
    }

    public function minus(self $other): self
    {
        $this->sameCurrency($other);

        return new self((string) $this->amount->minus($other->amount), $this->currency);
    }

    /** Rounding is mandatory at call sites, never implicit. */
    public function multipliedBy(string $factor, RoundingMode $rounding): self
    {
        if (! preg_match('/^-?\d+(?:\.\d+)?$/D', $factor)) {
            throw new InvalidArgumentException('Factor must be a plain decimal string.');
        }

        return new self((string) $this->amount->multipliedBy($factor)->toScale(4, $rounding), $this->currency);
    }

    public function compareTo(self $other): int
    {
        $this->sameCurrency($other);

        return $this->amount->compareTo($other->amount);
    }

    /** @return array{amount:string, currency:string} */
    public function jsonSerialize(): array
    {
        return ['amount' => $this->decimal(), 'currency' => $this->currency];
    }

    private function sameCurrency(self $other): void
    {
        if ($other->currency !== $this->currency) {
            throw new InvalidArgumentException('Cross-currency arithmetic requires an explicit rate conversion.');
        }
    }
}
