<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class CategoryFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function setup()
    {
        if (array_key_exists('search', $this->input())) {
            $this->blacklistMethod('name');
            $this->blacklistMethod('description');
        }
    }

    public function name($name)
    {
        return $this->where('name', 'LIKE', "%$name%");
    }

    public function description($description)
    {
        return $this->where('description', 'LIKE', "%$description%");
    }

    public function active($active)
    {
        $bool = filter_var($active, FILTER_VALIDATE_BOOLEAN);
        return $this->where('active', $bool);
    }

    
    public function search($search)
    {
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%");
        });
    }
}
