<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

use Admin\Orchid\Screens\PlatformScreen;
use Admin\Orchid\Screens\Role\RoleEditScreen;
use Admin\Orchid\Screens\Role\RoleListScreen;
use Admin\Orchid\Screens\User\UserEditScreen;
use Admin\Orchid\Screens\User\UserListScreen;
use Admin\Orchid\Screens\User\UserProfileScreen;
use Admin\Orchid\Screens\Wealth\ListScreen as WealthListScreen;
use Admin\Orchid\Screens\Wealth\EditScreen as WealthEditScreen;
use Admin\Orchid\Screens\Wealth\DisplayScreen as WealthDisplayScreen;
use Admin\Orchid\Screens\Tag\ListScreen as TagListScreen;
use Admin\Orchid\Screens\Tag\EditScreen as TagEditScreen;
use Admin\Orchid\Screens\Action\ListScreen as ActionListScreen;
use Admin\Orchid\Screens\Action\EditScreen as ActionEditScreen;
use Admin\Orchid\Screens\QualityLabel\EditScreen as QualityLabelEditScreen;
use Admin\Orchid\Screens\QualityLabel\ListScreen as QualityLabelsListScreen;
use Admin\Orchid\Screens\Indicator\ListScreen as IndicatorListScreen;
use Admin\Orchid\Screens\Indicator\EditScreen as IndicatorEditScreen;

