<?php

namespace App\Transformers;

use App\Models\Configuration;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ConfigurationAdminTransformer extends TransformerAbstract
{

    public function transform(Configuration $configuration)
    {
        return [
            'logo' => $configuration->logo ? media_url_web( $configuration->logo) : null,
            'email' => $configuration->email,
            'phone' => $configuration->phone,
            'longitude' => $configuration->longitude,
            'latitude' => $configuration->latitude,
            'currency' => $configuration->currency,
            'facebook' => $configuration->facebook,
            'google_plus' => $configuration->google_plus,
            'twitter' => $configuration->twitter,
            'instagram' => $configuration->instagram,
            'is_active' => $configuration->is_active,
            'app_store_link' => $configuration->app_store_link,
            'play_store_link' => $configuration->play_store_link,
            'max_day_class' => $configuration->max_day_class,
            'translations' => $configuration->getTranslationsArray(),
            'updated_at' => Carbon::parse($configuration->created_at)->format('d-m-Y'),
            'updated_by' => $configuration->user ? $configuration->user->name : null,
        ];
    }
}
