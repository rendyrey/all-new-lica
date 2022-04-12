<?php

use App\Specimen;
use App\Test;

Class Helper 
{
    /**
     * This is helper function to map the menu of the master
     * The key is the url, and value is the title or label of the link
     */
    public static function masterMenu(){
        return [
            url('master/test') => 'Master Test',
            url('master/patient') => 'Master Patients',
            url('master/group') => 'Master Groups',
            url('master/analyzer') => 'Master Analyzer',
            url('master/specimen') => 'Master Specimen',
            url('master/doctor') => 'Master Doctor',
            url('master/insurance') => 'Master Insurance',
            url('master/package') => 'Master Package'
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
}