<?php

namespace Admin\Orchid\Layouts\Attachment\Ypareo;

use Orchid\Screen\Sight;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Legend;

class YpareoCard extends Legend
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;


    /**
     * columns
     *
     * @return array
     */
    protected function columns(): array
    {
        return  [
            Sight::make(__('attachement_actions_card'))->render(function () {
                return Group::make(
                    [
                        Button::make(__('Edit'))
                            ->icon('pencil')
                            ->confirm(__('confirm_update_ypareo'))
                            ->method('editAttachment', [
                                'wealth' =>  $this->query['wealth'],
                            ]),

                        Button::make(__('Remove'))
                            ->icon('database')
                            ->confirm(__('confirm_delete_ypareo'))
                            ->method('removeAttachment', [
                                'wealth' =>  $this->query['wealth'],
                            ]),
                    ]
                );
            }),
            Sight::make('attachment.ypareo.type', __('attachment_ypareo_type')),
            Sight::make('attachment.ypareo.process', __('attachment_ypareo_process'))
                ->render(function ($query) {
                    // render not escape string, you can show html formated like this
                    $text = "";
                    if (isset($query['attachment']['ypareo']['process'])) {
                        $text = $query['attachment']['ypareo']['process'];
                    }
                    return $text;
                }),
            Sight::make('attachment.ypareo.created_at', __('created_at')),
        ];
    }
}
