<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime\format;

use froq\datetime\{DateTime, DateTimeZone};
use froq\datetime\locale\{Locale, Intl};

/**
 * A date/time formatter class with basic functionalities and locale support.
 *
 * @package froq\datetime\format
 * @class   froq\datetime\format\Formatter
 * @author  Kerem Güneş
 * @since   6.0
 */
class Formatter
{
    /** Format pattern. */
    private string $format = '';

    /** Locale info. */
    private string $locale = '';

    /** Intl map. */
    private array $intl = [];

    /** Locale format map. */
    private array $map;

    /**
     * Constructor.
     *
     * @param string|froq\datetime\format\Format|null $format
     * @param string|froq\datetime\locale\Locale|null $locale
     * @param array|froq\datetime\locale\Intl|null    $intl
     */
    public function __construct(string|Format $format = null, string|Locale $locale = null, array|Intl $intl = null)
    {
        $format && $this->setFormat($format);
        $locale && $this->setLocale($locale);
        $intl   && $this->setIntl($intl);
    }

    /**
     * Set format.
     *
     * @param  string|froq\datetime\format\Format $format
     * @return self
     */
    public function setFormat(string|Format $format): self
    {
        $this->format = (string) $format;

        return $this;
    }

    /**
     * Get format.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Set locale.
     *
     * @param  string|froq\datetime\locale\Locale $locale
     * @return self
     */
    public function setLocale(string|Locale $locale): self
    {
        $this->locale = (string) $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set intl.
     *
     * @param  array|froq\datetime\locale\Intl $intl
     * @return self
     */
    public function setIntl(array|Intl $intl): self
    {
        $this->intl = []; // Reset. @important

        foreach ($intl as $locale => $translation) {
            $this->intl[$locale] = $translation;
        }

        return $this;
    }

    /**
     * Get intl.
     *
     * @return array
     */
    public function getIntl(): array
    {
        return $this->intl;
    }

    /**
     * Format.
     *
     * @param  int|float|string|DateTimeInterface      $when
     * @param  string|froq\datetime\format\Format|null $format
     * @return string
     * @throws froq\datetime\format\FormatException
     */
    public function format(int|float|string|\DateTimeInterface $when, string|Format $format = null): string
    {
        $format = (string) $format ?: $this->format ?: throw FormatException::forEmptyFormat();

        if (!$when instanceof \DateTimeInterface) {
            $when = new DateTime($when);
        }

        // Safe for calls in DateTime instances (recursion).
        return date_format($when, $format);
    }

    /**
     * Format - UTC.
     *
     * @param  int|float|string|DateTimeInterface      $when
     * @param  string|froq\datetime\format\Format|null $format
     * @return string
     * @throws froq\datetime\format\FormatException
     */
    public function formatUtc(int|float|string|\DateTimeInterface $when, string|Format $format = null): string
    {
        $format = (string) $format ?: $this->format ?: throw FormatException::forEmptyFormat();

        if (!$when instanceof \DateTimeInterface) {
            $when = new DateTime($when);
        }

        // UTC check.
        if ($when->getOffset() <> 0) {
            $when = clone $when; // Keep original.
            $when->setTimezone(new \DateTimeZone('UTC'));
        }

        // Safe for calls in DateTime instances (recursion).
        return date_format($when, $format);
    }

    /**
     * Format locale.
     *
     * @param  int|float|string|DateTimeInterface      $when
     * @param  string|froq\datetime\format\Format|null $format
     * @return string
     * @throws froq\datetime\format\FormatException
     * @thanks https://gist.github.com/bohwaz/42fc223031e2b2dd2585aab159a20f30
     */
    public function formatLocale(int|float|string|\DateTimeInterface $when, string|Format $format = null): string
    {
        $format = (string) $format ?: $this->format ?: throw FormatException::forEmptyFormat();

        if (!$when instanceof \DateTimeInterface) {
            $when = new DateTime($when);
        }

        $this->createMap();

        $out = preg_replace_callback('~(?<!%)(%[a-z])~i', function ($match) use ($when) {
            if ($match[1] === '%n') return "\n";
            if ($match[1] === '%t') return "\t";

            $replace = $this->map[$match[1]] ?? throw FormatException::forInvalidFormat($match[1]);

            return is_string($replace) ? date_format($when, $replace) : $replace($when, $match[1]);
        }, $format);

        $out = str_replace('%%', '%', $out);

        return $out;
    }

    /**
     * Format locale - UTC.
     *
     * @param  int|float|string|DateTimeInterface      $when
     * @param  string|froq\datetime\format\Format|null $format
     * @return string
     * @causes froq\datetime\format\FormatException
     */
    public function formatLocaleUtc(int|float|string|\DateTimeInterface $when, string|Format $format = null): string
    {
        if (!$when instanceof \DateTimeInterface) {
            $when = new DateTime($when);
        }

        // UTC check.
        if ($when->getOffset() <> 0) {
            $when = clone $when; // Keep original.
            $when->setTimezone(new \DateTimeZone('UTC'));
        }

        return $this->formatLocale($when, $format);
    }

    /**
     * Format ago by locale.
     *
     * @param  int|float|string|\DateTimeInterface     $when
     * @param  string|\DateTimeZone|null               $where
     * @param  string|froq\datetime\format\Format|null $format Used for only more than 7 days.
     * @param  bool                                    $showTime
     * @return string
     */
    public function formatAgo(int|float|string|\DateTimeInterface $when, string|\DateTimeZone $where = null,
        string|Format $format = null, bool $showTime = true): string
    {
        $then = new \DateTime();

        if (!$when instanceof \DateTimeInterface) {
            $when = new DateTime($when);
            $where ??= $when->getTimezone();
        }
        if ($where !== null) {
            if (!$where instanceof \DateTimeZone) {
                $where = new DateTimeZone($where);
            }
            $then->setTimezone($where);
        }

        // Update then's timestamp.
        $then->setTimestamp($when->getTimestamp());

        // Locale format macro (for dynamic formatting).
        $formatLocale = fn($f) => $this->formatLocale($then, $f);

        // Get diff from then by now.
        switch ($diff = $then->diff(new \DateTime('', $where))) {
            // Yesterday.
            case ($diff->days === 1):
                $yesterday = $this->translate('yesterday', '', 'Yesterday');
                return $showTime ? $yesterday .', '. $formatLocale('%H:%M') : $yesterday;

            // 2-7 days.
            case ($diff->days >= 2 && $diff->days <= 7):
                return $showTime ? $formatLocale('%A, %H:%M') : $formatLocale('%A');

            // Week & more.
            case ($diff->days > 7):
                return $formatLocale($format ?? ($showTime ? Format::AGO : Format::AGO_SHORT));

            // Hours, minutes, now.
            default:
                if ($diff->h >= 1) {
                    return $diff->h .' '. (
                        $diff->h === 1 ? $this->translate('hour', '', 'hour')
                                       : $this->translate('hours', '', 'hours')
                    );
                }

                if ($diff->i >= 1) {
                    return $diff->i .' '. (
                        $diff->i === 1 ? $this->translate('minute', '', 'minute')
                                       : $this->translate('minutes', '', 'minutes')
                    );
                }

                return $this->translate('now', '', 'Just now');
        }
    }

    /**
     * Create a static format map for once.
     */
    private function createMap(): void
    {
        $this->map ??= [
            // Day.
            '%A' => fn($dt) => $this->getDay($dt),
            '%a' => fn($dt) => $this->getDayAbbr($dt),
            '%d' => 'd',
            '%e' => 'j',
            '%j' => fn($dt) => $this->getDayOfYear($dt),
            '%u' => 'N',
            '%w' => 'w',

            // Week.
            '%U' => fn($dt) => $this->getWeekOfYear($dt, 'Sunday'),
            '%W' => fn($dt) => $this->getWeekOfYear($dt, 'Monday'),
            '%V' => 'W',

            // Month.
            '%B' => fn($dt) => $this->getMonth($dt),
            '%b' => fn($dt) => $this->getMonthAbbr($dt),
            '%h' => fn($dt) => $this->getMonthAbbr($dt),
            '%m' => 'm',

            // Year.
            '%C' => fn($dt) => $this->getCentury($dt),
            '%g' => fn($dt) => $this->getShortYear($dt),
            '%G' => 'o',
            '%y' => 'y',
            '%Y' => 'Y',

            // Time.
            '%H' => 'H',
            '%k' => 'G',
            '%I' => 'h',
            '%l' => 'g',
            '%M' => 'i',
            '%p' => fn($dt) => $this->getPeriod($dt, 'upper'),
            '%P' => fn($dt) => $this->getPeriod($dt, 'lower'),
            '%r' => fn($dt) => $this->getTimeWithPeriod($dt),
            '%R' => 'H:i',
            '%S' => 's',
            '%T' => 'H:i:s',

            // Zone.
            '%z' => 'O',
            '%Z' => 'T',

            // Stamp.
            '%D' => 'm/d/y',
            '%F' => 'Y-m-d',
            '%s' => 'U',

            // Sorry..
            '%c' => fn($dt) => $this->exec($dt, '+%c'),
            '%x' => fn($dt) => $this->exec($dt, '+%x'),
            '%X' => fn($dt) => $this->exec($dt, '+%X'),
        ];
    }

    /**
     * Get day stuff (format: %A, %a).
     */
    private function getDay(\DateTimeInterface $dt): string
    {
        $subkey = date_format($dt, 'N') - 1;
        return $this->translate('days', $subkey, date_format($dt, 'l'));
    }
    private function getDayAbbr(\DateTimeInterface $dt): string
    {
        $subkey = date_format($dt, 'N') - 1;
        $ret = $this->translate('days', $subkey, date_format($dt, 'D'));

        // Some exceptions.
        if (str_starts_with($this->locale, 'tr_')) {
            return match ($subkey) {
                0 => 'Pzt', 5 => 'Cmt',
                default => mb_substr($ret, 0, 3)
            };
        }
        if (str_starts_with($this->locale, 'de_')) {
            return mb_substr($ret, 0, 2);
        }

        return mb_substr($ret, 0, 3);
    }

    /**
     * Get week of year (format: %j).
     */
    private function getDayOfYear(\DateTimeInterface $dt): string
    {
        return sprintf('%03d', date_format($dt, 'z') + 1);
    }

    /**
     * Get week of year (format: %U, %W).
     */
    private function getWeekOfYear(\DateTimeInterface $dt, string $day): string
    {
        $td = new \DateTime(date_format($dt, 'Y') .'-01 '. $day, date_timezone_get($dt));
        return sprintf('%02d', intval((date_format($dt, 'z') - $td->format('z')) / 7) + 1);
    }

    /**
     * Get month stuff (format: %B, %b, %h).
     */
    private function getMonth(\DateTimeInterface $dt): string
    {
        $subkey = date_format($dt, 'n') - 1;
        return $this->translate('months', $subkey, date_format($dt, 'F'));
    }
    private function getMonthAbbr(\DateTimeInterface $dt): string
    {
        $subkey = date_format($dt, 'n') - 1;
        return mb_substr($this->translate('months', $subkey, date_format($dt, 'M')), 0, 3);
    }

    /**
     * Get century (format: %C).
     */
    private function getCentury(\DateTimeInterface $dt): string
    {
        return (string) intval(date_format($dt, 'Y') / 100);
    }

    /**
     * Get short year (format: %g).
     */
    private function getShortYear(\DateTimeInterface $dt): string
    {
        return substr(date_format($dt, 'o'), -2);
    }

    /**
     * Get period, am/pm stuff (format: %p, %P, %r).
     */
    private function getPeriod(\DateTimeInterface $dt, string $case): string
    {
        $subkey = date_format($dt, 'a');
        if ($case === 'upper') {
            return mb_strtoupper($this->translate('periods', $subkey, $subkey));
        }
        return mb_strtolower($this->translate('periods', $subkey, $subkey));
    }

    /**
     * Get time with period (format: %r).
     */
    private function getTimeWithPeriod(\DateTimeInterface $dt): string
    {
        return trim(date_format($dt, 'h:i:s') .' '. $this->getPeriod($dt, 'upper'));
    }

    /**
     * Linux, my saver..
     */
    private function exec(\DateTimeInterface $dt, string $format): string
    {
        try {
            $ret = exec(sprintf(
                'LC_TIME=%s TZ=%s date -d %s %s 2>/dev/null',
                escapeshellarg($this->locale),
                escapeshellarg(date_format($dt, 'P')),
                escapeshellarg(date_format($dt, 'Y-m-d H:i:s')),
                escapeshellarg($format)
            ));

            // Somehow, zone id not added in exec.
            if ($ret && $format === '+%c' && !preg_match('~(GMT| [-+:][\d]+)$~', $ret)) {
                $ret .= ' '. (date_offset_get($dt) === 0 ? 'GMT' : date_format($dt, 'T'));
            }

            return $ret;
        } catch (\Error) {
            // Fallback.
            return match ($format) {
                '+%c' => date_format($dt, 'D d M Y H:i:s') .' '. (
                    date_offset_get($dt) === 0 ? 'GMT' : date_format($dt, 'T')
                ),
                '+%x' => date_format($dt, 'm/d/Y'),
                '+%X' => date_format($dt, 'h:i:s A'),
            };
        }
    }

    /**
     * Basic translation method for days/months and am/pm periods.
     */
    private function translate(string $key, string|int $subkey, string $default): string
    {
        // No am/pm use, I found, so far..
        if (in_array($subkey, ['am', 'pm'], true)
            && !preg_match('~^(en|tr)_~', $this->locale)) {
            return '';
        }

        // Subkey for periods, days, months (eg: periods[am]).
        return ($subkey !== '')
             ? $this->intl[$this->locale][$key][$subkey] ?? $default
             : $this->intl[$this->locale][$key]          ?? $default;
    }
}
