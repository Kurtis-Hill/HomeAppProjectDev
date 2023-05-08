import { IconResponseInterface } from '../../../Response/Icons/IconResponseInterface';
import { ColourResponseInterface } from '../../../Response/Colour/ColourResponseInterface';
import StandardCardViewReadingResponseInterface from '../CardViewReadings/StandardCardViewReadingResponseInterface';
import { CurrentUserSelections } from '../CardSelection/CurrentUserSelections';
import StateResponseInterface from '../../../Response/State/StateResponseInterface';

export default interface StandardCardViewSensorFormResponseInterface {
    sensorID: number,
    currentCardIcon: IconResponseInterface
    currentCardColour: ColourResponseInterface,
    currentViewState: StateResponseInterface,
    cardViewID: number
    cardUserSelectionOptions: CurrentUserSelections,
    sensorData: StandardCardViewReadingResponseInterface,
}