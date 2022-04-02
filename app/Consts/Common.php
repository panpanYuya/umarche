<?php

namespace App\Consts;


class Common{

    const PRODUCT_ADD = '1';
    const PRODUCT_REDUCE = '2';

    const PRODUCT_LIST =[
        //selfはclass内のconstを呼び出したい時に使う。
        'add' => self::PRODUCT_ADD,
        'reduce' => self::PRODUCT_REDUCE,
    ];
}
