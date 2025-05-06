<?php

namespace App\Http\Traits;

/**
 * util functions to manage attachments
 */
trait WithAttachments
{

    /**
     *
     * return true if only one of fields are null
     *
     * @param  mixed $attachments
     * @return bool
     */
    public function isEmptyAttachments($attachments): bool
    {
        if (isset($attachments)) {
            foreach ($attachments as $attachment) {
                if (isset($attachment)) {
                    foreach ($attachment as $key => $value) {
                        if (isset($value)) {
                            return false;
                        }
                    }
                }
                else {
                    return true;
                }
            }
        }

        return true;
    }


    /**
     *
     * return true if editable
     *
     * @param  mixed $query
     * @return bool
     */
    public function canEditAttachment($query): bool
    {
        return
            !isset($query['attachment']) ||
            (
                isset($query['attachment']) &&
                $this->isEmptyAttachments($query['attachment'])
            );
    }
}
