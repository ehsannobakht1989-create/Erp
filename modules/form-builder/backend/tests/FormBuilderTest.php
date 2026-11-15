<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\FormBuilder\FormBuilderService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['form-builder'], false);

    $svc = new FormBuilderService($db);
    $schema = ['fields' => [['key' => 'plate_no', 'required' => true]]];
    assertEquals(['plate_no' => 'فیلد الزامی است.'], $svc->validateSubmission($schema, []));
    $id = $svc->submit(1, 'gate-pass', 1, ['plate_no' => '12ب345'], 1);
    assertTrue($id !== '', 'Submission should return id');
};
