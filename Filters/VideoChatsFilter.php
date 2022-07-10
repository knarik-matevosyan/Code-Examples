<?php

namespace App\Domains\VideoChat\Filters;

use App\Filters\Filter;

/**
 * Class ChatFilter
 */
class VideoChatsFilter extends Filter
{
    /**
     * Model filters
     *
     * @var array
     */
    protected $filters = [
        'page',
        'per_page',
    ];

}
