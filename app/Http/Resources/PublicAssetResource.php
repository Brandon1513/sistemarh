<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicAssetResource extends JsonResource
{
    public function toArray($request)
    {
        // Depreciación 10% anual prorrateada por mes
        [$deprYears, $deprCurrent] = $this->computeDepreciation(
            $this->purchase_cost,
            $this->purchase_date
        );

        return [
            'id'           => $this->id,
            'asset_tag'    => $this->asset_tag,

            'type_id'      => $this->type_id,
            'type'         => $this->whenLoaded('type', fn () => [
                'id'   => $this->type?->id,
                'name' => $this->type?->name,
            ]),

            'brand_id'     => $this->brand_id,
            'brandRef'     => $this->whenLoaded('brandRef', fn () => [
                'id'   => $this->brandRef?->id,
                'name' => $this->brandRef?->name,
            ]),

            'model'        => $this->model,
            'serial_number'=> $this->serial_number,

            'status'       => $this->status,     // in_stock|assigned|repair|retired
            'condition'    => $this->condition,  // new|good|fair|poor
            'notes'        => $this->notes,

            'purchase_date'=> $this->purchase_date,
            'purchase_cost'=> $this->purchase_cost,

            // Depreciación pública (si quieres ocultarla, elimínalo)
            'depreciation' => [
                'years'   => $deprYears,        // años (float)
                'current' => $deprCurrent,      // valor actual estimado (float)
            ],

            // Campos de teléfono (si no usas para otros tipos, no pasa nada que vayan null)
            'phone_number' => $this->phone_number,
            'carrier'      => $this->carrier,
            'is_unlocked'  => (bool) $this->is_unlocked,

            // Ruta cruda en storage (el front ya arma la URL pública con Storage::url())
            'invoice_path' => $this->invoice_path,
        ];
    }

    /**
     * Depreciación lineal 10% anual, prorrateada por mes.
     */
    private function computeDepreciation($cost, $purchaseDate): array
    {
        $c = (float) ($cost ?? 0);
        if (!$c || !$purchaseDate) {
            return [0.0, $c];
        }

        try {
            $start = new \DateTime($purchaseDate);
            $now   = new \DateTime();
            $diff  = ($now->format('Y') - $start->format('Y')) * 12
                   + ($now->format('n') - $start->format('n'));
            $months = max(0, (int) $diff);
            $years  = $months / 12.0;

            $rate   = 0.10; // 10% anual
            $factor = max(0.0, 1.0 - $rate * $years);
            $current= round($c * $factor, 2);

            return [$years, $current];
        } catch (\Throwable $e) {
            return [0.0, $c];
        }
    }
}
