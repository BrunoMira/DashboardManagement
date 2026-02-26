<?php

namespace App\Http\Requests;

class UpdatePropertyRequest extends StorePropertyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
