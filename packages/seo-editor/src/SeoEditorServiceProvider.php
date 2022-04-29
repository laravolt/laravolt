<?php

namespace Laravolt\SeoEditor;

use Laravolt\Support\Base\BaseServiceProvider;

class SeoEditorServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'seo-editor';
    }

    protected function menu()
    {
        app('laravolt.menu.sidebar')->registerCore(function ($menu) {
            $menu->system
                ->add(__('SEO Editor'), route('seo-editor::meta.index'))
                ->data('permission', \Laravolt\Platform\Enums\Permission::MANAGE_SEO)
                ->active('seo-editor/*')
                ->data('icon', 'bullseye-arrow');
        });
    }
}
