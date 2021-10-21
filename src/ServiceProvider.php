<?php

namespace JackSleight\Members;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'cp'  => __DIR__.'/../routes/cp.php',
        'web' => __DIR__.'/../routes/web.php',
    ];

    protected $scripts = [
        __DIR__ . '/../dist/js/addon.js',
    ];

    protected $tags = [
        \JackSleight\Members\Tags\UserTags::class,
    ];

    public function register()
    {
        parent::register();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/members.php', 'members',
        );
    }

    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'members');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'members');

        $this->publishes([
            __DIR__ . '/../config/members.php' => config_path('members.php'),
        ], 'members-config');

        $this->publishes([
            __DIR__.'/../resources/views/web' => resource_path('views/vendor/members/web'),
        ], 'members-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/members'),
        ], 'members-translations');

        Nav::extend(function ($nav) {
            $nav->create('Members')
                ->section('Members')
                ->route('members.index')
                ->active('members')
                ->can('view members')
                ->icon('users');
        });

        $this->app->booted(function () {
            Permission::group('members', 'Members', function () {
                Permission::register('view members')->label(__('members::messages.view_members'))->children([
                    Permission::make('edit members')->label(__('members::messages.edit_members'))->children([
                        Permission::make('create members')->label(__('members::messages.create_members')),
                    ]),
                ]);
            });
        });
    }
}