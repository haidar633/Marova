<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

trait RoleBasedQueryTrait
{
    public function applyRoleBasedScope(Builder $query, $model)
    {
        $user = auth()->user();
//
//        if (!$user->hasRole('Admin') && $user->organization_id) {
//            if ($user->is_manager) {
//                $query->where('organization_id', $user->organization_id);
//            } else {
//                if (Route::currentRouteName() === 'roles') {
//                    abort(401);
//                }
//                if ($model == 'App\Models\User') {
//                    $query->where(['id' => $user->id, 'organization_id' => $user->organization_id]);
//                } else {
//                    $query->where(['user_id' => $user->id, 'organization_id' => $user->organization_id]);
//                }
//            }
//        }
        return $query;
    }

    public function getScopedAndSearchedData($model)
    {
        $query = $model::query();
        $query = $this->applyRoleBasedScope($query, $model);
        return $query;
    }
}
