<?php

namespace Felix\Metadata;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 * @property Meta $meta
 */
trait HasMetadata
{
    /**
     * @return Meta
     * @internal
     */
    public function getMetaAttribute(): Meta
    {
        return new Meta($this);
    }
}
