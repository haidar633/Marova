<?php

namespace App\Http\Livewire\FavoriteHubs;

use App\Models\Expenses;
use App\Models\FavoriteHub;
use DB;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\RoleBasedQueryTrait;

class FavoriteHubView extends Component
{
    use AuthorizesRequests, WithPagination, RoleBasedQueryTrait;

    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $searchableFields = ['name'];

    public $favoriteHub;

    protected $listeners = [
        'destroy',
    ];

    public function mount()
    {
        $this->authorize('favorate-hub-list');

        $this->favoriteHub = FavoriteHub::orderBy('id', 'ASC')->get();
    }

    #[On('destroy')]
    public function destroy($id)
    {
        FavoriteHub::where('id', $id)->delete();

        return redirect()->route('favorite-hubs');
    }

    public function render()
    {
        $query = $this->getScopedAndSearchedData(FavoriteHub::class)
            ->orderBy('created_at', 'desc');

        return view('livewire.FavoriteHubs.favorite-hub-view', [
            'favoriteHubs' => $query->paginate(10),
        ]);
    }

}
