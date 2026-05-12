<?php

namespace App\Http\Livewire\Partners;

use App\Models\Partner;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerView extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $showFavorites = false;
    public $selectedPartnerId;
    public $visitDate;
    public $visitNotes;

    protected $listeners = ['partnerDeleted' => '$refresh'];

    public function toggleFavorite($id)
    {
        $partner = Partner::findOrFail($id);
        $partner->is_favorite = !$partner->is_favorite;
        $partner->save();
        
        $this->dispatch('refresh-ui');
    }

    public function toggleShowFavorites()
    {
        $this->showFavorites = !$this->showFavorites;
        $this->resetPage();
    }

    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->authorize('partner-list');
    }

    public function openVisitModal($id)
    {
        $this->selectedPartnerId = $id;
        $this->visitDate = date('Y-m-d');
        $this->visitNotes = '';
        $this->dispatch('open-visit-modal');
    }

    public function logVisit()
    {
        $this->validate([
            'visitDate' => 'required|date',
            'selectedPartnerId' => 'required|exists:partners,id',
        ]);

        try {
            \App\Models\PartnerMeeting::create([
                'partner_id' => $this->selectedPartnerId,
                'meeting_date' => $this->visitDate,
                'notes' => $this->visitNotes,
            ]);

            // Update the partner's last_day_we_met if this date is newer or if it's the first one
            $partner = Partner::find($this->selectedPartnerId);
            if (!$partner->last_day_we_met || \Carbon\Carbon::parse($this->visitDate)->isAfter($partner->last_day_we_met)) {
                $partner->update(['last_day_we_met' => $this->visitDate]);
            }

            $this->dispatch('visit-logged');
            $this->dispatch('close-visit-modal');
            session()->flash('success', 'Visit logged successfully');
            
            $this->reset(['selectedPartnerId', 'visitDate', 'visitNotes']);

        } catch (\Exception $e) {
            session()->flash('error', 'Error logging visit: ' . $e->getMessage());
        }
    }

    #[On('destroy')]
    public function destroy($id)
    {
        try {
            $partner = Partner::findOrFail($id);

            if ($partner->image && \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->exists($partner->image)) {
                \Storage::disk(env('FILESYSTEM_DRIVER', 'public'))->delete($partner->image);
            }

            $partner->delete();

            $this->dispatch('partnerDeleted');
            session()->flash('success', 'Partner deleted successfully');

        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting partner: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Partner::query();
        
        if ($this->showFavorites) {
            $query->where('is_favorite', true);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $partners = $query
            ->orderByRaw('last_day_we_met IS NULL')
            ->orderBy('last_day_we_met', 'desc')
            ->paginate(10);

        return view('livewire.partners.partner-view', compact('partners'));
    }
}
