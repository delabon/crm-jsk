<?php

declare(strict_types=1);

namespace App\Http\Resources\Contacts;

use App\Http\Resources\Addresses\AddressResource;
use App\Http\Resources\Users\UserBriefResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Contact $resource
 */
final class ContactResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'status_label' => $this->resource->status->label(),
            'status' => $this->resource->status,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'address' => $this->when(
                $this->resource->relationLoaded('address'),
                fn () => new AddressResource($this->resource->address),
            ),
            'owner' => $this->when(
                $this->resource->relationLoaded('user'),
                fn () => new UserBriefResource($this->resource->user),
            ),
            'account' => $this->when(
                $this->resource->relationLoaded('account'),
                fn () => $this->resource->account
                    ? [
                        'id' => $this->resource->account->id,
                        'name' => $this->resource->account->name,
                    ]
                    : null,
            ),
            'formatted_created_at' => $this->resource->formatted_created_at,
        ];
    }
}
