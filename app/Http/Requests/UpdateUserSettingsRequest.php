<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'show_age' => 'boolean',
            'show_profile_picture' => 'boolean',
            'show_net_worth' => 'boolean',
            'map_visibility' => 'boolean',
            'online_status_visible' => 'boolean',
            'message_permission' => 'in:everyone,friends,near_me',
            'accept_event_invites' => 'boolean',
            'accept_brand_promotions' => 'boolean',
            'visibility_scope' => 'in:public,verified_users,friends_of_friends',
            'auto_hide_in_high_risk_regions' => 'boolean',
            'country_visibility_controls' => 'nullable|array',
            'country_visibility_controls.*' => 'boolean',
            'share_contact_with_lost_item_finders' => 'boolean',
            'share_contact_with_event_organizers' => 'boolean',
            'share_contact_with_brands' => 'boolean',
        ];
    }
}
