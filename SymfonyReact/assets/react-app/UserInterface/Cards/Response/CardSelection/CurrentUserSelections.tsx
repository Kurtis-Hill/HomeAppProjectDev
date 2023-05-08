import { StateResponseInterface } from "../../../Response/State/StateResponseInterface"
import { ColourResponseInterface } from "../../../Response/Colour/ColourResponseInterface"
import { IconResponseInterface } from "../../../Response/Icons/IconResponseInterface"

export interface CurrentUserSelections {
    icons: IconResponseInterface
    colours: ColourResponseInterface
    states: StateResponseInterface
}