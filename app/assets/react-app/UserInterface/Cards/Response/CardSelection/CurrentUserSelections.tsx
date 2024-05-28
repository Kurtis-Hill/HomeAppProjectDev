import { ColourResponseInterface } from "../../../Response/Colour/ColourResponseInterface"
import { IconResponseInterface } from "../../../Response/Icons/IconResponseInterface"
import StateResponseInterface from "../../../Response/State/StateResponseInterface"

export interface CurrentUserSelections {
    icons: IconResponseInterface
    colours: ColourResponseInterface
    states: StateResponseInterface
}