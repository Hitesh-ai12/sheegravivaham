<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempUser extends Model
{
    use HasFactory;

    protected $table = 'temp_users'; // 👈 correct table name
    protected $fillable = [
        'username', 'email', 'password', 'mobile_number', 'mother_tongue', 'account_type'
    ];
}
