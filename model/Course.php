<?php

class Cis
{
    const CSC = ["Computer Science", "CSC"];
    const MAC = ["Mass Communication", "MAC"];
    const LIS = ["Library and Information Science", "LIS"];
    const TCS = ["Telecommunication and Communication Science", "TCS"];
    const ICS = ["Information and Communication Science", "ICS"];

    public static function classConstants(): array
    {
        return [self::CSC, self::MAC, self::LIS, self::TCS, self::LIS, self::ICS];
    }

    public static function getAllocatedYear(): int
    {
        return 4;
    }
}

class Engineering
{
    const CPENG = ["Computer Engineering", "CPENG"];
    const FENG = ["Food Engineering", "FENG"];
    const CENGR = ["Civil Engineering", "CENGR"];
    const CHENGR = ["Chemical Engineering", "CHENGR"];
    const AENGR = ["Agricultural Engineering", "AENGR"];

    public static function classConstants(): array
    {
        return [self::CPENG, self::FENG, self::CENGR, self::CHENGR, self::AENGR];
    }

    public static function getAllocatedYear(): int
    {
        return 5;
    }

}

class Agriculture
{
    const CRP = ["Crop Protection", "CRP"];
    const APR = ["Animal Production", "APR"];
    const AGR = ["Agronomy", "AGR"];
    const FRW = ["Forestry and Wildlife", "FRW"];
    const HSC = ["Home Science", "HSC"];

    public static function classConstants(): array
    {
        return [self::CRP, self::APR, self::AGR, self::FRW, self::HSC];
    }

    public static function getAllocatedYear(): int
    {
        return 5;
    }
}

class Education
{
    const AEDU = ["Adult Education", "AEDU"];
    const CEDU = ["Chemistry Education", "CEDU"];
    const PEDU = ["Physics Education", "PEDU"];
    const COEDU = ["Computer Education", "COEDU"];
    const BEDU = ["Biology Education", "BEDU"];

    public static function classConstants(): array
    {
        return [self::AEDU, self::CEDU, self::PEDU, self::COEDU, self::BEDU];
    }

    public static function getAllocatedYear(): int
    {
        return 4;
    }
}


class Law
{
    const CIL = ["Civil Law", "CIL"];
    const COL = ["Common Law", "COL"];
    const CSS = ["Criminology and Security Studies", "CSS"];
    const PIL = ["Private and Islamic Law", "PIL"];
    const PPIL = ["Public and Private International Law", "PPIL"];

    public static function classConstants(): array
    {
        return [self::CIL, self::COL, self::CSS, self::PIL, self::PPIL];
    }

    public static function getAllocatedYear(): int
    {
        return 5;
    }
}