use Models\QualityLabel;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    // Main
    Route::screen('/dashboard', PlatformScreen::class)
        ->name('platform.dashboard');

    // Platform > Profile
    Route::screen('profile', UserProfileScreen::class)
        ->name('platform.profile')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('Profile'), route('platform.profile'));
        });

    // Platform > System > Users
    Route::screen('users/{user}/edit', UserEditScreen::class)
        ->name('platform.systems.users.edit')
        ->breadcrumbs(function (Trail $trail, $user) {
            return $trail
                ->parent('platform.systems.users')
                ->push(__('User'), route('platform.systems.users.edit', $user));
        });

    // Platform > System > Users > Create
    Route::screen('users/create', UserEditScreen::class)
        ->name('platform.systems.users.create')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.systems.users')
                ->push(__('Create'), route('platform.systems.users.create'));
        });

    // Platform > System > Users > User
    Route::screen('users', UserListScreen::class)
        ->name('platform.systems.users')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('Users'), route('platform.systems.users'));
        });

    // Platform > System > Roles > Role
    Route::screen('roles/{role}/edit', RoleEditScreen::class)
        ->name('platform.systems.roles.edit')
        ->breadcrumbs(function (Trail $trail, $role) {
            return $trail
                ->parent('platform.systems.roles')
                ->push(__('Role'), route('platform.systems.roles.edit', $role));
        });

    // Platform > System > Roles > Create
    Route::screen('roles/create', RoleEditScreen::class)
        ->name('platform.systems.roles.create')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.systems.roles')
                ->push(__('Create'), route('platform.systems.roles.create'));
        });

    // Platform > System > Roles
    Route::screen('roles', RoleListScreen::class)
        ->name('platform.systems.roles')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('Roles'), route('platform.systems.roles'));
        });


    //###########################################
    //##### GO TO Quality labels forms ##########
    //###########################################

    // Platform > quality > quality_labels
    Route::screen('quality-labels', QualityLabelsListScreen::class)
        ->name('platform.quality.quality_labels')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('quality_labels'), route('platform.quality.quality_labels'));
        });

    // Platform > Quality > wealth > Wealth
    Route::screen('quality-label/{label}/display', WealthDisplayScreen::class)
        ->name('platform.quality.wealth.display')
        ->breadcrumbs(function (Trail $trail, $wealth) {
            return $trail
                ->parent('platform.quality.wealths')
                ->push(__('wealth :id', ['id' => $wealth->id]), route('platform.quality.wealth.display', $wealth));
        });

    // Platform > Quality > wealth > Wealth
    Route::screen('quality-label/{quality_label}/edit', QualityLabelEditScreen::class)
        ->name('platform.quality.quality_label.edit')
        ->breadcrumbs(function (Trail $trail, $quality_label) {
            return $trail
                ->parent('platform.quality.quality_labels')
                ->push(
                    __(':label', ["label" => Qualitylabel::find(intval($quality_label))->label]),
                    route('platform.quality.quality_label.edit', ['quality_label' => $quality_label])
                );
        });

    // Platform > Quality > quality-label > Create
    Route::screen('quality-label/{quality_label}/create', QualityLabelEditScreen::class)
        ->name('platform.quality.quality_label.create')
        ->breadcrumbs(function (Trail $trail, $quality_label) {
            return $trail
                ->parent('platform.quality.quality_labels')
                ->push(
                    __(':label', ["label" => Qualitylabel::find(intval($quality_label))->label]),
                    route('platform.quality.quality_label.edit', ['quality_label' => $quality_label])
                );
        });

    //###########################################
    //########## GO TO Indicators forms ############
    //###########################################

    // Platform > quality > indicators
    Route::screen('quality-label/{quality_label}/indicators', IndicatorListScreen::class)
        ->name('platform.quality.quality_label.indicators')
        ->breadcrumbs(function (Trail $trail, $quality_label) {
            return $trail
                ->parent('platform.quality.quality_label.edit', $quality_label)
                ->push(__('indicators'), route('platform.quality.quality_label.indicators', ["quality_label" => $quality_label]));
        });
    // Platform > Quality > indicator > Create
    Route::screen('quality-label/{quality_label}/indicator/create', IndicatorEditScreen::class)
        ->name('platform.quality.quality_label.indicator.create')
        ->breadcrumbs(function (Trail $trail, $quality_label) {
            return $trail
                ->parent('platform.quality.quality_label.indicators', $quality_label)
                ->push(__('Create'), route('platform.quality.quality_label.indicator.create', ["quality_label" => $quality_label]));
        });

    // Platform > Quality > indicator > indicator
    Route::screen('quality-label/{quality_label}/indicator/{indicator}/edit', IndicatorEditScreen::class)
        ->name('platform.quality.quality_label.indicator.edit')
        ->breadcrumbs(function (Trail $trail, $quality_label, $indicator) {
            return $trail
                ->parent('platform.quality.quality_label.indicators', $quality_label)
                ->push(__('indicator :label', ['label' => $indicator->id]), route('platform.quality.quality_label.indicator.edit', ['indicator' => $indicator, 'quality_label' => $quality_label]));
        });


    //###########################################
    //########## GO TO Wealths forms ############
    //###########################################

    // Platform > quality > wealths
    Route::screen('wealths', WealthListScreen::class)
        ->name('platform.quality.wealths')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('wealths'), route('platform.quality.wealths'));
        });

    // Platform > Quality > wealth > Wealth
    // Route::screen('wealth/{wealth}/display', WealthDisplayScreen::class)
    //     ->name('platform.quality.wealth.display')
    //     ->breadcrumbs(function (Trail $trail, $wealth) {
    //         return $trail
    //             ->parent('platform.quality.wealths')
    //             ->push(__('wealth :id', ['id' => $wealth->id]), route('platform.quality.wealth.display', $wealth));
    //     });

    // Platform > Quality > wealth > Wealth
    Route::screen('wealth/{wealth}/edit/{duplicate?}', WealthEditScreen::class)
        ->name('platform.quality.wealth.edit')
        ->breadcrumbs(function (Trail $trail, $wealth) {
            return $trail
                ->parent('platform.quality.wealths')
                ->push(__('wealth'), route('platform.quality.wealth.edit', ['wealth' => $wealth]));
        });

    // Platform > Quality > wealth > Create
    Route::screen('wealth/create', WealthEditScreen::class)
        ->name('platform.quality.wealth.create')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.quality.wealths')
                ->push(__('Create'), route('platform.quality.wealth.create'));
        });

    //################################
    //####### GO TO Tag forms ########
    //################################
    //DOC NEW ORCHID FORM: add routes
    // Platform > quality > tags
    Route::screen('tags', TagListScreen::class)
        ->name('platform.quality.tags')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('tags'), route('platform.quality.tags'));
        });

    // Platform > Quality > tags > tag
    Route::screen('tags/{tag}/edit', TagEditScreen::class)
        ->name('platform.quality.tags.edit')
        ->breadcrumbs(function (Trail $trail, $tag) {
            return $trail
                ->parent('platform.quality.tags')
                ->push(__('tag'), route('platform.quality.tags.edit', $tag));
        });

    // Platform > Quality > tags > Create
    Route::screen('tags/create', TagEditScreen::class)
        ->name('platform.quality.tags.create')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.quality.tags')
                ->push(__('Create'), route('platform.quality.tags.create'));
        });

    //################################
    //####### GO TO Action forms ########
    //################################

    // Platform > quality > actions
    Route::screen('actions', ActionListScreen::class)
        ->name('platform.quality.actions')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.index')
                ->push(__('actions'), route('platform.quality.actions'));
        });

    // Platform > Quality > actions > action
    Route::screen('actions/{action}/edit', ActionEditScreen::class)
        ->name('platform.quality.actions.edit')
        ->breadcrumbs(function (Trail $trail, $action) {
            return $trail
                ->parent('platform.quality.actions')
                ->push(__('action'), route('platform.quality.actions.edit', $action));
        });

    // Platform > Quality > actions > Create
    Route::screen('actions/create', ActionEditScreen::class)
        ->name('platform.quality.actions.create')
        ->breadcrumbs(function (Trail $trail) {
            return $trail
                ->parent('platform.quality.actions')
                ->push(__('Create'), route('platform.quality.actions.create'));
        });
});
