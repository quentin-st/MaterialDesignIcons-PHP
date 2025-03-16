<?php

namespace Mdi;

use InvalidArgumentException;
use RuntimeException;
use Stringable;

abstract class Mdi
{
    private static ?string $iconsPath = null;
    /** @var array<string, string> */
    public static array $defaultAttributes = [];

    /**
     * Specify the icons path. Will usually look like Mdi::withIconsPath(__DIR__.'/../../../node_modules/@mdi/svg/svg/');
     */
    public static function withIconsPath(string $path): void
    {
        // Ensure path ends with "/"
        if (!str_ends_with($path, DIRECTORY_SEPARATOR)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        // Ensure the specified path exists
        if (!is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Specified icons path (%s) does not exist!', $path));
        }

        self::$iconsPath = $path;
    }

    public static function getIconsPath(): string
    {
        // Ensure that the icons path has been specified, or auto-detect it
        if (!self::$iconsPath && !self::autoDetectIconsPath()) {
            throw new RuntimeException('You forgot to specify MDI\'s path!');
        }

        return self::$iconsPath;
    }

    /**
     * Sets defaults that are used when building <svg> tag attributes set.
     */
    public static function withDefaultAttributes(array $attributes): void
    {
        self::validateAttributes($attributes);
        self::$defaultAttributes = $attributes;
    }

    public static function getDefaultAttributes(): array
    {
        return self::$defaultAttributes;
    }

    public static function mdi(string $icon, ?string $class = null, int $size = 24, array $attrs = []): string
    {
        // Ensure that the icons path has been specified, or auto-detect it
        self::getIconsPath();

        // Ensure attrs are OK
        self::validateAttributes($attrs);

        // Strip leading "mdi mdi-" or "mdi-"
        if (str_starts_with($icon, 'mdi mdi-')) {
            $icon = substr($icon, strlen('mdi mdi-'));
        }
        if (str_starts_with($icon, 'mdi-')) {
            $icon = substr($icon, strlen('mdi-'));
        }

        // Find the icon, ensure it exists
        $filePath = self::$iconsPath.$icon.'.svg';

        if (!is_file($filePath)) {
            throw new InvalidArgumentException(sprintf('Unrecognized icon "%s" (svg file "%s" does not exist).', $icon, $filePath));
        }

        // Read the file
        $svg = file_get_contents($filePath);

        // Only keep the <path d="..." /> part
        if (preg_match('/(<path d=".+" \/>)/', $svg, $matches) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s" could not be recognized as an icon file', $filePath));
        }
        $svg = $matches[1];

        // Add some (clean) attributes
        $attributes = array_merge(
            [
                'viewBox' => '0 0 24 24',
                'xmlns' => 'http://www.w3.org/2000/svg',
                'width' => (string) $size,
                'height' => (string) $size,
                'role' => 'presentation',
            ],
            self::$defaultAttributes,
            $attrs
        );

        if ($class !== null) {
            $attributes['class'] = $class;
        }

        // Remove possibly empty-ish attributes (self::$defaultAttributes or $attrs may contain null values)
        $attributes = array_filter($attributes);

        return sprintf(
            '<svg %s>%s</svg>',
            self::attributes($attributes),
            $svg
        );
    }

    private static function validateAttributes(array $attributes): void
    {
        foreach ($attributes as $name => $value) {
            if (!self::isStringable($value)) {
                throw new InvalidArgumentException("Attribute $name value must be a string, scalar or Stringable");
            }
        }
    }

    private static function isStringable(mixed $value): bool
    {
        return $value === null
            || is_scalar($value)
            || $value instanceof Stringable;
    }

    /**
     * Attempts to auto-detect $iconsPath.
     * We assume that this file (Mdi.php) lives in /vendor/mesavolt/mdi-php/src/.
     *
     * @return bool True if we successfully auto-detected the icons path.
     */
    private static function autoDetectIconsPath(): bool
    {
        // Detect icons installed as npm module
        if (is_dir($npmModule = __DIR__.'/../../../../node_modules/@mdi/svg/svg/')) {
            self::$iconsPath = $npmModule;
            return true;
        }

        return false;
    }

    /**
     * Turns a 1-dimension array into an HTML-ready attributes set.
     */
    private static function attributes(array $attrs): string
    {
        return implode(' ', array_map(
            function (string $val, string $key) {
                return $key.'="'.htmlspecialchars($val).'"';
            },
            $attrs,
            array_keys($attrs)
        ));
    }
}
