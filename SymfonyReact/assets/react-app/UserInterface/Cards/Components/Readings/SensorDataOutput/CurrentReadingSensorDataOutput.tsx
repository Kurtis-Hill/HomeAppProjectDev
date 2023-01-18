import * as React from 'react';

import { CardCurrentSensorDataInterface } from './CurrentReadingDataDisplayInterface';
import { capitalizeFirstLetter } from '../../../../../Common/StringFormatter';

export function CurrentReadingSensorDataOutput(props: { sensorData: CardCurrentSensorDataInterface[]|undefined; }) {
    const sensorData: Array<CardCurrentSensorDataInterface> = props.sensorData ?? undefined;

    const sensorReadingAgainstLowHighBoundary = (highReading: number, lowReading: number, currentReading: number) => {
        return (currentReading >= highReading) 
          ? 'text-red' 
          : (currentReading <= lowReading) 
            ? 'text-blue' 
            : 'text-gray-800';
      }

    //   console.log('sensorData', sensorData)
    if (sensorData !== undefined && sensorData.length > 0) {
        return (
            <React.Fragment>
                {
                    sensorData.map((data: CardCurrentSensorDataInterface, index: number) => {
                        const lastStateIcon: string = (data.lastState !== 'same') ? `chevron-${data.lastState}` : 'horizontal-rule';   
                        const iconColour: string = (data.lastState === 'up') ? 'red' : (data.lastState === 'down') ? 'blue' : 'gray';
    
                        return (
                            <React.Fragment key={index}>
                                <div>
                                    <div className={`card-font mb-0 font-weight-bold ${sensorReadingAgainstLowHighBoundary(data.hightReading, data.lowReading, data.currentReading)}`}>
                                        { capitalizeFirstLetter(data.readingType)}: {data.currentReading}{data.readingSymbol } <i style={{ color:iconColour }} className={`fas fa-1x fa-${lastStateIcon}`}> </i>
                                    </div>
                                    <div className="card-font mb-0 text-gray-400">updated@{data.updatedAt}</div>                            
                                </div>
                            </React.Fragment>
                        )
                    })  
                }          
            </React.Fragment>
        )
    }
}