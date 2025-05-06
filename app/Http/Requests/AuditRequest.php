<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AuditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasAccess('public_quality_labels_audit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quality_label'  => ['required', "string"],
            'name'  => ['required', "string"],
            'date' => ['required', 'date'],
            'validation' => ['nullable'],
            'auditor' => ['nullable', 'string'],
            'comment' => ['nullable'],
            'audit_type' => ["required"],
            'sample' => ['nullable'],
            'wealths' => ['required', 'array', 'min:1'] //size?
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'quality_label.required' => 'error.quality_label.required',
            'name.required' => 'error.audit.name_required',
            'date.required' => 'error.audit.date_required',
            'audit_type.required' => 'error.audit.audit_type_required',
            'wealths.required' => 'error.audit.wealths_required'
        ];
    }
}
