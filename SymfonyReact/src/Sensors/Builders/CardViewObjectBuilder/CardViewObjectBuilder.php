<?php

namespace App\Sensors\Builders\CardViewObjectBuilder;

use App\Sensors\Entity\Sensor;
use App\User\Entity\User;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;

class CardViewObjectBuilder
{
    public static function buildNewCardViewObject(
        Sensor $sensor,
        User $user,
        Icons $icons,
        CardColour $cardColour,
        Cardstate $cardState,
    ): CardView {
        $newCard = new CardView();
        $newCard->setSensor($sensor);
        $newCard->setUserID($user);
        $newCard->setIconID($icons);
        $newCard->setCardColourID($cardColour);
        $newCard->setCardStateID($cardState);

        return $newCard;
    }
}
