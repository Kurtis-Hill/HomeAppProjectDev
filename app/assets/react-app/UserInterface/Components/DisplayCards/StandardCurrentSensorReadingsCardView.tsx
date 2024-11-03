import * as React from 'react';
import { CurrentReadingSensorDataOutputFactory, CurrentSensorDataTypeStandardCard } from '../../Factories/CurrentReadingSensorDataOutputFactory';
import { SensorTypesEnum } from '../../../Sensors/Enum/SensorTypesEnum';
import { BaseCard } from '../../../Common/Components/BaseCard';

export function StandardCurrentSensorReadingsCardView(props: {
    cardViewID: number;
    sensorType: SensorTypesEnum;
    sensorName: string; 
    room: string; 
    sensorData: CurrentSensorDataTypeStandardCard[];
    cardIcon: string; 
    loadingCardModalView: boolean;
    setLoadingCardModalView: (loadingCardModalView: boolean) => void;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    cardColour?: string|undefined;
}): React {
    const cardViewID: number = props.cardViewID;
    const sensorType: SensorTypesEnum = props.sensorType;
    const sensorName: string = props.sensorName;
    const sensorRoom: string = props.room;
    const cardIcon: string = props.cardIcon ?? 'dog';
    const cardColour: string|undefined = props.cardColour;
    const sensorData: CurrentSensorDataTypeStandardCard[] = props.sensorData;
    const setSelectedCardForQuickUpdate: (cardViewID: number) => void = props.setSelectedCardForQuickUpdate;

    return (
        <BaseCard 
            colour={cardColour} 
            setVariableToUpdate={setSelectedCardForQuickUpdate}
            id={cardViewID}
            loading={props.loadingCardModalView}
            setCardLoading={props.setLoadingCardModalView}
        >
            <React.Fragment>
                <div className="card-sensor-type-display-name"><b>{sensorType}</b></div>
                <div className="row no-gutters align-items-center">
                    <div className="col mr-2">
                        <div className="d-flex font-weight-bold text text-uppercase mb-1">{sensorName}</div>
                        <div className="d-flex text text-uppercase mb-1 card-room-text-display">{sensorRoom}</div>
                            <CurrentReadingSensorDataOutputFactory 
                                sensorData={sensorData}
                                sensorType={sensorType}
                            />
                    </div>
                    <div className="col-auto">
                        <i className={`fas fa-2x text-gray-300 fa-${cardIcon}`}></i>
                    </div>
                </div>
            </React.Fragment>                
        </BaseCard>
    );
}
