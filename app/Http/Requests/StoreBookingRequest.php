<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'court_id'     => 'required|exists:courts,id',
            'date'         => 'required|date|after_or_equal:today',
            'time_start'   => 'required|date_format:H:i',
            'time_end'     => 'required|date_format:H:i|after:time_start',
            'court_detail' => 'nullable|string|max:255',
            'slots'        => 'required|array|min:1',
            'slots.*'      => 'required|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'date.after_or_equal' => 'Tanggal booking tidak boleh di masa lalu.',
            'time_end.after'      => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'slots.required'      => 'Anda harus memilih minimal satu slot waktu.',
        ];
    }
}
