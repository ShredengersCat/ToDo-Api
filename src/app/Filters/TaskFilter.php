<?php

namespace App\Filters;

class TaskFilter extends QueryFilter
{
    public function priority_search($param = 0)
    {
        return $this->builder->where('priority', '=' , $param);
    }

    public function name_field($search_string = '')
    {
        return $this->builder
            ->where('name', 'LIKE', '%'.$search_string.'%')
            ->orWhere('description', 'LIKE', '%'.$search_string.'%');
    }

    public function status($param = 0)
    {
        return $this->builder->where('status', $param);
    }

    public function created_at()
    {
        return $this->builder->latest();
    }

    public function priority()
    {
        return $this->builder->orderBy('priority');
    }
}