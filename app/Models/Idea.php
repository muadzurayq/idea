<?php

namespace App\Models;

use App\IdeaStatus;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Idea extends Model
{
    /** @use HasFactory<\Database\Factories\IdeaFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'image_path',
        'links',
    ];

    /**
     * @var \Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    protected $casts=[
        'links'=> AsArrayObject::class,
        'status'=> IdeaStatus::class,

    ];

    public static function statusesCount(User  $user) : Collection
    {
        $count = $user
            ->ideas()->selectRaw('status,count(*) as count')
            ->groupBy('status')

            ->pluck('count', 'status');

        return collect(IdeaStatus::cases())
            ->mapWithKeys(fn($status) => [
                $status->value => $count->get($status->value, 0)
            ])
            ->put('all', Auth::user()->ideas()->count());
    }
    protected $attributes = [
        'status'=> IdeaStatus::PENDING
        ];
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function steps(): hasMany
    {
        return $this->hasMany(Step::class);
    }

}
