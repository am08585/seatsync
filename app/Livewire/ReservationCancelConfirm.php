<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ReservationCancelConfirm extends Component
{
    public ?Reservation $reservation = null;

    public bool $showModal = false;

    public bool $isProcessing = false;

    protected $listeners = [
        'open-cancel-modal' => 'openModal',
        'close-cancel-modal' => 'closeModal',
    ];

    public function openModal(Reservation $reservation): void
    {
        $this->reservation = $reservation;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reservation = null;
        $this->showModal = false;
        $this->isProcessing = false;
    }

    public function cancelReservation()
    {
        if ($this->reservation === null) {
            return;
        }

        $this->isProcessing = true;

        try {
            $reservationService = app(ReservationService::class);
            $result = $reservationService->cancel($this->reservation);

            // Dispatch success event to parent component
            $this->dispatch('reservation-cancelled', result: $result);

            // Close modal
            $this->closeModal();

            // Redirect to cancelled page
            return redirect()->route('reservation.cancelled', [
                'reservation' => $result['reservation']->id,
            ]);

        } catch (AuthorizationException $e) {
            $this->addError('authorization', 'You are not authorized to cancel this reservation.');
        } catch (ValidationException $e) {
            $this->addError('validation', $e->getMessage());
        } catch (\Throwable $e) {
            $this->addError('general', 'An error occurred while cancelling the reservation. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render(): View
    {
        return view('livewire.reservation-cancel-confirm');
    }
}
