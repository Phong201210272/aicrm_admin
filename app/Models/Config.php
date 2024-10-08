<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $table = "config";

    // Cập nhật các cột có thể điền được
    protected $fillable = [
        'logo',
        'receiver', // thêm cột receiver
        'bank_account',
        'qr',
        'bank_id',
        'user_id' // thêm cột user_id
    ];

    // Nếu bạn vẫn cần sử dụng thuộc tính 'bank', giữ lại 'appends' và các phương thức liên quan
    protected $appends = ['bank', 'user'];

    // Định nghĩa quan hệ với Bank
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    // Định nghĩa thuộc tính 'bank'
    public function getBankAttribute()
    {
        return Bank::where('id', $this->attributes['bank_id'])->first();
    }

    public function getUserAttribute()
    {
        return User::where('id', $this->attributes['user_id'])->first();
    }

    // Định nghĩa quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
