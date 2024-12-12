<?php

namespace App\Http\Resources\CompanyBranches;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyBrancheResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $return = [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'title' => $this->title,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phones' => json_decode($this->phones),
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s')
        ];

        return $return;
    }
}
