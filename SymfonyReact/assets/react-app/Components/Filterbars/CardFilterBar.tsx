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

export default function CardFilterBar(props: {filterParams: CardFilterBarInterface|[]; addFilterParams: (filterParams: {type: string, value: string}) => void, removeFilterParams: (filterParams: {type: string, value: string}) => void}) {
    const [showFilters, setShowFilters] = useState<boolean>(false);

    const addFilterParams = props.addFilterParams;

    const removeFilterParams = props.removeFilterParams;

    const itemDropdownToggleClass: string = showFilters === true ? 'show' : '';

    const readingTypesString = 'readingTypes';

    const sensorTypesString = 'sensorTypes';

    const toggleShowFilters = (): void => {
        setShowFilters(!showFilters);
    }

    const buildCardFilterForm = (): React => {
        const handleClick = (e: Event) => {
            console.log('e been clicked', e);
            const inputCheckElement = e.target as HTMLInputElement;
            console.log('checked check', inputCheckElement.checked, inputCheckElement.value);
            switch (inputCheckElement.name) {
                case readingTypesString:
                    if (inputCheckElement.checked === true) {
                        removeFilterParams({type: 'readingType', value: inputCheckElement.value});
                    }
                    if (inputCheckElement.checked === false) {
                        addFilterParams({type: 'readingType', value: inputCheckElement.value});
                    }
                    // let hey = ['hi' => '2'];
                    break;
                case sensorTypesString:
                    console.log('2');
                    break;
                default:
                    throw Error('filter option not recognized')
            }

            console.log('event name and id', inputCheckElement.name, inputCheckElement.value);
        }
        
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
                                                    <input onChange={handleClick} defaultChecked type="checkbox" name={sensorTypesString} value={sensorType.sensorTypeID} />
                                                    <label style={{padding: '2%'}} htmlFor={sensorType.sensorTypeName}>{capitalizeFirstLetter(sensorType.sensorTypeName)}</label>
                                                </div>
                                            </React.Fragment>
                                        ))
                                    }
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
