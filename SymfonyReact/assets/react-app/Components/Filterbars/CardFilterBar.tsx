import * as React from 'react';
import { useState } from 'react';

import SmallWhiteBoxDisplay from '../../../OldApp/js/components/DisplayBoxes/SmallWhiteBoxDisplay';
import SensorDataContext from '../../Contexts/SensorData/SensorDataContext';
import CardFilterButton from '../Cards/Buttons/CardFilterButton';
import { CardFilterBarInterface } from './CardFilterBarInterface';

import { SensorDataContextDataInterface } from '../../Components/SensorDataProvider/SensorDataProvider';
import { SensorReadingTypeResponseInterface } from '../../Response/Sensor/SensorReadingTypeResponseInterface';
import { SensorTypeResponseInterface } from '../../Response/Sensor/SensorTypeResponseInterface';

import { capitalizeFirstLetter } from '../../Common/StringFormatter';

export default function CardFilterBar(props: {
    filterParams: CardFilterBarInterface|[]; 
    addFilterParams: (filterParams: {type: string, value: string}) => void,
    removeFilterParams: (filterParams: {type: string, value: string}) => void,
    setCardRefreshTimer: (timer: number) => void,
    cardRefreshTimer: number,
}) {
    const [showFilters, setShowFilters] = useState<boolean>(false);

    const addFilterParams = props.addFilterParams;

    const removeFilterParams = props.removeFilterParams;

    const cardRefreshTimer = props.cardRefreshTimer;

    const setCardRefreshTimer = props.setCardRefreshTimer;

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
            // console.log('e been clicked', e);
            const inputCheckElement = e.currentTarget as HTMLInputElement;
            console.log('clicked', e.target, inputCheckElement)
            let filterParamType: string;
            // console.log('checked check', inputCheckElement.checked, inputCheckElement.value);
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

            console.log('bla', newRefreshValue, cardRefreshMinLimit, cardRefreshMaxLimit)
            if (newRefreshValue >= cardRefreshMinLimit && newRefreshValue <= cardRefreshMaxLimit) {
                console.log('nope')
                const refreshValueInMillieSeconds = newRefreshValue * 1000;          
                setCardRefreshTimer(refreshValueInMillieSeconds);
            }
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
                                        sensorData.sensorReadingTypeData.map((sensorReadingType: SensorReadingTypeResponseInterface, index: Number) => (
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
                                        sensorData.sensorTypes.map((sensorType: SensorTypeResponseInterface, index:Number) => (
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
                                    <input onChange={ handleSliderChange } min={cardRefreshMinLimit} max={cardRefreshMaxLimit} value={ cardRefreshTimerInSeconds } type="range" className="form-range" id="card-refresh-slider" />
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
                    content={ buildCardFilterForm() }
                />
        </div>
    );
}
