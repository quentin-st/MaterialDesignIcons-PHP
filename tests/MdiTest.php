<?php

namespace Mdi\Tests;

use Mdi\Mdi;
use PHPUnit\Framework\TestCase;

class MdiTest extends TestCase
{
    private const DUCK_PATH = 'M8.5,5C7.67,5 7,5.67 7,6.5C7,7.33 7.67,8 8.5,8C9.33,8 10,7.33 10,6.5C10,5.67 9.33,5 8.5,5M10,2C12.76,2 15,4.24 15,7C15,8.7 14.15,10.2 12.86,11.1C14.44,11.25 16.22,11.61 18,12.5C21,14 22,12 22,12C22,12 21,21 15,21H9C9,21 4,21 4,16C4,13 7,12 6,10C2,10 2,6.5 2,6.5C3,7 4.24,7 5,6.65C5.19,4.05 7.36,2 10,2Z';

    public function test_withIconsPath_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/^Specified icons path \(.+\/fa\/\) does not exist!$/');
        Mdi::withIconsPath(__DIR__.'/fa');
    }

    public function test_mdi_withoutIconsPath(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You forgot to specify MDI\'s path!');
        Mdi::mdi('duck');
    }

    public function test_mdi_missingIcon(): void
    {
        Mdi::withIconsPath(__DIR__.'/Resources/icons');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/^Unrecognized icon "dog" \(svg file ".+" does not exist\)\.$/');
        Mdi::mdi('dog');
    }

    public function test_mdi_brokenIcon(): void
    {
        Mdi::withIconsPath(__DIR__.'/Resources/icons');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/".+" could not be recognized as an icon file/');
        Mdi::mdi('broken-duck');
    }

    public function test_mdi_success(): void
    {
        Mdi::withIconsPath(__DIR__.'/Resources/icons');

        $standardOutput = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="presentation"><path d="'.self::DUCK_PATH.'" /></svg>';
        $this->assertSame(
            $standardOutput,
            Mdi::mdi('mdi mdi-duck')
        );
        $this->assertSame(
            $standardOutput,
            Mdi::mdi('mdi-duck')
        );
        $this->assertSame(
            $standardOutput,
            Mdi::mdi('duck')
        );

        // Class
        $this->assertSame(
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="presentation" class="duck-icon"><path d="'.self::DUCK_PATH.'" /></svg>',
            Mdi::mdi('duck', 'duck-icon')
        );

        // Size
        $this->assertSame(
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="64" height="64" role="presentation"><path d="'.self::DUCK_PATH.'" /></svg>',
            Mdi::mdi('duck', null, 64)
        );

        // Attrs
        $this->assertSame(
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="presentation" data-toggle="tooltip" title="I am a duck"><path d="'.self::DUCK_PATH.'" /></svg>',
            Mdi::mdi('duck', null, 24, [
                'data-toggle' => 'tooltip',
                'title' => 'I am a duck'
            ])
        );

        // Ability to remove a default attr by passing an empty value
        $this->assertSame(
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="'.self::DUCK_PATH.'" /></svg>',
            Mdi::mdi('duck', null, 24, [
                'role' => null,
            ])
        );
    }

    public function test_defaultAttributes_success(): void
    {
        Mdi::withIconsPath(__DIR__.'/Resources/icons');

        // New attribute
        Mdi::withDefaultAttributes([
            'aria-label' => 'Duck'
        ]);
        $this->assertSame(
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="presentation" aria-label="Duck"><path d="'.self::DUCK_PATH.'" /></svg>',
            Mdi::mdi('duck')
        );

        // (reset attrs)
        Mdi::withDefaultAttributes([]);

        // Remove attributes
        Mdi::withDefaultAttributes([
            'viewBox' => null,
            'xmlns' => null,
            'role' => null,
        ]);
        $this->assertSame(
            '<svg width="24" height="24"><path d="'.self::DUCK_PATH.'" /></svg>',
            Mdi::mdi('duck')
        );
    }
}
