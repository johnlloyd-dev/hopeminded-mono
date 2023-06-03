<?php

namespace App\Rules;


use Illuminate\Support\Facades\DB;

class UniqueDependingOnFlag
{
    protected $table;
    protected $column;
    protected $flagColumn;

    public function __construct($table, $column, $flagColumn)
    {
        $this->table = $table;
        $this->column = $column;
        $this->flagColumn = $flagColumn;
    }

    public function passes($attribute, $value)
    {
        $count = DB::table($this->table)
            ->where($this->column, $value)
            ->where($this->flagColumn, request()->input('flag'))
            ->count();

        return $count === 0;
    }

    public function message()
    {
        return 'The :attribute has already been taken for the given flag value.';
    }
}
