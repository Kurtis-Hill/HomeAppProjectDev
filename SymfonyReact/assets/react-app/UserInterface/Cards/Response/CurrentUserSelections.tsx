import { CardStateResponseInterface } from "./CardStateResponseInterface"
import { ColourResponseInterface } from "./ColourResponseInterface"
import { IconResponseInterface } from "./IconResponseInterface"

export interface CurrentUserSelections {
    icons: IconResponseInterface
    colours: ColourResponseInterface
    states: CardStateResponseInterface
}