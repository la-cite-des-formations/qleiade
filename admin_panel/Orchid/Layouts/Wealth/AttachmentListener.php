<?php

namespace Admin\Orchid\Layouts\Wealth;

use Admin\Orchid\Layouts\Attachment\File\FileCard;
use Admin\Orchid\Layouts\Attachment\Link\LinkCard;
use Admin\Orchid\Layouts\Attachment\File\UploadLayout;
use Admin\Orchid\Layouts\Attachment\Ypareo\YpareoCard;
use Admin\Orchid\Layouts\Attachment\Link\LinkEditLayout;
use Admin\Orchid\Layouts\Attachment\Ypareo\YpareoEditlayout;
use App\Http\Traits\WithAttachments;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Layouts\Listener;


class AttachmentListener extends Listener
{
    use WithAttachments;
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = ['wealth.wealth_type', 'wealth.id'];

    /**
     * What screen method should be called
     * as a source for an asynchronous request.
     *
     * The name of the method must
     * begin with the prefix "async"
     *
     * @var string
     */
    protected $asyncMethod = 'asyncAttachmentData';

    /**
     * @return Layout[]
     */
    protected function layouts(): iterable
    {

        if (isset($this->query['wealth'])) {
            $wealth = $this->query['wealth'];
        }

        if (isset($this->query['whoShouldSee'])) {
            $whoShouldSee = $this->query['whoShouldSee'];
        } else {
            $whoShouldSee = false;
        }

        //File type attachment
        $uploadFile = new UploadLayout();
        $fileCard = new FileCard();

        //Link type attachment
        $linkEdit = new LinkEditLayout();
        $linkCard = new LinkCard();

        //Ypareo type attachment
        $ypareoEdit = new YpareoEditlayout();
        $ypareoCard = new YpareoCard();

        return [
            //empty wealth type
            Layout::legend('empty wealth type', [
                Sight::make('Avertissement')->render(function () {
                    return __('empty_wealth_type_card');
                })
            ])
                ->canSee(
                    ($whoShouldSee == '')
                        &&
                        ($this->canEditAttachment($this->query))
                ),

            //File type attachment
            //edit
            $uploadFile->title(__('file_upload'))
                ->canSee(
                    ($whoShouldSee === 'file')
                        &&
                        ($this->canEditAttachment($this->query))
                ),
            //File show card
            $fileCard->title(__('show_attachment'))
                ->canSee(
                    ($whoShouldSee === 'file')
                        &&
                        ($wealth->exists && !$this->canEditAttachment($this->query))
                ),

            // link type attachment
            //edit
            $linkEdit->title(__('link_edit'))
                ->canSee(
                    ($whoShouldSee === 'link')
                        &&
                        ($this->canEditAttachment($this->query))
                ),
            // show
            $linkCard->title(__('show_attachment'))
                ->canSee(
                    ($whoShouldSee === 'link')
                        &&
                        ($wealth->exists && !$this->canEditAttachment($this->query))
                ),

            // Ypareo type attachment
            //edit
            $ypareoEdit->title(__('ypareo_edit'))
                ->canSee(
                    ($whoShouldSee === 'ypareo')
                        // &&
                        // ($this->canEditAttachment($this->query))
                ),
            //show
            // $ypareoCard->title(__('show_attachment'))
            //     ->canSee(
            //         ($whoShouldSee === 'ypareo')
            //             &&
            //             ($wealth->exists && !$this->canEditAttachment($this->query))
            //     ),
        ];
    }
}
