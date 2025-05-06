<?php

namespace App\Http\Requests;

use Models\WealthType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class WealthRequest extends FormRequest
{

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasAccess('platform.quality.wealth.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //payload
        //   "wealth" => [
        //         "id" => null
        //         "name" => "test test"
        //         "validity_date" => "30-11-2022"
        //         "wealth_type" => "2"
        //         "description" => "<p>vgftrgtrzgtrz</p>"
        //         "indicators" => [1]
        //         "unit" => "1"
        //         "actions" => [1]
        //   ]
        //   "attachment" => [
        //       "link" => [2]
        //   ]
        return [
            "wealth.unit" => "required",
            // "wealth.validity_date" => "required|after:yesterday",
            "attachment" => [function ($attribute, $value, $fail) {
                $message = $this->validateAttachment($value, $fail);
                if ($message) {
                    $fail($message);
                }
            }],
            "wealth.indicators" => "required",
            "attachment.link.url" => ["nullable"]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "wealth.unit.required" => "Le service doit être renseigné",
            // "wealth.validity_date.after" => "La date de validité doit être supérieur à la date du jour",
            "attachment.required" => "L'élément de preuve est requis"
        ];
    }

    private function validateAttachment($value, $fail)
    {
        // renvoie un message si pas bon sinon renvoie false

        $type = WealthType::find($this->get('wealth')['wealth_type']);
        if (is_null($type)) {
            return "le type de preuve n'est pas bon";
        }
        if (!array_key_exists($type->name, $value)) {
            return "La visualisation" . $type->name . " est requise";
        }

        switch ($type->name) {
            case 'link':
                if (is_null($value['link']['type'])) {
                    return "le type n'est pas valide";
                }

                if (is_null($value['link']['url'])) {
                    // dd('pas bien' , $value);
                    return 'Un lien doit être renseigné';
                }
                break;

            case 'ypareo':
                // if (is_null($value['ypareo']['type'])) {
                //     return "le type n'est pas valide";
                // }
                if (is_null($value['ypareo']['process'])) {
                    return "Le process n'est pas renseigné";
                }

                break;
            case 'file':
                // rien à faire pour le moment
                break;

            default:
                return false;
                break;
        }
        return false;
    }
}
