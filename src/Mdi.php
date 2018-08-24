<?php

namespace Mdi;

abstract class Mdi
{
    /** @var string */
    private static $iconsPath;

    /** @var array */
    public static $defaultAttributes = [];

    /**
     * Specify the icons path. Will usually look like Mdi::withIconsPath(__DIR__.'/../../../node_modules/@mdi/svg/svg/');
     */
    public static function withIconsPath(string $path): void
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        // Ensure the specified path exists
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('Specified icons path (%s) does not exist!', $path));
        }

        self::$iconsPath = $path;
    }

    /**
     * Sets defaults that are used when building <svg> tag attributes set.
     */
    public static function withDefaultAttributes(array $attributes): void
    {
        self::$defaultAttributes = $attributes;
    }

    public static function mdi(string $icon, ?string $class = null, int $size = 24, array $attrs = []): string
    {
        // Ensure that the icons path has been specified, or auto-detect it
        if (!self::$iconsPath && !self::autoDetectIconsPath()) {
            throw new \RuntimeException('You forgot to specify MDI\'s path!');
        }

        // Strip leading "mdi mdi-" or "mdi-"
        if (strpos($icon, 'mdi mdi-') === 0) {
            $icon = substr($icon, \strlen('mdi mdi-'));
        }
        if (strpos($icon, 'mdi-') === 0) {
            $icon = substr($icon, \strlen('mdi-'));
        }

        // Find the icon, ensure it exists
        $filePath = self::$iconsPath.$icon.'.svg';

        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unrecognized icon "%s" (svg file "%s" does not exist).', $icon, $filePath));
        }

        // Read the file
        $svg = file_get_contents($filePath);

        // Only keep the <path d="..." /> part
        if (preg_match('/(<path d=".+" \/>)/', $svg, $matches) !== 1) {
            throw new \InvalidArgumentException(sprintf('"%s" could not be recognized as an icon file', $filePath));
        }
        $svg = $matches[1];

        // Add some (clean) attributes
        $attributes = array_merge(
            [
                'viewBox' => '0 0 24 24',
                'xmlns' => 'http://www.w3.org/2000/svg',
                'width' => $size,
                'height' => $size,
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

    /**
     * Attempts to auto-detect $iconsPath.
     * We assume that this file (Mdi.php) lives in /vendor/mesavolt/mdi-php/src/.
     *
     * @return bool True if we successfully auto-detected the icons path.
     */
    private static function autoDetectIconsPath(): bool
    {
        $candidates = [
            __DIR__.'/../../../../node_modules/@mdi/svg/svg/', // icons installed as npm module
        ];

        foreach ($candidates as $candidate) {
            if (is_dir($candidate)) {
                self::$iconsPath = $candidate;

                return true;
            }
        }

        return false;
    }

    /**
     * Turns a 1-dimension array into an HTML-ready attributes set.
     */
    private static function attributes(array $attrs): string
    {
        return implode(' ', array_map(
            function ($val, $key) {
                return $key.'="'.htmlspecialchars($val).'"';
            },
            $attrs,
            array_keys($attrs)
        ));
    }
}
