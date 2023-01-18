import { IconResponseInterface } from '../../../Response/IconResponseInterface';
import { ColourResponseInterface } from '../../../Response/ColourResponseInterface';
import { CardStateResponseInterface } from '../../../Response/CardStateResponseInterface';
import { CurrentUserSelections } from '../../../Response/CurrentUserSelections';
import { CardCurrentReadingResponse } from '../../../Response/CardDataResponseInterface';

export interface CardFormResponseInterface {
    sensorID: number;
    currentCardIcon: IconResponseInterface;
    currentCardColour: ColourResponseInterface;
    currentViewState: CardStateResponseInterface;
    cardUserSelectionOptions: CurrentUserSelections;
    cardViewID: number;
    sensorData: CardCurrentReadingResponse;
}