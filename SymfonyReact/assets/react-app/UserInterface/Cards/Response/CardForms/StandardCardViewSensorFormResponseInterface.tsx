import { IconResponseInterface } from '../../../Response/Icons/IconResponseInterface';
import { ColourResponseInterface } from '../../../Response/Colour/ColourResponseInterface';
import { StateResponseInterface } from '../../../Response/State/StateResponseInterface';
import StandardCardViewReadingResponseInterface from '../CardViewReadings/StandardCardViewReadingResponseInterface';
import { CardCurrentSensorReadings } from '../../Components/DisplayCards/CardCurrentSensorReadings';
import { CurrentUserSelections } from '../CardSelection/CurrentUserSelections';

export default interface StandardCardViewSensorFormResponseInterface {
    sensorID: number,
    currentCardIcon: IconResponseInterface
    currentCardColour: ColourResponseInterface,
    currentViewState: StateResponseInterface,
    cardViewID: number
    cardUserSelectionOptions: CurrentUserSelections,
    sensorData: StandardCardViewReadingResponseInterface,
}