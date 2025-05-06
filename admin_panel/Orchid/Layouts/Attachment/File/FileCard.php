<?php

namespace Admin\Orchid\Layouts\Attachment\File;

use Orchid\Screen\Sight;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Legend;

/**
 * FileCard
 */
class FileCard extends Legend
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
                        Button::make(__('file_archive'))
                            ->icon('database')
                            ->confirm(__('confirm_archive_file'))
                            ->method('removeFile', [
                                'wealth' =>  $this->query['wealth'],
                                'action' => "archive"
                            ]),

                        Button::make(__('remove_db'))
                            ->icon('trash')
                            ->confirm(__('confirm_delete_db_file'))
                            ->method('removeFile', [
                                'wealth' =>  $this->query['wealth'],
                                "action" => "logical"
                            ])->right(),

                        Button::make(__('remove_drive'))
                            ->icon('trash')
                            ->confirm(__('confirm_delete_file'))
                            ->method('removeFile', [
                                'wealth' =>  $this->query['wealth'],
                                'action' => 'eradicate'
                            ]),
                    ]
                );
            }),
            Sight::make('original_name', __('original_name'))->render(function () {
                return $this->query['wealth']->file->original_name;
            }),
            Sight::make('mime_type', __('mime_type'))->render(function () {
                return $this->query['wealth']->file->mime_type;
            }),
            Sight::make('gdrive_shared_link', __('gdrive_shared_link'))->render(function () {
                $link = $this->query['wealth']->file->gdrive_shared_link;
                return Link::make($link)
                    ->href($link);
                // ->class('my-link');
            }),
            Sight::make('created_at', __('created_at'))->render(function () {
                return $this->query['wealth']->file->created_at;
            }),
        ];
    }
}
