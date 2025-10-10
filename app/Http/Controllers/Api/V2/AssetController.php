<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssetStoreRequest;
use App\Http\Requests\AssetUpdateRequest;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    /**
     * GET /api/v2/assets
     * Soporta:
     *  - ?q=texto (busca en tag/serie/modelo/marca y brandRef)
     *  - ?status=in_stock|assigned|repair|retired
     *  - ?type_id=ID
     *  - ?per_page=30 (1..2000)
     *  - ?page=2 (manejada por Laravel)
     *  - ?paginate=0 (devuelve TODOS sin meta/links)
     */
    public function index(Request $r)
    {
        // Control de paginación
        $perPage  = (int) $r->query('per_page', 30);
        $perPage  = max(1, min($perPage, 2000));      // límites sanos
        $paginate = $r->boolean('paginate', true);    // ?paginate=0 desactiva paginación

        $rowsQuery = Asset::with(['type','brandRef','currentAssignment.user'])
            ->when($r->status, fn ($qq) => $qq->where('status', $r->status))
            ->when($r->type_id, fn ($qq) => $qq->where('type_id', $r->type_id))
            ->when($r->q, function ($qq) use ($r) {
                $q = trim($r->q);
                $qq->where(function ($w) use ($q) {
                    $w->where('asset_tag', 'like', "%{$q}%")
                      ->orWhere('serial_number', 'like', "%{$q}%")
                      ->orWhere('model', 'like', "%{$q}%")
                      ->orWhere('brand', 'like', "%{$q}%")
                      ->orWhere('phone_number','like',"%{$q}%")
                      ->orWhere('carrier','like',"%{$q}%")
                      ->orWhereHas('brandRef', fn($b) => $b->where('name', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('created_at');

        if (!$paginate) {
            // Sin paginar (para dashboards)
            $items = $rowsQuery->get();
            $items->transform(function ($asset) {
                $asset->depreciation = $this->calcDepreciationArray($asset);
                return $asset;
            });
            return response()->json($items);
        }

        // Paginado (respeta per_page/page)
        $rows = $rowsQuery->paginate($perPage);
        $rows->getCollection()->transform(function ($asset) {
            $asset->depreciation = $this->calcDepreciationArray($asset);
            return $asset;
        });

        return response()->json($rows);
    }

    /**
     * POST /api/v2/assets
     */
    public function store(AssetStoreRequest $req)
    {
        $data = $req->validated();

        // asset_tag autogenerado si no viene
        if (empty($data['asset_tag'])) {
            $next = (int) (optional(Asset::orderByDesc('id')->first())->id ?? 0) + 1;
            $data['asset_tag'] = 'IT-' . Str::padLeft((string) $next, 6, '0');
        }

        // subir factura si viene (PDF/JPG/PNG)
        if ($req->hasFile('invoice')) {
            $data['invoice_path'] = $req->file('invoice')->store('invoices', 'public');
        }

        $data['created_by'] = $req->user()->id;

        $asset = Asset::create($data);

        // incluir depreciación en la respuesta
        $asset->load(['type','brandRef']);
        $asset->depreciation = $this->calcDepreciationArray($asset);

        return response()->json($asset, 201);
    }

    /**
     * GET /api/v2/assets/{asset}
     */
    public function show(Asset $asset)
    {
        $asset->load(['type','brandRef','assignments.user','currentAssignment.user']);
        $asset->depreciation = $this->calcDepreciationArray($asset);
        return response()->json($asset);
    }

    /**
     * PUT /api/v2/assets/{asset}
     */
    public function update(AssetUpdateRequest $req, Asset $asset)
    {
        $data = $req->validated();

        // reemplazo de factura si se envía una nueva
        if ($req->hasFile('invoice')) {
            if ($asset->invoice_path) {
                Storage::disk('public')->delete($asset->invoice_path);
            }
            $data['invoice_path'] = $req->file('invoice')->store('invoices', 'public');
        }

        $asset->update($data);

        $asset->refresh()->load(['type','brandRef']);
        $asset->depreciation = $this->calcDepreciationArray($asset);

        return response()->json($asset);
    }

    /**
     * Depreciación lineal: 10% anual prorrateado por mes (sin valor de rescate).
     * Si falta costo o fecha, regresa factor 1 (sin depreciar).
     */
    private function calcDepreciationArray(Asset $a): array
    {
        $cost = (float) ($a->purchase_cost ?? 0);
        $date = $a->purchase_date ? $a->purchase_date->copy() : null;

        if ($cost <= 0 || !$date) {
            return [
                'years'   => 0,
                'months'  => 0,
                'rate'    => 0.10,
                'factor'  => 1.0,
                'current' => $cost,
            ];
        }

        $now    = now();
        $months = max(0, ($now->year - $date->year) * 12 + ($now->month - $date->month));
        $years  = $months / 12;
        $rate   = 0.10; // 10% anual
        $factor = max(0, 1 - ($rate * $years)); // sin valor de rescate

        return [
            'years'   => round($years, 2),
            'months'  => $months,
            'rate'    => $rate,
            'factor'  => round($factor, 4),
            'current' => round($cost * $factor, 2),
        ];
    }
}
