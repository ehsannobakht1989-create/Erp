<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Values\Money;
use Brick\Math\RoundingMode;

function moneyCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function moneyReject(callable $operation, string $exception): void
{
    try {
        $operation();
    } catch (Throwable $e) {
        moneyCheck($e instanceof $exception, 'Wrong exception: '.get_class($e));
        return;
    }
    throw new RuntimeException('Invalid money operation accepted');
}
$a = new Money('0.1', 'IRR');
$b = new Money('0.2', 'IRR');
moneyCheck($a->plus($b)->decimal() === '0.3000', 'Addition must be exact');
moneyCheck($a->decimal() === '0.1000', 'Money must remain immutable');
moneyCheck($a->minus($b)->decimal() === '-0.1000', 'Subtraction must preserve sign');
moneyCheck((new Money('-0.0000', 'IRR'))->decimal() === '0.0000', 'Zero must be canonical');
moneyCheck((new Money('000012.3', 'IRR'))->decimal() === '12.3000', 'Leading zeros must normalize');
moneyCheck((new Money('9999999999999999.9999', 'IRR'))->decimal() === '9999999999999999.9999', 'Boundary must be exact');
moneyCheck($a->multipliedBy('0.0005', RoundingMode::HalfUp)->decimal() === '0.0001', 'Rounding must be explicit');
moneyCheck($a->multipliedBy('0.0005', RoundingMode::HalfEven)->decimal() === '0.0000', 'Rounding policy must apply');
moneyCheck($a->compareTo($b) < 0, 'Comparison must use decimal arithmetic');
moneyCheck(json_encode($a) === '{"amount":"0.1000","currency":"IRR"}', 'JSON must not contain float amounts');
foreach (['1e3', 'NaN', '1,000', ' 1', '1.00001', '.1', '+1', ''] as $value) {
    moneyReject(fn () => new Money($value, 'IRR'), InvalidArgumentException::class);
}
moneyReject(fn () => new Money('1', 'irr'), InvalidArgumentException::class);
moneyReject(fn () => new Money('10000000000000000', 'IRR'), OverflowException::class);
moneyReject(fn () => (new Money('9999999999999999.9999', 'IRR'))->plus(new Money('0.0001', 'IRR')), OverflowException::class);
moneyReject(fn () => $a->plus(new Money('1', 'USD')), InvalidArgumentException::class);
moneyReject(fn () => $a->compareTo(new Money('1', 'USD')), InvalidArgumentException::class);
moneyReject(fn () => $a->multipliedBy('1e3', RoundingMode::Down), InvalidArgumentException::class);
echo "Exact money tests passed.\n";
