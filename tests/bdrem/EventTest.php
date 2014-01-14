<?php
namespace bdrem;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testIsWithinSameDaySameYear()
    {
        $event = new Event('Amy', 'birthday', '1995-02-24');
        $this->assertTrue($event->isWithin('1995-02-24', 0, 0));
        $this->assertTrue($event->isWithin('1995-02-24', 3, 7));
    }

    public function testIsWithinSameDayDifferentYear()
    {
        $event = new Event('Amy', 'birthday', '1995-02-24');
        $this->assertTrue($event->isWithin('2019-02-24', 0, 0));
        $this->assertTrue($event->isWithin('1996-02-24', 3, 7));
    }

    public function testIsWithinOneDayAfterSameYear()
    {
        $event = new Event('Amy', 'birthday', '1995-02-24');
        $this->assertFalse($event->isWithin('1995-02-23', 0, 0));
        $this->assertTrue($event->isWithin('1995-02-23', 3, 7));
    }

    public function testIsWithinOneDayBeforeSameYear()
    {
        $event = new Event('Amy', 'birthday', '1995-02-24');
        $this->assertFalse($event->isWithin('1995-02-25', 0, 0));
        $this->assertTrue($event->isWithin('1995-02-25', 3, 7));
    }

    public function testIsWithinOneDayBeforeDifferentYear()
    {
        $event = new Event('Amy', 'birthday', '1995-02-24');
        $this->assertFalse($event->isWithin('1999-02-25', 0, 0));
        $this->assertTrue($event->isWithin('1990-02-25', 3, 7));
    }

    public function testIsWithinThreeDaysBeforeSameYear()
    {
        $event = new Event('Amy', 'birthday', '1995-02-24');
        $this->assertFalse($event->isWithin('1999-02-27', 0, 0));
        $this->assertFalse($event->isWithin('1999-02-27', 1, 0));
        $this->assertFalse($event->isWithin('1999-02-27', 2, 0));
        $this->assertTrue( $event->isWithin('1990-02-27', 3, 0));

        $this->assertFalse($event->isWithin('1999-02-27', 0, 3));
    }

    public function testIsWithinYearOverflowAfter()
    {
        $event = new Event('Amy', 'birthday', '1995-01-01');
        $this->assertTrue($event->isWithin('2019-12-31', 0, 1));
        $this->assertFalse($event->isWithin('1996-12-30', 0, 1));
    }

    public function testIsWithinYearOverflowBefore()
    {
        $event = new Event('Amy', 'birthday', '1995-12-30');
        $this->assertTrue($event->isWithin('2019-01-02', 3, 0));
        $this->assertFalse($event->isWithin('1996-01-02', 2, 0));
        $this->assertTrue($event->isWithin('1996-01-01', 2, 0));
    }

    public function testCompareDifferentMonths()
    {
        $this->assertLessThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '2013-05-10'),
                new Event('Bob', 'birthday', '2013-06-10')
            )
        );
        $this->assertGreaterThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '2013-10-10'),
                new Event('Bob', 'birthday', '2013-08-10')
            )
        );
    }

    public function testCompareDifferentMonthsYearOverflow()
    {
        $this->assertGreaterThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '2013-01-10'),
                new Event('Bob', 'birthday', '2013-12-10')
            )
        );
        $this->assertLessThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '2013-12-10'),
                new Event('Bob', 'birthday', '2013-02-10')
            )
        );
    }

    public function testCompareDifferentDays()
    {
        $this->assertLessThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '1950-05-10'),
                new Event('Bob', 'birthday', '2013-05-11')
            )
        );
        $this->assertGreaterThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '2013-10-20'),
                new Event('Bob', 'birthday', '1992-10-02')
            )
        );
    }

    public function testCompareSameDay()
    {
        $this->assertLessThan(
            0, 
            Event::compare(
                new Event('Amy', 'birthday', '1950-05-10'),
                new Event('Bob', 'birthday', '2013-05-10')
            )
        );
        $this->assertGreaterThan(
            0, 
            Event::compare(
                new Event('Bob', 'birthday', '1992-10-02'),
                new Event('Amy', 'birthday', '2013-10-02')
            )
        );
    }
}
?>
