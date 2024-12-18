import * as React from 'react';
import { useState } from 'react';
import SensorDataContext from '../../../Sensors/Contexts/SensorDataContext';
import { SensorDataContextDataInterface } from '../../../Sensors/DataProviders/SensorDataProvider';
import SmallWhiteBoxDisplay from '../../../Common/Components/Elements/SmallWhiteBoxDisplay';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import ReadingTypeResponseInterface from '../../../Sensors/Response/ReadingTypes/ReadingTypeResponseInterface';
import { SensorTypeResponseInterface } from '../../../Sensors/Response/SensorType/SensorTypeResponseInterface';
import CardFilterButton from '../Buttons/CardFilterButton';
import {AcceptButton} from "../../../Common/Components/Buttons/AcceptButton";


export type CardFilterBarType = {
    sensorTypes?: string[];
    readingTypes?: string[];
}

export default function CardFilterBarView(props: {
    filterParams: CardFilterBarType|[]; 
    addFilterParams: (filterParams: {type: string, value: string}) => void,
    removeFilterParams: (filterParams: {type: string, value: string}) => void,
    setCardRefreshTimer: (timer: number) => void,
    cardRefreshTimer: number,
    setCardFilterSettingsForceReset?: (boolean) => void,
}) {
    const [showFilters, setShowFilters] = useState<boolean>(false);

    const [internalSliderValue, setInternalSliderValue] = useState<number>(4000);

    const addFilterParams = props.addFilterParams;

    const removeFilterParams = props.removeFilterParams;

    const cardRefreshTimer = props.cardRefreshTimer;

    const setCardRefreshTimer = props.setCardRefreshTimer;

    const setCardFilterSettingsForceReset = props.setCardFilterSettingsForceReset;

    const itemDropdownToggleClass: string = showFilters === true ? 'show' : '';

    const readingTypesString = 'readingType';

    const sensorTypesString = 'sensorType';

    const cardRefreshMaxLimit: number = 60;

    const cardRefreshMinLimit: number = 1;

    const toggleShowFilters = (): void => {
        setShowFilters(!showFilters);
    }

    const buildCardFilterForm = (): React => {
        const handleClick = (e: Event) => {
            const inputCheckElement = e.currentTarget as HTMLInputElement;
            let filterParamType: string;
            switch (inputCheckElement.name) {
                case readingTypesString:
                    filterParamType = readingTypesString;
                    break;
                case sensorTypesString:
                    filterParamType = sensorTypesString;
                    break;
                default:
                    throw Error('filter option not recognized')
            }

            if (inputCheckElement.checked === true) {
                removeFilterParams({type: filterParamType, value: inputCheckElement.value});
            }
            if (inputCheckElement.checked === false) {
                addFilterParams({type: filterParamType, value: inputCheckElement.value});
            }
        }


        const handleSliderChange = (e: Event) => {
            const sliderElement = e.currentTarget as HTMLInputElement;
            const newRefreshValue: number = parseInt(sliderElement.value);

            if (newRefreshValue >= cardRefreshMinLimit && newRefreshValue <= cardRefreshMaxLimit) {
                const refreshValueInMillieSeconds = newRefreshValue * 1000;          
                setInternalSliderValue(refreshValueInMillieSeconds);
            }
        }

        const setSliderValue = (e: Event) => {
            console.log('set slider value', internalSliderValue);
            setCardRefreshTimer(internalSliderValue);
        }
        
        const cardRefreshTimerInSeconds = cardRefreshTimer / 1000;
        return (
            <SensorDataContext.Consumer>
                {(sensorData: SensorDataContextDataInterface) => (
                    <>
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <span className="one-line-text">Reading types</span>
                                    {
                                        sensorData.readingTypes.map((sensorReadingType: ReadingTypeResponseInterface, index: Number) => (
                                            <React.Fragment key={index}>
                                                <div style={{padding: '2%'}} className="row">
                                                    <input onChange={handleClick} defaultChecked type="checkbox" name={readingTypesString} value={sensorReadingType.readingTypeName} />
                                                    <label style={{padding: '2%'}} htmlFor={sensorReadingType.readingTypeName}>{capitalizeFirstLetter(sensorReadingType.readingTypeName)}</label>
                                                </div>
                                            </React.Fragment>
                                        ))
                                    }
                                </div>
                                <div className="col">
                                    <span className="one-line-text">Sensor types</span>
                                    {
                                        sensorData.sensorTypes.map((sensorType: SensorTypeResponseInterface, index: number) => (
                                            <React.Fragment key={index}>
                                                <div style={{padding: '2%'}} className="row">
                                                    <input onChange={handleClick} defaultChecked type="checkbox" name={sensorTypesString} value={sensorType.sensorTypeName} />
                                                    <label style={{padding: '2%'}} htmlFor={sensorType.sensorTypeName}>{capitalizeFirstLetter(sensorType.sensorTypeName)}</label>
                                                </div>
                                            </React.Fragment>
                                        ))
                                    }
                                </div>
                            </div>
                            <div className="card-filter-slider">
                                <label className="form-label" htmlFor="card-refresh-slider">{ cardRefreshTimerInSeconds } seconds for data refresh</label>
                                <div className="range">
                                    <input onChange={handleSliderChange} min={cardRefreshMinLimit} max={cardRefreshMaxLimit} value={ internalSliderValue } type="range" className="form-range" id="card-refresh-slider" />
                                    <AcceptButton clickEvent={setSliderValue} dataName="cardRefreshSlider" />
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </SensorDataContext.Consumer>
        );
    }

    return (
        <div className="card-filter-bar-container">
            <CardFilterButton toggleShowFilters={toggleShowFilters} />
                <SmallWhiteBoxDisplay
                    classes={`${itemDropdownToggleClass} card-filter-box`}
                    heading={'Card Display Filters'}
                >
                    { buildCardFilterForm() }
                </SmallWhiteBoxDisplay>
        </div>
    );
}
