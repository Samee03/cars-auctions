<?php

namespace App\Http\Controllers;

use App\DTOs\AddressDTO;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use App\Traits\ApiResponse;

class AddressController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AddressService $addressService)
    {
    }

    public function index()
    {
        $addresses = auth()->user()->addresses;

        return $this->success(AddressResource::collection($addresses));
    }

    public function show(Address $address)
    {
        return $this->success(AddressResource::make($address));
    }

    public function store(UpdateAddressRequest $request)
    {
        $dto = AddressDTO::fromArray($request->validated());

        $address = $this->addressService->storeAddress($dto);

        return $this->success(AddressResource::make($address), "Address created successfully.", 201);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $dto = AddressDTO::fromArray($request->validated());

        $addresses = $this->addressService->updateAddress($dto, $address);

        return $this->success(AddressResource::collection($addresses), "Address updated successfully");
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return $this->success(null, "Address deleted successfully");
    }
}
