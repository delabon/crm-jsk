<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Account $resource
 */
final class AccountResource extends JsonResource
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
            'description' => $this->resource->description,
            'industry' => $this->resource->industry,
            'website' => $this->resource->website,
            'phone' => $this->resource->phone,
            'owner' => $this->resource->user->name,
            'owner_detail' => $this->when(
                $this->resource->relationLoaded('user'),
                fn (): array => [
                    'name' => $this->resource->user->name,
                    'formatted_role' => $this->resource->user->formatted_role,
                ],
            ),
            'formatted_created_at' => $this->resource->formatted_created_at,
        ];
    }
}
