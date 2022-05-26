<?php

namespace App\DataFixtures\Data;

class SettingsData
{
    public function getSocialDatas(): array
    {
        $datas = [];
        $datas[] = [
            "socialMedias" => [
                [
                    "type" => "socialMedia",
                    "socialMediaName" => "facebook",
                    "socialMediaUrl" => "https://facebook.com"
                ],
                [
                    "type" => "socialMedia",
                    "socialMediaName" => "linkedin",
                    "socialMediaUrl" => "https://linkedin.com"
                ],
                [
                    "type" => "socialMedia",
                    "socialMediaName" => "instagram",
                    "socialMediaUrl" => "https://instagram.com",
                ],
                [
                    "type" => "socialMedia",
                    "socialMediaName" => "twitter",
                    "socialMediaUrl" => "https://twitter.com"
                ]
            ]
        ];
        return $datas;
    }

    public function getCompanyData(): array
    {
        $datas = [];
        $datas[] = [
            "name" => "Pixel développement",
            "email" => "contact@pixel-developpement.com",
            "phoneNumber" => "04.75.46.20.53",
            "address" => ["lat" => 44.4957492, "long" => 4.7469046, "zoom" => 15, "code" => "26780", "town" => "Châteauneuf du Rhône", "street" => "rue Marie Curie", "country" => null, "title" => null, "numbre" => "251"],
            "placeId" => "AIzaSyCURaHXOFsQIEZQoSOrIxaRnPSj0fjMkuY",
            "openingHours" => [
                [
                    "type" => "openingHour",
                    "day" => "monday",
                    "openHour" => "09:00:00",
                    "closeHour" => "12:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "monday",
                    "openHour" => "14:00:00",
                    "closeHour" => "18:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "tuesday",
                    "openHour" => "09:00:00",
                    "closeHour" => "12:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "tuesday",
                    "openHour" => "14:00:00",
                    "closeHour" => "18:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "wednesday",
                    "openHour" => "09:00:00",
                    "closeHour" => "12:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "wednesday",
                    "openHour" => "14:00:00",
                    "closeHour" => "18:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "thrusday",
                    "openHour" => "09:00:00",
                    "closeHour" => "12:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "thrusday",
                    "openHour" => "14:00:00",
                    "closeHour" => "18:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "friday",
                    "openHour" => "09:00:00",
                    "closeHour" => "12:00:00"
                ],
                [
                    "type" => "openingHour",
                    "day" => "friday",
                    "openHour" => "14:00:00",
                    "closeHour" => "18:00:00"
                ]
            ]
        ];
        return $datas;
    }
}
