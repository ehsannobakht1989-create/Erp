<?php
declare(strict_types=1);

namespace Modules\PartyMaster\Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Modules\PartyMaster\Services\PartyMasterService;

final class PartyController
{
    public function __construct(private readonly PartyMasterService $service)
    {
    }

    public function index(Request $req): Response
    {
        $companyId = (int) ($req->attributes['company_id'] ?? 1);
        return Response::ok($this->service->lookup($companyId, ['q' => (string) $req->input('q', '')]));
    }

    public function store(Request $req): Response
    {
        $companyId = (int) ($req->attributes['company_id'] ?? 1);
        $actorId = isset($req->attributes['user']['id']) ? (int) $req->attributes['user']['id'] : null;
        return Response::ok($this->service->create($companyId, $actorId, $req->body), [], 201);
    }
}
