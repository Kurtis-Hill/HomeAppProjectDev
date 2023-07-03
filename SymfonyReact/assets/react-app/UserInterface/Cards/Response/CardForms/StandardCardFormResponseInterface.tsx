import { IconResponseInterface } from '../../../Response/Icons/IconResponseInterface';
import { ColourResponseInterface } from '../../../Response/Colour/ColourResponseInterface';
import { CurrentUserSelections } from '../CardSelection/CurrentUserSelections';
import { CurrentCardCurrentReadingResponse as StandardSensorCardCurrentReadingResponse } from '../CurrentReadingCardData/CardDataResponseInterface';
import StateResponseInterface from '../../../Response/State/StateResponseInterface';

export interface StandardCardFormResponseInterface {
    sensorID: number;
    currentCardIcon: IconResponseInterface;
    currentCardColour: ColourResponseInterface;
    currentViewState: StateResponseInterface;
    cardUserSelectionOptions: CurrentUserSelections;
    cardViewID: number;
    sensorData: StandardSensorCardCurrentReadingResponse|null;
}