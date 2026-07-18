<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use SoftDeletes;
    const FAILED = 0;
    const INPROGRESS = 1;
    const DONE = 2;
    // Payment method offline
    const TRANSFER = 'TRANSFER';
    const OFFLINE = 'OFFLINE';
    const IN_APP = 'IAP';

    const DEFAULT_PROVIDER = 'Rainichi';
    const GOOGLE_PLAY_PROVIDER = "GooglePlay";
    const APPLE_PLAY_PROVIDER = "ApplePay";


    public $table = 'order_payment';
    public $fillable = ['payment_method', 'amount', 'credit', 'user_id', 'final_amount', 'payment_provider',
        'payment_transaction_id', 'payment_status', 'payment_msg', 'discount_code', 'discount_amount', 'is_enterprise',
        'created_by', 'updated_by', 'return_url'];

    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $casts = [
        'is_enterprise' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * Search by date range
     * @param $q
     * @param $from_date (optional)
     * @param $to_date (optional)
     * @return mixed
     */
    public function scopeDateRange($q, $from_date, $to_date)
    {
        if (isset($from_date) && isset($to_date)) {
            $from_date = date($from_date);
            $to_date = date($to_date);
            return $q->whereBetween('created_at', [$from_date, $to_date]);
        } elseif (isset($from_date) && !isset($to_date)) {
            $from_date = date($from_date);
            return $q->whereDate('created_at', '>=', $from_date);
        } elseif (!isset($from_date) && isset($to_date)) {
            $to_date = date($to_date);
            return $q->whereDate('created_at', '<=', $to_date);
        }
    }

    /**
     * @return HasOne
     */
    public function paymentCourse()
    {
        return $this->hasOne('App\Models\OrderPaymentCourse', 'order_payment_id', 'id');
    }

    /**
     * @param $q
     * @param $is_enterprise
     * @return mixed
     */
    public function scopeIsEnterprise($q, $is_enterprise)
    {
        if (isset($is_enterprise)) {
            return $q->where('is_enterprise', (boolean)$is_enterprise);
        }
    }

    /**
     * @param $q
     * @param $payment_status
     * @return mixed
     */
    public function scopePaymentStatus($q, $payment_status)
    {
        if (isset($payment_status)) {
            return $q->where('payment_status', $payment_status);
        }
    }

    /**
     * @param $q
     * @param $payment_method
     * @return mixed
     */
    public function scopePaymentMethod($q, $payment_method)
    {
        if (isset($payment_method)) {
            return $q->where('payment_method', $payment_method);
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @param $is_enterprise
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string, $lang, $is_enterprise)
    {
        if (isset($search_string)) {
            $queryCollection = $q->join('users', function ($join) use ($search_string) {
                $join->on('users.id', '=', 'order_payment.user_id')
                    ->where('users.name', 'like', '%' . $search_string . '%')
                    ->where('users.email', 'like', '%' . $search_string . '%');
            });
            if ($is_enterprise) {
                $queryCollection = $queryCollection->join('order_payment_course', function ($join) use ($search_string) {
                    $join->on('order_payment_course.order_payment_id', '=', 'order_payment.id')
                        ->join('classroom', 'classroom.id', '=', 'order_payment_course.classroom_id')
                        ->where('classroom.name', 'like', '%' . $search_string . '%');
                });
            }
            return $queryCollection->select('order_payment.*');
        }
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
