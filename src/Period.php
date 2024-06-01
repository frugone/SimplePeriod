<?php

namespace Pfrug\Simpleperiod;

use DateTime;
use Pfrug\Simpleperiod\Exception\InvalidPeriodException;
use Pfrug\Simpleperiod\Helpers\StrHelper;

/**
 * Represents a period of time between two DateTime objects.
 *
 * @package Pfrug\Simpleperiod
 * @author P.Frugone <frugone@gmail.com>
 */
class Period
{
    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * @var string
     */
    public $timezone = 'UTC';

    /**
     * @var string
     */
    public $outputFormat = 'Y-m-d H:i:s';

    /**
     * Constructor for the class.
     *
     * @param DateTime $startDate The start date of the period.
     * @param DateTime $endDate The end date of the period.
     *
     * @throws InvalidPeriodException If the start date is after the end date.
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        if ($startDate > $endDate) {
            throw InvalidPeriodException::startDateCannotBeAfterEndDate($startDate, $endDate);
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Creates an instance of Period from the specified dates.
     *
     * @param string|DateTime $startDate The start date of the period.
     * @param string|DateTime $endDate The end date of the period.
     * @return Period
     */
    public static function create($startDate, $endDate = null)
    {
        if (is_string($startDate)) {
            $startDate = new DateTime($startDate);
        }
        if (!$endDate) {
            $endDate = self::now();
        } elseif (is_string($endDate)) {
            $endDate = new DateTime($endDate);
        }
        return new static($startDate, $endDate);
    }

    /**
     * Creates a Period instance with the specified number of minutes.
     *
     * @param int $numberOfMinutesStart The number of minutes to subtract from the end date.
     * @param int $numberOfMinutesEnd The number of minutes to add to the start date.
     * @return Period
     */
    public static function minutes($numberOfMinutesStart, $numberOfMinutesEnd = 0)
    {
        return self::getStartEndDates($numberOfMinutesStart, $numberOfMinutesEnd, 'minutes');
    }

    /**
     * Creates a Period instance with the specified number of hours.
     *
     * @param int $numberOfHoursStart The number of hours to subtract from the end date.
     * @param int $numberOfHoursEnd The number of hours to add to the start date.
     * @return Period
     */
    public static function hours($numberOfHoursStart, $numberOfHoursEnd = 0)
    {
        return self::getStartEndDates($numberOfHoursStart, $numberOfHoursEnd, 'hours');
    }

    /**
     * Creates a Period instance with the specified number of days.
     *
     * @param int $numberOfDays The number of days to subtract from the end date.
     * @param int $numberOfDaysEnd The number of days to add to the start date.
     * @return Period
     */
    public static function days($numberOfDays, $numberOfDaysEnd = 0)
    {
        return self::getStartEndDates($numberOfDays, $numberOfDaysEnd, 'days');
    }

    /**
     * Creates a Period instance with the specified number of weeks.
     *
     * @param int $numberOfWeeks The number of weeks to subtract from the end date.
     * @param int $numberOfWeeksEnd The number of weeks to add to the start date.
     * @return Period
     */
    public static function weeks($numberOfWeeks, $numberOfWeeksEnd = 0)
    {
        return self::getStartEndDates($numberOfWeeks, $numberOfWeeksEnd, 'weeks');
    }

    /**
     * Creates a Period instance with the specified number of months.
     *
     * @param int $numberOfMonths The number of months to subtract from the end date.
     * @param int $numberOfMonthsEnd The number of months to add to the start date.
     * @return Period
     */
    public static function months($numberOfMonths, $numberOfMonthsEnd = 0)
    {
        return self::getStartEndDates($numberOfMonths, $numberOfMonthsEnd, 'month');
    }

    /**
     * Creates a Period instance with the specified number of years.
     *
     * @param int $numberOfYears The number of years to subtract from the end date.
     * @param int $numberOfYearsEnd The number of years to add to the start date.
     * @return Period
     */
    public static function years($numberOfYears, $numberOfYearsEnd = 0)
    {
        return self::getStartEndDates($numberOfYears, $numberOfYearsEnd, 'years');
    }

    /**
     * Converts dates created in a given timezone to another.
     *
     * @param string $tzIn The timezone of the input dates.
     * @param string $tzOut The timezone for the output dates. Default is 'UTC'.
     * @return Period
     */
    public function convertToTimezone($tzIn, $tzOut = 'UTC')
    {
        return $this->toTimezone($tzOut, $tzIn);
    }

    /**
     * Converts dates to the specified timezone.
     *
     * @param string $tzOut The timezone for the output dates.
     * @param string $tzIn The timezone of the input dates.
     * @return Period
     */
    public function toTimezone($tzOut, $tzIn = 'UTC')
    {
        $this->startDate = (new DateTime($this->startDate->format('Y-m-d H:i:s'), new \DateTimeZone($tzIn)))
                                ->setTimezone(new \DateTimeZone($tzOut));

        $this->endDate = (new DateTime($this->endDate->format('Y-m-d H:i:s'), new \DateTimeZone($tzIn)))
                                ->setTimezone(new \DateTimeZone($tzOut));

        return $this;
    }

