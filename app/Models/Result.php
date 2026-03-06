<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['result_set_id','field_key','value_text','value_num','unit','flags_json'];

    protected $casts = [
        'flags_json' => 'array'
    ];

    public function resultSet() {
        return $this->belongsTo(ResultSet::class);
    }
}