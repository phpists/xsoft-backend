<?php

namespace App\Http\Resources\Staff;

use App\Http\Resources\CompanyBranches\CompanyBranchesResource;
use App\Http\Resources\Media\MediasResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'category_id' => $this->category_id,
            'role_id' => $this->role_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'color' => $this->color,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'phones' => json_decode($this->phones),
            'media' => isset($this->staffMedia) ? new MediasResource($this->staffMedia) : null,
            'branches' => isset($this->userBranch) ? new CompanyBranchesResource($this->getUserBranch()) : null
        ];

        return $return;
    }
}
