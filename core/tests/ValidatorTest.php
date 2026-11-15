<?php
declare(strict_types=1);

use Core\I18n\Translator;
use Core\Validation\Validator;

return function (string $root): void {
    $tr = new Translator($root . '/core/lang', 'fa');
    $v = new Validator($tr);

    $errors = $v->validate(
        ['name' => '', 'qty' => 'abc', 'date' => '1403/13/01', 'status' => 'bad'],
        ['name' => 'required|string', 'qty' => 'int', 'date' => 'jdate', 'status' => 'enum:draft,posted']
    );
    assertTrue(isset($errors['name'], $errors['qty'], $errors['date'], $errors['status']), 'All invalid fields should error');

    $ok = $v->validate(
        ['name' => 'Steel', 'qty' => '10', 'date' => '1403/08/25', 'status' => 'posted'],
        ['name' => 'required|string', 'qty' => 'int|min:1', 'date' => 'jdate', 'status' => 'enum:draft,posted']
    );
    assertEquals([], $ok, 'Valid payload should have no errors');
};
