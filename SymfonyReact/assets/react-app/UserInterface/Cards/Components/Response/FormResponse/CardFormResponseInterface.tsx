import { IconResponseInterface } from '../../../../Response/Icons/IconResponseInterface';
import { ColourResponseInterface } from '../../../../Response/Colour/ColourResponseInterface';
import { StateResponseInterface } from '../../../../Response/State/StateResponseInterface';
import { CurrentUserSelections } from '../../../Response/CardSelection/CurrentUserSelections';
import { CardCurrentReadingResponse } from '../../../Response/CurrentReadingCardData/CardDataResponseInterface';

export interface CardFormResponseInterface {
    sensorID: number;
    currentCardIcon: IconResponseInterface;
    currentCardColour: ColourResponseInterface;
    currentViewState: StateResponseInterface;
    cardUserSelectionOptions: CurrentUserSelections;
    cardViewID: number;
    sensorData: CardCurrentReadingResponse;
}