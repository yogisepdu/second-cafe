<?php

namespace App\Policies;

use App\Policies\Concerns\AdminOnlyPolicy;

class CafeTablePolicy
{
    use AdminOnlyPolicy;
}
