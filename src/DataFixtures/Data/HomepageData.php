<?php

namespace App\DataFixtures\Data;

use App\DataFixtures\ORM\AppFixtures;

class HomepageData
{
    public function getDatas(): array
    {
        return [
            'locale' => AppFixtures::LOCALE_FR,
            'title' => 'sulu-skeleton',
            'url' => '/',
            'article' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum posuere vehicula urna, vitae sollicitudin massa eleifend vel.</p>
            <p>Nunc id quam enim. Etiam vitae nisi ut urna blandit viverra sed eget purus. Mauris quis velit quis magna gravida sagittis. Cras scelerisque faucibus turpis a ultricies. Sed nec tellus quam.</p>"
        ];
    }
}
