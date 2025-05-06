<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Models\Action;
use Models\Unit;
use Models\Indicator;
use Models\QualityLabel;
use Models\Tag;
use Models\Wealth;
use Models\WealthType;
use App\Observers\WealthObserver;
use App\Observers\ActionObserver;
use App\Observers\IndicatorObserver;
use App\Observers\UnitObserver;
use App\Observers\QualityLabelObserver;
use App\Observers\TagObserver;
use App\Observers\WealthTypeObserver;


class EventServiceProvider extends ServiceProvider
{

    //DOC: to register listeners
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\UpdateObjectInRelationWithWealth' => [
            'App\Listeners\UpdateWealthsIndex',
        ]
    ];

    //DOC: to register observers
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Wealth::observe(WealthObserver::class);
        Action::observe(ActionObserver::class);
        Indicator::observe(IndicatorObserver::class);
        Unit::observe(UnitObserver::class);
        QualityLabel::observe(QualityLabelObserver::class);
        Tag::observe(TagObserver::class);
        WealthType::observe(WealthTypeObserver::class);
    }
}
