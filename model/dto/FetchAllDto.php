<?php

class FetchAllDto
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function map(): array
    {
        $array = array();
        for ($i = 0; $i < sizeof($this->data); $i++) {
            $obj = $this->data[$i];
            if ($obj["RUSTICATED"] == 0)
                $obj["RUSTICATED"] = false;
            else
                $obj["RUSTICATED"] = true;
            $array[$i] = [$obj["STUDENT_ID"], $obj["FIRST_NAME"], $obj["MIDDLE_NAME"], $obj["LAST_NAME"], $obj["AGE"], $obj["GENDER"], $obj["PHONE_NUMBER"], $obj["EMAIL_ADDRESS"], $obj["DEPARTMENT"], $obj["FACULTY"], $obj["ALLOCATED_YEAR"], $obj["DESIRED_LEVEL"], $obj["APPLICATION_NUMBER"], $obj["APPLICATION_DATE"], $obj["RUSTICATED"]];
        }
        return $array;
    }
}