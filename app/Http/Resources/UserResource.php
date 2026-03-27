<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read User $resource
 */
final class UserResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'created_at' => $this->resource->created_at,
            'formatted_created_at' => $this->resource->formatted_created_at,
            'email_verified_at' => $this->resource->email_verified_at,
            'formatted_email_verified_at' => $this->resource->formatted_email_verified_at,
            'formatted_role' => $this->resource->formatted_role,
            'main_role' => $this->resource->main_role,
            'role_names' => $this->resource->role_names,
            'permission_names' => $this->resource->permission_names,
        ];
    }
}
