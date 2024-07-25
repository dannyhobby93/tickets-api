<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected $builder;
    protected $request;
    protected $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function filter($arr)
    {
        foreach ($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }

    // public function sort($value)
    // {
    //     $sort_attributes = explode(',', $value);

    //     foreach ($sort_attributes as $attribute) {
    //         $direction = 'asc';

    //         if (strpos($attribute, '-') === 0) {
    //             $direction = 'desc';
    //             $attribute = substr($attribute, 1);
    //         }

    //         if (!in_array($attribute, $this->sortable) && !array_key_exists($attribute, $this->sortable)) {
    //             continue;
    //         }

    //         $column_name = $this->sortable[$attribute] ?? $attribute;

    //         $this->builder->orderBy($column_name, $direction);
    //     }

    //     return $this->builder;
    // }
    protected function sort($values)
    {
        $sortables = explode(',', $values);

        foreach ($sortables as $sortable) {
            $direction = Str::startsWith($sortable, '-') ? 'desc' : 'asc';
            $column = Str::of($sortable)->remove('-')->snake()->value();

            if (in_array($column, $this->sortable) && !array_key_exists($column, $this->sortable)) {

                $column_name = $this->sortable[$column] ?? $column;

                $this->builder->orderBy($column_name, $direction);
            }
        }
    }

}