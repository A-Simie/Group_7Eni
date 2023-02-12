<?php

class Faculty
{
    const CIS = "Communication And Information Sciences";
    const ENGR = "Engineering and Technology";
    const AGRIC = "Agriculture";
    const EDU = "Education";
    const LAW = "Law";

    public static function classConstants(): array
    {
        return [self::CIS, self::ENGR, self::AGRIC, self::EDU, self::LAW];
    }
}