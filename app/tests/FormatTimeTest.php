<?php
declare(strict_types=1);

/**
 * Tests
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
use PHPUnit\Framework\TestCase;

final class FormatTimeTest extends TestCase
{

    public function testMomentsAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time());

        $this->assertEquals(
            'Moments ago',
            $text
        );
    }
    
    public function testOneMinAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time() - 60);

        $this->assertEquals(
            '1 min ago',
            $text
        );
    }
    
    public function testOneHrAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time() - 3600);

        $this->assertEquals(
            '1 h ago',
            $text
        );
    }
    
    public function testHrsAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time() - 86400);

        $this->assertEquals(
            'Yesterday',
            $text
        );
    }

    public function test2DaysAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time() - intval(86400*2));

        $this->assertEquals(
            '2 days ago',
            $text
        );
    }

    public function test2WeeksAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time() - intval(86400*14));

        $this->assertEquals(
            '2 weeks ago',
            $text
        );
    }

    public function testMonthsAgoDisplay()
    {
        $text = \DS\Component\Text\StringFormat::factory()->prettyDateTimestamp(time() - intval(86400*62));

        $this->assertEquals(
            '2 months ago',
            $text
        );
    }

}
