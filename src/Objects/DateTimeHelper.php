<?php

namespace Bytes\ResponseBundle\Objects;

use BadMethodCallException;
use Bytes\ResponseBundle\Enums\DayOfWeek;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Illuminate\Support\Arr;
use JetBrains\PhpStorm\ArrayShape;

use function Symfony\Component\String\u;

/**
 * @method static int                    getYearFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static int                    getMonthFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static int                    getDayFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static int                    getHourFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static int                    getMinuteFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static int                    getSecondFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static mixed                  getTimezoneFromDate(DateTimeInterface $dateTime, ?string $datePart = null)
 * @method static int                    getDayOfWeekFromDate(DateTimeInterface $dateTime, ?string $datePart = null) 0 (for Sunday) through 6 (for Saturday)
 * @method static int                    getDayOfWeekISO8601FromDate(DateTimeInterface $dateTime, ?string $datePart = null) 1 (for Monday) through 7 (for Sunday)
 * @method static DateTimeImmutable|null toDoctrine(?DateTimeInterface $value)
 */
class DateTimeHelper
{
    /**
     * @var string
     */
    public const FORMAT_YMD = 'Y-m-d';

    /**
     * @var string
     */
    public const FORMAT_DOCTRINE = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    public const FORMAT_EASYADMIN_FILTER = 'Y-m-d\TH:i';

    /**
     * @var string
     */
    public const FORMAT_EASYADMIN_SHORT = 'n/j @ g:i a T';

    /**
     * @var string
     */
    public const TIMEZONE_UTC = 'UTC';

    /**
     * @var string
     */
    public const TIMEZONE_CHICAGO = 'America/Chicago';

    /**
     * @var int
     */
    public const MINUTES_IN_DAY = 1400;

    /**
     * @var int
     */
    public const SECONDS_IN_HOUR = 3600;

    /**
     * @var int
     */
    public const SECONDS_IN_DAY = 86400;

    /**
     * @var int
     */
    public const SECONDS_IN_WEEK = 604800;

    /**
     * @throws Exception
     */
    public static function buildDateTimeFromToday(int $hour, int $minute = 0, int $seconds = 0, DateTimeZone|string $nowTimeZone = 'America/Chicago', DateTimeZone|string $thenTimeZone = 'America/Chicago', $nowInput = 'now'): DateTime
    {
        $nowTimeZone = $nowTimeZone instanceof DateTimeZone ? $nowTimeZone : new DateTimeZone($nowTimeZone);
        $now = new DateTime($nowInput, $nowTimeZone);
        $then = new DateTime(sprintf('%sT%s:%s:%s%s', $now->format(self::FORMAT_YMD), str_pad($hour, 2, '0'), str_pad($minute, 2, '0'), str_pad($seconds, 2, '0'), $now->format('P')));

        $thenTimeZone = $thenTimeZone instanceof DateTimeZone ? $thenTimeZone : new DateTimeZone($thenTimeZone);
        if ($nowTimeZone->getOffset($now) !== $thenTimeZone->getOffset($now)) {
            $then = $then->setTimezone($thenTimeZone);
        }

        return $then;
    }

    public static function createTodaysDateWithSuppliedTime(DateTimeInterface $then = null, string $tz = 'America/Chicago', DateTimeInterface $now = null, int $hour = null, int $minute = null, int $second = null): DateTime
    {
        if (is_null($then)) {
            $then = new DateTimeImmutable();
        }
        $then = $then->setTimezone(new DateTimeZone($tz));

        return static::create(now: $now, hour: $hour ?? static::getHourFromDate($then), minute: $minute ?? static::getMinuteFromDate($then), second: $second ?? static::getSecondFromDate($then));
    }

