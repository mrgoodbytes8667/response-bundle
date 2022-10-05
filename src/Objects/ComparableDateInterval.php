<?php


namespace Bytes\ResponseBundle\Objects;


use BadMethodCallException;
use Bytes\ResponseBundle\Exception\LargeDateIntervalException;
use DateInterval;
use DateTime;
use Exception;
use InvalidArgumentException;

/**
 * Class ComparableDateInterval
 * Compares date intervals for days, hours, minutes, seconds, and microseconds
 * @package Bytes\ResponseBundle\Objects
 *
 * Adapted from a one time (excluded) PHP patch and some StackOverflow suggestions
 *
 * @method static DateInterval|int getTotalMinutes(DateInterval|int $interval, string $manipulator) Manipulator is a one argument function including round, ceiling, or floor.
 * @method static DateInterval|int getTotalHours(DateInterval|int $interval, string $manipulator) Manipulator is a one argument function including round, ceiling, or floor.
 * @method static DateInterval|int getTotalDays(DateInterval|int $interval, string $manipulator) Manipulator is a one argument function including round, ceiling, or floor.
 */
class ComparableDateInterval extends DateInterval
{
    /**
     * @var int
     */
    const INSTANCE_GREATER_THAN = 1;

    /**
     * @var int
     */
    const INSTANCE_EQUALS = 0;

    /**
     * @var int
     */
    const INSTANCE_LESS_THAN = -1;

    /**
     * ComparableDateInterval constructor.
     * @param string $interval_spec
     * @throws Exception
     */
    public function __construct(string $interval_spec)
    {
        parent::__construct($interval_spec);
    }

    /**
     * Creates a ComparableDateInterval object
     * @param DateInterval|string|int $intervalSpec A DateInterval, a string interval spec or a number of seconds
     * @return static
     * @throws Exception
     */
    public static function create(DateInterval|string|int $intervalSpec): static
    {
        if ($intervalSpec instanceof DateInterval) {
            $intervalSpec = ComparableDateInterval::getTotalSeconds($intervalSpec);
        }

        if (is_int($intervalSpec)) {
            $intervalSpec = sprintf('PT%dS', $intervalSpec);
        }

        return new static($intervalSpec);
    }

    /**
     * @param DateInterval|int $interval
     * @return float
     *
     * @throws LargeDateIntervalException
     *
     * @link https://stackoverflow.com/a/28418969/7906133
     */
    public static function getTotalSeconds(DateInterval|int $interval)
    {
        if ($interval instanceof DateInterval) {
            $iSeconds = $interval->s + ($interval->i * 60) + ($interval->h * 3600);
            if ($interval->d > 0) {
                $iSeconds += ($interval->d * 86400);
            }

            if ($interval->f > 0) {
                $iSeconds += 1 / $interval->f;
            }
            $bak = $iSeconds;

            $iSeconds = static::parseSeconds($interval);
            $iSeconds += static::parseYears($interval);
            $iSeconds += static::parseMonths($interval);
            $iSeconds += static::parseDays($interval);
            $iSeconds += static::parseHours($interval);
            $iSeconds += static::parseMinutes($interval);
            $iSeconds += static::parseMicroseconds($interval);

            if ($interval->invert) {
                $iSeconds *= -1;
            }

            return $iSeconds;
        } else {
            return $interval;
        }
    }

    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseSeconds(DateInterval $interval): int
    {
        return $interval->s;
    }

    /**
     * @param DateInterval $interval
     * @return int
     * @throws LargeDateIntervalException
     */
    protected static function parseYears(DateInterval $interval): int
    {
        if ($interval->y != 0) {
            throw new LargeDateIntervalException($interval, sprintf('The "%s" class cannot handle DateIntervals where there is a interval defined in months or years', __CLASS__));
        }

        return 0;
    }

    /**
     * @param DateInterval $interval
     * @return int
     * @throws LargeDateIntervalException
     */
    protected static function parseMonths(DateInterval $interval): int
    {
        if ($interval->m != 0) {
            throw new LargeDateIntervalException($interval, sprintf('The "%s" class cannot handle DateIntervals where there is a interval defined in months or years', __CLASS__));
        }

        return 0;
    }

    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseDays(DateInterval $interval): int
    {
        if ($interval->d != 0) {
            return ($interval->d * 86400);
        }

        return 0;
    }

    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseHours(DateInterval $interval): int
    {
        if ($interval->h != 0) {
            return ($interval->h * 3600);
        }

        return 0;
    }

