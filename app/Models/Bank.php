<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'code',
        'swift_code',
        'status'
    ];

    public static function getNepalBankList()
    {
        return [
            ['name' => 'Agricultural Development Bank Limited', 'code' => 'ADBL', 'status' => 1],
            ['name' => 'Bank of Kathmandu Ltd.', 'code' => 'BOK', 'status' => 1],
            ['name' => 'Citizens Bank International Ltd.', 'code' => 'CBIL', 'status' => 1],
            ['name' => 'Civil Bank Ltd.', 'code' => 'CBL', 'status' => 1],
            ['name' => 'Everest Bank Ltd.', 'code' => 'EBL', 'status' => 1],
            ['name' => 'Global IME Bank Ltd.', 'code' => 'GIBL', 'status' => 1],
            ['name' => 'Himalayan Bank Ltd.', 'code' => 'HBL', 'status' => 1],
            ['name' => 'Kumari Bank Ltd.', 'code' => 'KBL', 'status' => 1],
            ['name' => 'Laxmi Sunrise Bank Ltd.', 'code' => 'LSBL', 'status' => 1],
            ['name' => 'Machhapuchchhre Bank Ltd.', 'code' => 'MBL', 'status' => 1],
            ['name' => 'Mega Bank Nepal Ltd.', 'code' => 'MEGA', 'status' => 1],
            ['name' => 'Nepal Bank Ltd.', 'code' => 'NBL', 'status' => 1],
            ['name' => 'Nepal Credit and Commerce Bank Ltd.', 'code' => 'NCC', 'status' => 1],
            ['name' => 'Nepal Investment Mega Bank Ltd.', 'code' => 'NIMB', 'status' => 1],
            ['name' => 'Nepal SBI Bank Ltd.', 'code' => 'NSBI', 'status' => 1],
            ['name' => 'NIC Asia Bank Ltd.', 'code' => 'NIC', 'status' => 1],
            ['name' => 'NMB Bank Ltd.', 'code' => 'NMB', 'status' => 1],
            ['name' => 'Prabhu Bank Ltd.', 'code' => 'PRABHU', 'status' => 1],
            ['name' => 'Rastriya Banijya Bank Ltd.', 'code' => 'RBB', 'status' => 1],
            ['name' => 'Sanima Bank Ltd.', 'code' => 'SANIMA', 'status' => 1],
            ['name' => 'Standard Chartered Bank Nepal Ltd.', 'code' => 'SCBN', 'status' => 1],
            ['name' => 'Siddhartha Bank Ltd.', 'code' => 'SBL', 'status' => 1],
        ];
    }
}
