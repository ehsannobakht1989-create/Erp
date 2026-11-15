<?php
declare(strict_types=1);

use Core\Support\Money;

return function (): void {
    assertEquals('30.5000', Money::add('10.25', '20.25'));
    assertEquals('5.2500', Money::sub('10.50', '5.25'));
    assertEquals('25.0000', Money::mul('10', '2.5'));
    assertEquals('4.0000', Money::div('10', '2.5'));
    assertEquals('13', Money::round('12.50', 0, 'half_up'));
    assertEquals('12', Money::round('12.90', 0, 'floor'));
};
