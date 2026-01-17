<?php

namespace App\Http\Requests;

use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Foundation\Http\FormRequest;

class HoldSeatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'screening_id' => ['required', 'integer', 'exists:screenings,id'],
            'seat_id' => ['required', 'integer', 'exists:seats,id'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validationData()
    {
        $screening = $this->route('screening');
        $seat = $this->route('seat');

        $screeningId = $screening instanceof Screening ? $screening->getKey() : $screening;
        $seatId = $seat instanceof Seat ? $seat->getKey() : $seat;

        return array_merge(parent::validationData(), [
            'screening_id' => $screeningId,
            'seat_id' => $seatId,
        ]);
    }
}
