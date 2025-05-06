<?php

declare(strict_types=1);

namespace Admin\Orchid\Layouts\User;

use Models\Unit;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserUnitLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('user.unit.')
                ->fromModel(Unit::class, 'label')
                ->multiple()
                ->title(__('unit'))
                ->help(__('user_account_unit_help')),
        ];
    }
}
