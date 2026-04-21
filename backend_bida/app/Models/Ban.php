<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ban extends Model
{
    use HasFactory;

    public const LOAI_THUONG = 1;
    public const LOAI_VIP = 2;

    protected $table = 'bans';
    protected $primaryKey = 'ban_id';
    public $timestamps = true;

    protected $appends = [
        'loai_ban_label',
        'hourly_rate',
    ];

    protected $fillable = [
        'ban_name',
        'loai_ban',
        'status',
    ];

    public function getLoaiBanLabelAttribute(): string
    {
        return $this->isVip() ? 'VIP' : 'Thường';
    }

    public function getHourlyRateAttribute(): int
    {
        return $this->isVip()
            ? (int) config('bida.hourly_rates.vip', 70000)
            : (int) config('bida.hourly_rates.thuong', 50000);
    }

    public function isVip(): bool
    {
        // Hỗ trợ dữ liệu cũ từng dùng giá trị 3.
        return in_array((int) $this->loai_ban, [self::LOAI_VIP, 3], true);
    }
}
