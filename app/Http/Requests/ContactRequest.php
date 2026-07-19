<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:80'],
            'need_type' => ['required', 'string', 'in:job,project,consulting,other'],
            'estimated_value' => ['nullable', 'string', 'max:80'],
            'subject' => ['nullable', 'string', 'max:180'],
            'systems' => ['nullable', 'string', 'max:180'],
            'offer_url' => ['nullable', 'url', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'consent' => ['accepted'],
            // Honeypot: must stay empty.
            'website' => ['nullable', 'size:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'consent.accepted' => __('portfolio.contact.consent_required'),
            'website.size' => 'Spam detected.',
        ];
    }
}
