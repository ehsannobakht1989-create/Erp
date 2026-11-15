<?php
declare(strict_types=1);

namespace Modules\ItemMaster\Controllers;

use Core\Http\Request;
use Core\Http\Response;
use Modules\ItemMaster\Services\ItemMasterService;

final class ItemController
{
    public function __construct(private readonly ItemMasterService $service)
    {
    }

    public function index(Request $req): Response
    {
        $companyId = (int) ($req->attributes['company_id'] ?? 1);
        $items = $this->service->search($companyId, (string) $req->input('q', ''));
        return Response::ok($items);
    }

    public function store(Request $req): Response
    {
        $companyId = (int) ($req->attributes['company_id'] ?? 1);
        $actorId = isset($req->attributes['user']['id']) ? (int) $req->attributes['user']['id'] : null;
        $res = $this->service->create($companyId, $actorId, $req->body);
        if (!($res['ok'] ?? false)) {
            return Response::error('DUPLICATE_WARNING', 'Similar item exists', 409, $res);
        }
        return Response::ok($res['item'], [], 201);
    }
}
