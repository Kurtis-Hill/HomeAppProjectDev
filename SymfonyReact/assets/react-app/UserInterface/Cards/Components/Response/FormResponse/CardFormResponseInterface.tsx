import { IconResponseInterface } from '../../../../Response/Icons/IconResponseInterface';
import { ColourResponseInterface } from '../../../../Response/Colour/ColourResponseInterface';
import { CurrentUserSelections } from '../../../Response/CardSelection/CurrentUserSelections';
import { CardCurrentReadingResponse } from '../../../Response/CurrentReadingCardData/CardDataResponseInterface';
import StateResponseInterface from '../../../../Response/State/StateResponseInterface';

export interface CardFormResponseInterface {
    sensorID: number;
    currentCardIcon: IconResponseInterface;
    currentCardColour: ColourResponseInterface;
    currentViewState: StateResponseInterface;
    cardUserSelectionOptions: CurrentUserSelections;
    cardViewID: number;
    sensorData: CardCurrentReadingResponse;
}