    /**
     * @param DateInterval $interval
     * @return int
     */
    protected static function parseMinutes(DateInterval $interval): int
    {
        if ($interval->i != 0) {
            return ($interval->i * 60);
        }

        return 0;
    }

    /**
     * @param DateInterval $interval
     * @return float|int
     */
    protected static function parseMicroseconds(DateInterval $interval): float|int
    {
        if ($interval->f != 0) {
            return 1 / $interval->f;
        }

        return 0;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return DateInterval|int|void
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (count($arguments) !== 2) {
            throw new InvalidArgumentException('Both an interval (DateInterval|int) and a manipulator function name (string, either ceil, floor, or round) are required arguments.');
        }
        $interval = array_shift($arguments);
        $manipulator = array_shift($arguments);
        if (!in_array($manipulator, ['ceil', 'floor', 'round'])) {
            throw new InvalidArgumentException('The manipulator function name argument must be one of ceil, floor, or round.');
        }
        switch (strtolower($name)) {
            case 'gettotalminutes':
                return static::getTotalByTimeType($interval, 'MINUTES', $manipulator);
                break;
            case 'gettotalhours':
                return static::getTotalByTimeType($interval, 'HOURS', $manipulator);
                break;
            case 'gettotaldays':
                return static::getTotalByTimeType($interval, 'DAYS', $manipulator);
                break;
        }
    }

    protected static function getTotalByTimeType(DateInterval|int $interval, string $type, string $manipulator = 'round'): DateInterval|int
    {
        $seconds = static::getTotalSeconds($interval);
        if (is_float($seconds)) {
            $seconds = (int)$seconds;
        } elseif (!is_int($seconds)) {
            return $seconds;
        }

        $divisor = 1;
        switch (strtoupper($type)) {
            case 'MINUTE':
            case 'MINUTES':
            case 'M':
                $divisor = 60;
                break;
            case 'HOUR':
            case 'HOURS':
            case 'H':
                $divisor = 60 * 60;
                break;
            case 'DAY':
            case 'DAYS':
            case 'D':
                $divisor = 24 * 60 * 60;
                break;
            default:
                throw new BadMethodCallException(sprintf('Type "%s" is not supported.', $type));
                break;
        }

        return (int)$manipulator($seconds / $divisor);
    }

    /**
     * @param DateInterval|int $seconds
     * @return DateInterval
     */
    public static function normalizeToDateInterval(DateInterval|int $seconds): DateInterval
    {
        if ($seconds instanceof DateInterval) {
            return $seconds;
        }

        return ComparableDateInterval::secondsToInterval($seconds);
    }

    /**
     * @param DateInterval|int $seconds
     * @return int
     */
    public static function normalizeToSeconds(DateInterval|int $seconds): int
    {
        if(is_int($seconds)) {
            return $seconds;
        }

        return ComparableDateInterval::getTotalSeconds($seconds);
    }

    /**
     * @param int $seconds
     * @return DateInterval
     */
    public static function secondsToInterval(int $seconds): DateInterval
    {
        $dtF = new DateTime('@0');
        $dtT = new DateTime("@$seconds");
        return $dtF->diff($dtT);
    }

    /**
     * @param DateInterval $interval
     * @return bool
     */
    public function equals(DateInterval $interval): bool
    {
        return $this->compare($interval) === self::INSTANCE_EQUALS;
    }

    /**
     * Compares the instance DateInterval to the param DateInterval
     * @param DateInterval $oDateInterval
     * @return int Returns 1 if the param is greater, 0 if they are equal, -1 if it is less
     */
    public function compare(DateInterval $oDateInterval)
    {
        $oMyTotalSeconds = $this->getIntervalSeconds();
        $oYourTotalSeconds = static::getTotalSeconds($oDateInterval);

        if ($oMyTotalSeconds < $oYourTotalSeconds) {
            return self::INSTANCE_LESS_THAN;
        } elseif ($oMyTotalSeconds == $oYourTotalSeconds) {
            return self::INSTANCE_EQUALS;
        }

        return self::INSTANCE_GREATER_THAN;
    }

    /**
     * @return int
     */
    protected function getIntervalSeconds()
    {
        return static::getTotalSeconds($this);
    }
}
