<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class MenuPolicy
{
    use AdminOnlyPolicy;
}
