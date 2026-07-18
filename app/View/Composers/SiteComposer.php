<?php

namespace App\View\Composers;

use App\Models\Profile;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\View\View;

class SiteComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'siteProfile' => Profile::current(),
            'socialLinks' => SocialLink::active()->get(),
            'siteSettings' => fn (string $key, $default = null) => SiteSetting::get($key, $default),
        ]);
    }
}
