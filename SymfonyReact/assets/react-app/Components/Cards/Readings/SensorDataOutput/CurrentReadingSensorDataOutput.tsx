import * as React from 'react';
import { CardCurrentReadingResponse } from '../../../../Response/User/CardData/Interfaces/CardDataResponseInterface';
import { capitalizeFirstLetter } from '../../../../Common/StringFormatter';

export function CurrentReadingSensorDataOutput(props: { sensorData: CardCurrentReadingResponse[]; }) {
    const sensorData: Array<CardCurrentReadingResponse> = props.sensorData;

    const senorReadingStyle = (highReading: number, lowReading: number, currentReading: number) => {
        return (currentReading >= highReading) 
          ? 'text-red' 
          : (currentReading <= lowReading) 
            ? 'text-blue' 
            : 'text-gray-800';
      }

    return (
        <React.Fragment>
            {
                sensorData.map((data: CardCurrentReadingResponse, index: number) => {
                    return (
                        <React.Fragment key={index}>
                            <div className={`card-font mb-0 font-weight-bold ${senorReadingStyle(data.hightReading, data.lowReading, data.currentReading)}`}>
                                { capitalizeFirstLetter(data.readingType)}: {data.currentReading}{data.readingSymbol }
                            </div>
                            <div className="card-font mb-0 text-gray-400">updated@{data.updatedAt}</div>                            
                        </React.Fragment>
                    )
                })  
            }          
        </React.Fragment>
    )
}