    /**
     * @throws Exception
     */
    public static function create(string $tz = 'America/Chicago', DateTimeInterface $now = null, int $year = null, int $month = null, int $day = null, int $hour = null, int $minute = null, int $second = null): DateTime|bool
    {
        $timeZone = new DateTimeZone($tz);
        $now ??= new DateTime(timezone: $timeZone);
        if (is_null($year)) {
            $year = static::getYearFromDate($now);
        }

        if (is_null($month)) {
            $month = static::getMonthFromDate($now);
        }

        if (is_null($day)) {
            $day = static::getDayFromDate($now);
        }

        if (is_null($hour)) {
            $hour = static::getHourFromDate($now);
        }

        if (is_null($minute)) {
            $minute = static::getMinuteFromDate($now);
        }

        if (is_null($second)) {
            $second = static::getSecondFromDate($now);
        }

        $tzOffset = $timeZone->getOffset(new DateTime(timezone: $timeZone));
        $tzOffset /= 3600;
        $tzSign = '+';
        if ($tzOffset < 0) {
            $tzSign = '-';
            $tzOffset = abs($tzOffset);
        }

        return DateTime::createFromFormat(DateTimeInterface::ATOM, sprintf('%s-%s-%sT%s:%s:%s%s%s:00', str_pad($year, 4, '0', STR_PAD_LEFT), str_pad($month, 2, '0', STR_PAD_LEFT), str_pad($day, 2, '0', STR_PAD_LEFT), str_pad($hour, 2, '0', STR_PAD_LEFT), str_pad($minute, 2, '0', STR_PAD_LEFT), str_pad($second, 2, '0', STR_PAD_LEFT), $tzSign, str_pad($tzOffset, 2, '0', STR_PAD_LEFT)));
    }

    /**
     * @throws Exception
     */
    public static function createImmutable(string $tz = 'America/Chicago', DateTimeInterface $now = null, int $year = null, int $month = null, int $day = null, int $hour = null, int $minute = null, int $second = null): ?DateTimeImmutable
    {
        $dateTime = static::create($tz, $now, $year, $month, $day, $hour, $minute, $second);

        return $dateTime instanceof DateTimeInterface ? static::toImmutable($dateTime) : null;
    }

    public static function nowAdd(DateInterval $interval, DateTimeInterface $now = null): DateTimeImmutable
    {
        if (!is_null($now)) {
            $now = DateTimeImmutable::createFromInterface($now);
        } else {
            $now = static::getNowUTC();
        }

        return $now->add($interval);
    }

    public static function nowDiff(?DateTimeInterface $then): ?DateInterval
    {
        if (is_null($then)) {
            return null;
        }

        return static::getNowUTC()->diff($then);
    }

    /**
     * Gets the days from the diff, ensuring negative diffs return a negative value.
     */
    public static function nowDiffDays(?DateTimeInterface $then): ?int
    {
        if (is_null($then)) {
            return null;
        }
        $diff = static::nowDiff($then);
        $days = $diff->days;
        if (0 !== $diff->invert) {
            $days *= -1;
        }

        return $days;
    }

    public static function getNowUTC(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTimezone(static::getTimeZoneUTC());
    }

    public static function getTimeZoneUTC(): DateTimeZone
    {
        return new DateTimeZone(static::TIMEZONE_UTC);
    }

