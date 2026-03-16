<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'account_type' => $this->account_type,
            'status' => (bool) $this->status,
            'email_verified' => $this->hasVerifiedEmail(),
            'email_verified_at' => $this->email_verified_at,
            'approval_status' => $this->approval_status,
            'is_approved' => $this->isApproved(),
            'is_pending_approval' => $this->isPendingApproval(),
            'company_profile' => $this->when(
                $this->isCompanyBuyer(),
                fn () => $this->companyProfile ? [
                    'company_name' => $this->companyProfile->company_name,
                    'registration_number' => $this->companyProfile->registration_number,
                    'company_phone' => $this->companyProfile->company_phone,
                    'company_country' => $this->companyProfile->company_country,
                    'company_city' => $this->companyProfile->company_city,
                    'company_address' => $this->companyProfile->company_address,
                ] : null
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