    /**
     * Gets the difference between startDate and endDate.
     * @param string $method Carbon function to obtain the difference between dates, e.g., diffInMinutes, diffInYear, etc.
     */
    /*public function diff($method)
    {
        return $this->startDate->{$method}($this->endDate);
    }*/

    /*
    public function addHours($numberOfHours)
    {
        $this->startDate->addHours($numberOfHours);
        $this->endDate->addHours($numberOfHours);
    }*/

    /**
     * Obtains the set of dates and times, repeating at regular intervals during the start and end date.
     *
     * @param int $interval Interval of time.
     * @param string $scale Time unit to apply {minutes, days, week, month, year, etc.}
     * @return DatePeriod
     */
    public function getDatePeriodByTime($interval, $scale)
    {
        $step = \DateInterval::createFromDateString("$interval $scale");
        $period = new \DatePeriod($this->startDate, $step, $this->endDate);
        return $period;
    }

    /**
     * Obtains a set of dates and times, repeating at regular intervals during the start and end dates.
     *
     * @param int $steps Number of steps to be obtained.
     * @return DatePeriod
     */
    public function getDatePeriod($steps)
    {
        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
        return $this->getDatePeriodByTime(ceil($diff / $steps), 'seconds');
    }

    /**
     * Returns the difference between startDate and endDate as a string.
     *
     * @return string The formatted interval between startDate and endDate.
     */
    public function getDiffToString()
    {
        $interval = $this->startDate->diff($this->endDate);
        return StrHelper::intervalToString($interval);
    }

    /**
     * Limits the start date.
     * If the limit date is later than the start date, the start date is replaced by the date specified in $limit.
     *
     * @param DateTime $limit The limit date.
     */
    public function limitStartDate(DateTime $limit)
    {
        if ($limit > $this->startDate) {
            $this->startDate = $limit;
        }
    }

    /**
     * Limits the end date.
     * If the limit date is earlier than the end date, the end date is replaced by the one specified in $limit.
     *
     * @param DateTime $limit The limit date.
     */
    public function limitEndDate(DateTime $limit)
    {
        if ($limit < $this->endDate) {
            $this->endDate = $limit;
        }
    }

    /**
     * Gets the current date and time with seconds removed.
     *
     * @return DateTime The current date and time with seconds set to zero.
     */
    private static function now()
    {
        return self::removeAboveSeconds(new DateTime('NOW'));
    }

    /**
     * Subtracts a specified quantity of time from the current date and time.
     *
     * @param int $quantity The quantity of time to subtract.
     * @param string $unit The unit of time (e.g., 'days', 'hours').
     * @return DateTime The modified date and time.
     */
    private static function nowSub($quantity, $unit)
    {
        return (self::now())->modify('- ' . $quantity . ' ' . $unit);
    }

    /**
     * Adds a specified quantity of time to the current date and time.
     *
     * @param int $quantity The quantity of time to add.
     * @param string $unit The unit of time (e.g., 'days', 'hours').
     * @return DateTime The modified date and time.
     */
    private static function nowAdd($quantity, $unit)
    {
        return (self::now())->modify('+ ' . $quantity . ' ' . $unit);
    }

    /**
     * Gets the start and end dates based on the given parameters.
     *
     * @param int $start The start time quantity.
     * @param int|null $end The end time quantity, or null for the current time.
     * @param string $scale The unit of time (e.g., 'days', 'hours').
     * @return static An instance with the calculated start and end dates.
     */
    private static function getStartEndDates($start, $end, $scale)
    {
        $endDate = ($end) ? self::nowAdd($end, $scale) : self::now();

        $startDate = self::nowSub($start, $scale);
        return new static($startDate, $endDate);
    }

    /**
     * Removes the time component above seconds from a DateTimeInterface object.
     *
     * @param DateTimeInterface $dateTime The date object to modify.
     * @return DateTimeInterface The modified date object.
     * @throws InvalidArgumentException If the provided date object is not an instance of DateTime.
     */
    private static function removeAboveSeconds(\DateTimeInterface $dateTime): \DateTimeInterface
    {
        if (!$dateTime instanceof \DateTime) {
            throw new \InvalidArgumentException('Invalid date object.');
        }

        return $dateTime->setTime(
            (int) $dateTime->format('G'), // hours
            (int) $dateTime->format('i'), // minutes
            (int) $dateTime->format('s')  // seconds
        );
    }

    /**
     * Returns startDate and endDate as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->startDate, $this->endDate];
    }

    /**
     * Returns a string representation of the date range.
     *
     * @return string The formatted date range string.
     */
    public function __toString()
    {
        return 'From: ' . $this->startDate->format($this->outputFormat) .
                    ', To: ' . $this->endDate->format($this->outputFormat);
    }

}
