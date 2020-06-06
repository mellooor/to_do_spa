<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToDoItem extends Model
{
    /*
     * Casts attributes to data types to prevent issues when PDO doesn't cast them.
     */
    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'body' => 'string',
        'completed' => 'boolean'
    ];

    /*
     * The attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id', 'body'
    ];

    public function owner() {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}
