<?php

use App\Specimen;
use App\Test;
use App\Price;
use App\Room;

Class Helper 
{
    /**
     * This is helper function to map the menu of the master
     * The key is the url, and value is the title or label of the link
     */
    public static function masterMenu(){
        return [
            url('master/test') => 'Master Tests',
            url('master/package') => 'Master Packages',
            url('master/patient') => 'Master Patients',
            url('master/group') => 'Master Groups',
            url('master/analyzer') => 'Master Analyzers',
            url('master/specimen') => 'Master Specimens',
            url('master/doctor') => 'Master Doctors',
            url('master/insurance') => 'Master Insurances',
            url('master/price') => 'Master Prices',
            url('master/room') => 'Master Rooms'
        ];
    }

    public static function specimenColor()
    {
        return Specimen::COLOR;
    }

    public static function testRangeType()
    {
        return Test::RANGE_TYPE;
    }

    public static function priceType()
    {
        return Price::TYPE;
    }

    public static function roomType()
    {
        return Room::TYPE;
    }
}