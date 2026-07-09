<?php

declare(strict_types=1);

namespace App\Http\Resources\Accounts;

use App\Http\Resources\Addresses\AddressResource;
use App\Http\Resources\Contacts\ContactResource;
use App\Http\Resources\Users\UserBriefResource;
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
            'owner' => $this->when(
                $this->resource->relationLoaded('user'),
                fn () => new UserBriefResource($this->resource->user),
            ),
            'contacts' => $this->when(
                $this->resource->relationLoaded('contacts'),
                fn () => ContactResource::collection($this->resource->contacts),
            ),
            'addresses' => $this->when(
                $this->resource->relationLoaded('addresses'),
                fn () => AddressResource::collection($this->resource->addresses),
            ),
            'formatted_created_at' => $this->resource->formatted_created_at,
        ];
    }
}
