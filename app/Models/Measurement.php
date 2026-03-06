<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sample;
use App\Models\Method;
use App\Models\User; 

class Measurement extends Model
{
    protected $fillable = [
        'sample_id','method_id','status','assignee_id','priority','planned_at','started_at','finished_at','notes'
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function method()
    {
        return $this->belongsTo(Method::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class,'assignee_id');
    }
    
    public function resultSets()
    {
        return $this->hasMany(ResultSet::class);
    }
}