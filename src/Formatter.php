<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use froq\common\trait\FactoryTrait;
use DateTime;

/**
 * A simple formatter class with basic functionalities for date/time formatting.
 *
 * @package froq\date
 * @object  froq\date\Formatter
 * @author  Kerem Güneş
 * @since   6.0
 */
class Formatter
{
    use FactoryTrait;

    /** @var array */
    protected array $intl;

    /** @var string */
    protected string $format;

    /** @var string */
    protected string $locale;

    /** @var array */
    private array $map;

    /**
     * Constructor.
     *
     * @param array|froq\date\Intl|null    $intl
     * @param string|froq\date\Format|null $format
     * @param string|froq\date\Locale|null $locale
     */
    public function __construct(array|Intl $intl = null, string|Format $format = null, string|Locale $locale = null)
    {
        $this->setIntl($intl ?: []);
        $this->setFormat($format ?: '');
        $this->setLocale($locale ?: Locale::default());
    }

    /**
     * Set intl.
     *
     * @param  array|Intl $intl
     * @return self
     */
    public function setIntl(array|Intl $intl): self
    {
        $this->intl = []; // Clean up / also set.

        foreach ($intl as $locale => $translation) {
            // Set charset to UTF-8 if none charset.
            if (!str_contains($locale, '.')) {
                $locale .= '.UTF-8';
            }

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
     * Set format.
     *
     * @param  string|froq\date\Format $format
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
     * @param  string|froq\date\Locale $locale
     * @return self
     */
    public function setLocale(string|Locale $locale): self
    {
        $locale = (string) $locale;

        // Set charset to UTF-8 if none charset.
        if (!str_contains($locale, '.')) {
            $locale .= '.UTF-8';
        }

        $this->locale = $locale;

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
     * Format.
     *
     * @param  string|int|float|froq\date\Date|DateTime $when
     * @param  string|froq\date\Format|null             $format
     * @return string
     * @throws froq\date\FormatterException
     * @thanks https://gist.github.com/bohwaz/42fc223031e2b2dd2585aab159a20f30
     */
    public function format(string|int|float|Date|DateTime $when, string|Format $format = null): string
    {
        $format = (string) $format ?: $this->format ?:
            throw new FormatterException('No format yet, call setFormat() or pass $format argument');

        if (!$when instanceof Date) {
            if (is_object($when)) {
                $when = new Date((float) $when->format('U.u'), $when->getTimeZone()->getName());
            } else {
                $when = new Date($when);
            }
        }

        $this->createMap();

        $out = preg_replace_callback('~(?<!%)(%[a-z])~i', function ($match) use ($when) {
            if ($match[1] == '%n') return "\n";
            if ($match[1] == '%t') return "\t";

            $replace = $this->map[$match[1]] ??
                throw new FormatterException('Invalid format: `%s`', $match[1]);

            // So, why man? Dunno..
            [$date, $when] = [$when, null];

            return is_string($replace) ? $date->format($replace) : $replace($date, $match[1]);
        }, $format);

        $out = str_replace('%%', '%', $out);

        return $out;
    }

    /**
     * Format UTC.
     *
     * @param  string|int|float|froq\date\Date|DateTime $when
     * @param  string|froq\date\Format|null             $format
     * @return string
     * @causes froq\date\FormatterException
     */
    public function formatUtc(string|int|float|Date|DateTime $when, string|Format $format = null): string
    {
        if (!$when instanceof UtcDate) {
            if (is_object($when)) {
                $when = new UtcDate((float) $when->format('U.u'));
            } else {
                $when = new UtcDate($when);
            }
        }

        return $this->format($when, $format);
    }

    /**
     * Create a static format map for once.
     */
    private function createMap(): void
    {
        $this->map ??= [
            // Day.
            '%A' => fn($date) => $this->getDay($date),
            '%a' => fn($date) => $this->getDayAbbr($date),
            '%d' => 'd',
            '%e' => 'j',
            '%j' => fn($date) => $this->getDayOfYear($date),
            '%u' => 'N',
            '%w' => 'w',

            // Week.
            '%U' => fn($date) => $this->getWeekOfYear($date, 'Sunday'),
            '%W' => fn($date) => $this->getWeekOfYear($date, 'Monday'),
            '%V' => 'W',

            // Month.
            '%B' => fn($date) => $this->getMonth($date),
            '%b' => fn($date) => $this->getMonthAbbr($date),
            '%h' => fn($date) => $this->getMonthAbbr($date),
            '%m' => 'm',

            // Year.
            '%C' => fn($date) => $this->getCentury($date),
            '%g' => fn($date) => $this->getShortYear($date),
            '%G' => 'o',
            '%y' => 'y',
            '%Y' => 'Y',

            // Time.
            '%H' => 'H',
            '%k' => 'G',
            '%I' => 'h',
            '%l' => 'g',
            '%M' => 'i',
            '%p' => fn($date) => $this->getDayPeriod($date, 'upper'),
            '%P' => fn($date) => $this->getDayPeriod($date, 'lower'),
            '%r' => fn($date) => $this->getTimeWithDayPeriod($date),
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
            '%c' => fn($date) => $this->exec($date, '+%c'),
            '%x' => fn($date) => $this->exec($date, '+%x'),
            '%X' => fn($date) => $this->exec($date, '+%X'),
        ];
    }

    /**
     * Get day stuff (format: %A, %a).
     */
    private function getDay(Date $date): string
    {
        $key = $date->format('N') - 1;
        return $this->translate('days', $key, $date->format('l'));
    }
    private function getDayAbbr(Date $date): string
    {
        $key = $date->format('N') - 1;
        $ret = $this->translate('days', $key, $date->format('D'));

        // Some exceptions.
        if (str_starts_with($this->locale, 'tr_')) {
            return match ($key) {
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
    private function getDayOfYear(Date $date): string
    {
        return sprintf('%03d', $date->format('z') + 1);
    }

    /**
     * Get week of year (format: %U, %W).
     */
    private function getWeekOfYear(Date $date, string $day): string
    {
        $etad = new Date($date->format('Y') .'-01 '. $day, $date->getTimezone());
        return sprintf('%02d', intval(($date->format('z') - $etad->format('z')) / 7) + 1);
    }

    /**
     * Get month stuff (format: %B, %b, %h).
     */
    private function getMonth(Date $date): string
    {
        $key = $date->format('n') - 1;
        return $this->translate('mons', $key, $date->format('F'));
    }
    private function getMonthAbbr(Date $date): string
    {
        $key = $date->format('n') - 1;
        return mb_substr($this->translate('mons', $key, $date->format('M')), 0, 3);
    }

    /**
     * Get century (format: %C).
     */
    private function getCentury(Date $date): string
    {
        return (string) intval($date->format('Y') / 100);
    }

    /**
     * Get short year (format: %g).
     */
    private function getShortYear(Date $date): string
    {
        return substr($date->format('o'), -2);
    }

    /**
     * Get day period, am/pm stuff (format: %p, %P, %r).
     */
    private function getDayPeriod(Date $date, string $case): string
    {
        $key = $date->format('a');
        if ($case == 'upper') {
            return mb_strtoupper($this->translate(null, $key, $key));
        }
        return mb_strtolower($this->translate(null, $key, $key));
    }

    /**
     * Get time day period (format: %r).
     */
    private function getTimeWithDayPeriod(Date $date): string
    {
        return trim($date->format('h:i:s') .' '. $this->getDayPeriod($date, 'upper'));
    }

    /**
     * Linux, my saver..
     */
    private function exec(Date $date, string $format): string
    {
        try {
            $ret = exec(sprintf(
                'LC_TIME=%s TZ=%s date -d %s %s 2>/dev/null',
                escapeshellarg($this->locale),
                escapeshellarg($date->format('P')),
                escapeshellarg($date->format('Y-m-d H:i:s')),
                escapeshellarg($format)
            ));

            // Somehow, zone id not added in exec.
            if ($ret && $format == '+%c' && !preg_match('~(GMT| [-+:][\d]+)$~', $ret)) {
                $ret .= ' '. ($date instanceof UtcDate ? 'GMT' : $date->format('T'));
            }

            return $ret;
        } catch (\Error) {
            // Fallback.
            return match ($format) {
                '+%c' => $date->format('D d M Y H:i:s')
                         .' '. ($date instanceof UtcDate ? 'GMT' : $date->format('T')),
                '+%x' => $date->format('m/d/Y'),
                '+%X' => $date->format('h:i:s A'),
            };
        }
    }

    /**
     * Basic translation method for days/months and am/pm.
     */
    private function translate(string|null $name, string|int $key, string $default): string
    {
        // No am/pm use, I found, so far..
        if (in_array($key, ['am', 'pm'], true)
            && !preg_match('~^(en|tr)_~', $this->locale)) {
            return '';
        }

        if ($name) {
            return $this->intl[$this->locale][$name][$key] ?? $default;
        }
        return $this->intl[$this->locale][$key] ?? $default;
    }
}