    /**
     * is triggered when invoking inaccessible methods in a static context.
     *
     * @see https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if ('toDoctrine' === $name && count($arguments) > 0 && array_key_exists(0, $arguments)) {
            return static::toImmutableUTC($arguments[0]);
        }
        $getFromDatePart = u($name)->before('FromDate')->after('get')->lower()->toString();
        $toInt = true;
        switch ($getFromDatePart) {
            case 'year':
            case 'years':
                $part = 'Y';
                break;
            case 'month':
            case 'months':
                $part = 'm';
                break;
            case 'day':
            case 'days':
                $part = 'd';
                break;
            case 'hour':
            case 'hours':
                $part = 'G';
                break;
            case 'minute':
            case 'minutes':
                $part = 'i';
                break;
            case 'second':
            case 'seconds':
                $part = 's';
                break;
            case 'timezone':
                $part = 'p';
                $toInt = false;
                break;
            case 'dayofweek':
                $part = 'w';
                break;
            case 'dayofweekiso8601':
                $part = 'N';
                break;
            default:
                return null;
        }

        /** @var DateTimeInterface $date */
        $date = Arr::first($arguments);
        $formatted = $date->format($part);
        if ($toInt) {
            return (int) $formatted;
        } else {
            return $formatted;
        }
    }

    public static function convertTimeTypeToTimeType(int $fromValue, string $fromType, string $toType): int
    {
        switch (u($fromType)->trim()->lower()->slice(0, 1)->toString()) {
            case 'd':
                $fromValue *= DateTimeHelper::SECONDS_IN_DAY;
                break;
            case 'h':
                $fromValue *= 3600;
                break;
            case 'm':
                $fromValue *= 60;
                break;
            case 's':
                break;
            default:
                throw new BadMethodCallException();
                break;
        }

        switch (u($toType)->trim()->lower()->slice(0, 1)->toString()) {
            case 'd':
                $fromValue /= DateTimeHelper::SECONDS_IN_DAY;
                break;
            case 'h':
                $fromValue /= 3600;
                break;
            case 'm':
                $fromValue /= 60;
                break;
            case 's':
                break;
            default:
                throw new BadMethodCallException();
                break;
        }

        return round($fromValue);
    }

    public static function getNowChicago(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTimezone(static::getTimeZoneChicago());
    }

    public static function getTimeZoneChicago(): DateTimeZone
    {
        return new DateTimeZone(static::TIMEZONE_CHICAGO);
    }

    public static function toImmutableUTC(?DateTimeInterface $since): ?DateTimeImmutable
    {
        return static::toImmutable($since)?->setTimezone(static::getTimeZoneUTC());
    }

    public static function toImmutable(?DateTimeInterface $since): ?DateTimeImmutable
    {
        return !is_null($since) ? DateTimeImmutable::createFromInterface($since) : null;
    }

    /**
     * @throws Exception
     */
    public static function toDoctrineNoSeconds(?DateTimeInterface $value): ?DateTimeImmutable
    {
        if (!is_null($value)) {
            if (0 != DateTimeHelper::getSecondFromDate($value)) {
                $value = DateTimeImmutable::createFromInterface($value)->setTimezone(DateTimeHelper::getTimeZoneChicago());
                $value = DateTimeHelper::create(now: $value, second: 0);
                $value = DateTimeHelper::toImmutableUTC($value);
            } elseif (!($value instanceof DateTimeImmutable)) {
                $value = DateTimeHelper::toImmutableUTC($value);
            }
        }

        return $value;
    }

    /**
     * @param int|int[] $allowedMinutes
     *
     * @throws Exception
     */
    public static function reduceMinutesNoTensToValue(DateTimeInterface $dateTime, int|array $allowedMinutes): DateTimeInterface
    {
        list('minute' => $minute, 'allowed' => $returnImmediately, 'allowedMinutes' => $allowedMinutes) = static::isMinutesNoTensToValue($dateTime, $allowedMinutes);
        if ($returnImmediately) {
            return $dateTime;
        }

        $offset = 0;

        do {
            ++$offset;
            --$minute;
            if ($minute < 0) {
                $minute = 9;
            }
        } while (!in_array($minute, $allowedMinutes));

        return $dateTime->sub(new DateInterval('PT'.$offset.'M'));
    }

    /**
     * @return array{minute: int, allowed: bool, allowedMinutes: array}
     */
    public static function isMinutesNoTensToValue(DateTimeInterface $dateTime, int|array $allowedMinutes): array
    {
        $allowedMinutes = Arr::wrap($allowedMinutes);
        $minute = DateTimeHelper::getMinuteFromDate($dateTime) % 10;
        $return = [
            'minute' => $minute,
            'allowed' => false,
            'allowedMinutes' => $allowedMinutes,
        ];
        if (in_array($minute, $allowedMinutes)) {
            $return['allowed'] = true;
        }

        return $return;
    }

    /**
     * @param int|int[] $allowedMinutes
     *
     * @throws Exception
     */
    public static function increaseMinutesNoTensToValue(DateTimeInterface $dateTime, int|array $allowedMinutes): DateTimeInterface
    {
        list('minute' => $minute, 'allowed' => $returnImmediately, 'allowedMinutes' => $allowedMinutes) = static::isMinutesNoTensToValue($dateTime, $allowedMinutes);
        if ($returnImmediately) {
            return $dateTime;
        }

        $offset = 0;

        do {
            ++$offset;
            ++$minute;
            if ($minute > 9) {
                $minute = 0;
            }
        } while (!in_array($minute, $allowedMinutes));

        return $dateTime->add(new DateInterval('PT'.$offset.'M'));
    }

    public static function countWeekdaysInRange(DateTimeInterface $start, DateTimeInterface $end): int
    {
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);

        return count(Arr::where(iterator_to_array($period), function (DateTimeInterface $dt) {
            return !in_array(static::getDayOfWeekFromDate($dt), [0, 6]);
        }) ?? []);
    }

    /**
     * @throws Exception
     */
    public static function countWeeksInRange(DateTimeInterface $start, DateTimeInterface $end, bool $includePartialWeek = false): int
    {
        $start = static::create(now: $start, hour: 0, minute: 0, second: 0);
        $end = static::create(now: $end, hour: 23, minute: 59, second: 59);
        $interval = DateInterval::createFromDateString('7 days');
        $period = new DatePeriod($start, $interval, $end);

        $weeksBeginningOn = iterator_to_array($period);

        return count($weeksBeginningOn) - ($includePartialWeek ? 0 : 1);
    }

    /**
     * @throws Exception
     */
    public static function isBetween(DateTimeInterface $from, DateTimeInterface|DateInterval $to, DateTimeInterface $date = null, bool $inclusive = true, bool $stripSeconds = true): bool
    {
        $date = static::toImmutableChicago($date ?? new DateTime());
        $from = static::toImmutableChicago($from);
        if ($to instanceof DateInterval) {
            $to = $from->add($to);
        } else {
            $to = static::toImmutableChicago($to);
        }

        if ($stripSeconds) {
            $date = static::create(now: $date, second: 0);
            $from = static::create(now: $from, second: 0);
            $to = static::create(now: $to, second: 0);
        }

        if ($inclusive) {
            return $date >= $from && $date <= $to;
        } else {
            return $date > $from && $date < $to;
        }
    }

    public static function toImmutableChicago(?DateTimeInterface $since): ?DateTimeImmutable
    {
        return static::toImmutable($since)?->setTimezone(static::getTimeZoneChicago());
    }

    /**
     * @param int[]|int $daysOfWeek
     */
    public static function isInDaysOfWeek(array|int $daysOfWeek, DateTimeInterface $date = null): bool
    {
        $date = static::toImmutableChicago($date ?? new DateTime());

        return in_array(static::getDayOfWeekFromDate($date), Arr::wrap($daysOfWeek));
    }

    /**
     * Converts a time-only field to today's date + time.
     */
    public static function convertTimeToDateTime(?DateTimeInterface $time, DateTimeZone $timeZone = null, DateTimeInterface $day = null): ?DateTimeInterface
    {
        try {
            if (is_null($time)) {
                return null;
            }

            $date = DateTimeHelper::create(tz: $time->getTimezone()->getName(), now: DateTimeHelper::toImmutableChicago($day) ?? DateTimeHelper::nowChicago(), hour: DateTimeHelper::getHourFromDate($time), minute: DateTimeHelper::getMinuteFromDate($time), second: 0)->setTimezone(DateTimeHelper::getTimeZoneChicago());
            if (!is_null($timeZone)) {
                $date = $date->setTimezone($timeZone);
            }

            return $date;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @throws Exception
     */
    public static function parseRequestToDate(?string $input, DateTimeInterface $now = null): ?DateTime
    {
        if (empty($input)) {
            return null;
        }

        $u = u($input)->trim()->upper();
        if ($u->startsWith(['+', '-', 'P']) && 1 === preg_match('/(P([1-9]|[1-9]+[0-9])([YMWD])(T[0-9]+[HMS])?)/', $u)) {
            $interval = static::parseRequestToDateInterval($input);

            $return = ($now ?? new DateTime())->add($interval);
        } else {
            $return = new DateTime($input);
        }

        if (empty($return)) {
            return null;
        }

        return new DateTime($return->format('Y-m-d'));
    }

    public static function parseRequestToDateInterval(?string $input): ?DateInterval
    {
        if (empty($input)) {
            return null;
        }

        $u = u($input)->trim()->upper();
        if ($u->startsWith(['+', '-', 'P']) && 1 === preg_match('/(P([1-9]|[1-9]+[0-9])([YMWD])(T[0-9]+[HMS])?)/', $u)) {
            $u = $u->beforeLast('T')->afterLast('+');
            $interval = new DateInterval($u->after(['+', '-']));
            if ($u->startsWith('-')) {
                $interval->invert = 1;
            } elseif ($u->startsWith('+')) {
                $interval->invert = 0;
            }

            return $interval;
        } else {
            return new DateInterval($input);
        }
    }

    public static function getDayOfWeekEnumFromDate(DateTimeInterface $dateTime): DayOfWeek
    {
        return DayOfWeek::from(static::getDayOfWeekFromDate(dateTime: $dateTime));
    }
}
