<?php

namespace App\Repositories;

use CollegePortal\Models\User;
use CollegePortal\Models\CourseDependency;
use App\Filters\CourseDependencyFilters;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourseDependencyRepository
{
    public function model()
    {
        return app(CourseDependency::class);
    }

    public function list(User $user, CourseDependencyFilters $filters) {
        $items = $user->courseDependencies()->filter($filters)->paginate();
        $items->transform(function ($item) use ($filters) {
            return $filters->transform($item);
        });
        return $items;
    }

    public function single($id, CourseDependencyFilters $filters = null) {
        $q = $this->model();
        if ($filters) {
            $q = $q->filter($filters);
        }
        return $filters ? $filters->transform($q->findOrFail($id)) : $q->findOrFail($id);
    }

    public function delete($id) {
        return DB::transaction(function () use ($id) {
            return $this->model()->findOrFail($id)->delete();
        });
    }

    public function create($opts) {
        return DB::transaction(function () use ($opts) {
            return $this->model()->create($opts);
        });
    }

    public function update($id, $opts = []) {
        return DB::transaction(function () use ($id, $opts) {
            $item = $this->model()->findOrFail($id);
            $item->fill($opts);
            $item->save();
            return $item;
        });
    }

    public function count(CourseDependencyFilters $filters)
    {
        return $this->model()->filter($filters)->select('id', DB::raw('count(*) as total'))->count();
    }
}