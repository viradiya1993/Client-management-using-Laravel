<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id) {
        return Tax::where('id', $id);
    }
}
