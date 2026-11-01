<?php

namespace App\Http\Requests\Solution;

class ContestStoreRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'order' => 'required',
        ]);
    }

    public function getOrder()
    {
        return $this->input('order');
    }
}
