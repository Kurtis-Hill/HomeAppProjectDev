<?php

namespace App\Builders\UserInterface\CardViewObjectBuilder;

use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardState;
use App\Entity\UserInterface\Card\CardView;
use App\Entity\UserInterface\Card\Colour;
use App\Entity\UserInterface\Icons;

class CardViewObjectBuilder
{
    public static function buildNewCardViewObject(
        Sensor $sensor,
        User $user,
        Icons $icons,
        Colour $cardColour,
        CardState $cardState,
    ): CardView {
        $newCard = new CardView();
        $newCard->setSensor($sensor);
        $newCard->setUserID($user);
        $newCard->setCardIconID($icons);
        $newCard->setCardColourID($cardColour);
        $newCard->setCardStateID($cardState);

        return $newCard;
    }
}
