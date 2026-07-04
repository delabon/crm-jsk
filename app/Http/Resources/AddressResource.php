<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Address $resource
 */
final class AddressResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'line1' => $this->resource->line1,
            'line2' => $this->resource->line2,
            'city' => $this->resource->city,
            'region_id' => $this->resource->region_id,
            'country_id' => $this->resource->country_id,
            'postal_code' => $this->resource->postal_code,
            'country_name' => $this->whenLoaded('country', fn () => $this->resource->country?->name),
            'region_name' => $this->whenLoaded('region', fn () => $this->resource->region?->name),
        ];
    }
}
