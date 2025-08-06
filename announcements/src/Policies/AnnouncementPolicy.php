<?php

namespace Boy132\Announcements\Policies;

use App\Policies\DefaultPolicies;

class AnnouncementPolicy
{
    use DefaultPolicies;

    protected string $modelName = 'announcement';
}
