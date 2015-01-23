<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 23.01.15
 * Time: 13:15
 */

namespace Helpers;


class Consts {
    const appSecretKey = "sFHePANXTQfhYprW7q2agtotD5YPNh"; // secret key which will encrypt/decrypt tokens
    const tokenPermanence = "PT15M";                       // token permanence (DateInterval)
    const mysqlDateTimeColumnFormat = "Y-m-d H:i:s";       // mysql DateTime column format
} 