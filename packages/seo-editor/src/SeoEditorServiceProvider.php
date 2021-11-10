<?php

namespace Laravolt\SeoEditor;

use Laravolt\Support\Base\BaseServiceProvider;

class SeoEditorServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        dd('ding');
        return 'seo-editor';
    }

    protected function menu()
    {
        app('laravolt.menu.sidebar')->register(function ($menu) {
            $menu->system
                ->add(__('SEO Editor'), route('seo-editor::meta.edit'))
                ->data('permission', \Laravolt\Platform\Enums\Permission::MANAGE_SEO)
                ->active('seo-editor/*')
                ->data('icon', 'bullseye-arrow');
        });
    }
}
