<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('searchMany', function ($fields, $string, $perPage = 10) {
            // Apply text search if a search string is provided
            if ($string) {
                $this->where(function ($query) use ($fields, $string) {
                    foreach ($fields as $field) {
                        // Check if the field contains a dot, indicating a relation
                        if (strpos($field, '.') !== false) {
                            [$relation, $relationField] = explode('.', $field, 2);
                            // Search in the related model using whereHas
                            $query->orWhereHas($relation, function ($query) use ($relationField, $string) {
                                $query->where($relationField, 'like', '%' . $string . '%');
                            });
                        } else {
                            // Field is in the main table
                            $query->orWhere($field, 'like', '%' . $string . '%');
                        }
                    }
                });
            }

            return $perPage !== null
                ? $this->orderBy('id', 'desc')->paginate($perPage)
                : $this->orderBy('id', 'desc')->get();
        });
    }
}
