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
            'date_of_birth' => $this->date_of_birth,

            // Optionally
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),

            'phone' => $this->phone,
            'account_type' => $this->account_type,
            'status' => $this->status,
            'email_verified' => $this->hasVerifiedEmail(),
            'email_verified_at' => $this->email_verified_at,
            'approval_status' => $this->admin_approval_status,
            'admin_approved_at' => $this->admin_approved_at,
            'verified_badge' => $this->verified_badge,
            'is_approved' => $this->isApproved(),
            'is_pending_approval' => $this->isPendingApproval(),
            'company_profile' => $this->when(
                $this->isCompanyBuyer(),
                fn () => $this->companyProfile ? [
                    'company_name' => $this->companyProfile->company_name,
                    'registration_number' => $this->companyProfile->registration_number,
                    'company_phone' => $this->companyProfile->company_phone,
                    'company_address' => $this->companyProfile->company_address,
                    'contact_first_name' => $this->companyProfile->contact_first_name,
                    'contact_last_name' => $this->companyProfile->contact_last_name,
                    'contact_email' => $this->companyProfile->contact_email,
                    'contact_phone' => $this->companyProfile->contact_phone,
                    'address' => $this->companyProfile->address ? [
                        'id' => $this->companyProfile->address->id,
                        'country' => $this->companyProfile->address->country,
                        'city' => $this->companyProfile->address->city,
                        'street' => $this->companyProfile->address->street,
                        'state' => $this->companyProfile->address->state,
                        'zip' => $this->companyProfile->address->zip,
                    ] : null,
                ] : null
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
