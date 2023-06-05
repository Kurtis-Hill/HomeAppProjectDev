import { ReadingTypesEnum } from "./ReadingTypesEnum";

export interface ReadingTypeBoundaryUpdateInput {
    readingType: ReadingTypesEnum,
    highReading: number,
    lowReading: number,
    constRecord: boolean,
}