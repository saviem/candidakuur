<?php

namespace App\Support;

/**
 * Strips Unicode em dashes (U+2014) from string attributes on save.
 * Candidakuur copy style: no em dashes in user-facing text.
 */
trait WithoutEmDashes
{
    protected static function bootWithoutEmDashes(): void
    {
        static::saving(function (self $model): void {
            foreach ($model->getAttributes() as $key => $value) {
                if (! is_string($value) || ! str_contains($value, "\u{2014}")) {
                    continue;
                }

                $clean = str_replace(" \u{2014} ", ', ', $value);
                $clean = str_replace("\u{2014}", ', ', $clean);
                $clean = str_replace(', ,', ',', $clean);
                $clean = str_replace(' ,', ',', $clean);

                $model->setAttribute($key, $clean);
            }
        });
    }
}
