import { ColourResponseInterface } from "../../../Response/Colour/ColourResponseInterface"
import { IconResponseInterface } from "../../../Response/Icons/IconResponseInterface"
import { StateResponseInterface } from "../../../Response/State/StateResponseInterface"

export default interface CardViewResponseInterface {
    cardViewID: number,
    cardIcon: IconResponseInterface,
    cardColour: ColourResponseInterface,
    cardViewState: StateResponseInterface,
}