<?php

namespace Local\ModelAudit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event' => ['nullable', 'string', 'max:255'],
            'subject_type' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'string', 'max:255'],
            'actor_type' => ['nullable', 'string', 'max:255'],
            'actor_id' => ['nullable', 'string', 'max:255'],
            'request_id' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ];
    }
}
