<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    protected $table = 'mail_setting';

    public $fillable = ['driver', 'email', 'name', 'host', 'port', 'password', 'encryption'];

    protected $hidden = ['password'];
}
