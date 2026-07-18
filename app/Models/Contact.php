<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Base
{
    protected $table = 'contacts';

    protected $fillable = ['type', 'name', 'email', 'phone', 'address', 'course_id', 'num_of_employee',
        'content', 'view', 'is_active', 'ip_address', 'user_agent', 'title'];

    //Declare type contact
    const INDEX = 1;
    const CENTER = 2;
    const ENTERPRISE = 3;
    const CONTACT = 4;
    const SHOP = 5;

    /**
     * @return  array types
     */
    public static function getContactType()
    {
        $contacts = [
            Contact::INDEX => 'Trang chủ',
            Contact::CENTER => 'Trang Trung Tâm',
            Contact::ENTERPRISE => 'Trang Doanh nghiệp',
            Contact::CONTACT => 'Trang Liên hệ',
            Contact::SHOP => 'Trang Cửa hàng',
        ];
        return $contacts;
    }

    /**
     * @return  array types that provide CMS access
     */
    public static function getContactList()
    {
        $contacts = [
            ['id' => Contact::INDEX, 'name' => 'Trang chủ'],
            ['id' => Contact::CENTER, 'name' => 'Trang Trung Tâm'],
            ['id' => Contact::ENTERPRISE, 'name' => 'Trang Doanh nghiệp'],
            ['id' => Contact::CONTACT, 'name' => 'Trang Liên hệ'],
            ['id' => Contact::SHOP, 'name' => 'Trang Cửa hàng'],
        ];
        return $contacts;
    }

    /**
     * @param $q
     * @param $search_string
     * @return mixed
     */
    public function scopeSearch($q, $search_string)
    {
        $search_string = trim($search_string); // Clean up white space
        if (isset($search_string)) {
            return $q->where(function ($query) use ($search_string) {
                $query->where('name', 'like', '%' . $search_string . '%')
                    ->orWhere('email', 'like', '%' . $search_string . '%')
                    ->orWhere('phone', 'like', '%' . $search_string . '%')
                    ->orWhere('address', 'like', '%' . $search_string . '%');
            });
        }
    }

    /**
     * @param $q
     * @param $page_id
     */
    public function scopeGetPage($q, $page_id)
    {
        if (isset($page_id)) {
            $q->where('type', $page_id);
        }
    }

}
