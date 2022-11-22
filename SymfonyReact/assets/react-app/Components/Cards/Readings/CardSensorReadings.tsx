import * as React from 'react';
import { Outlet } from 'react-router-dom';

export function CardSensorReadings(props) {
    const content = props.content ?? '';
    return (
        <React.Fragment>
            <div style={{ position: "absolute", top: '2%', right: '5%'}}>SensorType</div>
            <div className="row no-gutters align-items-center">
                <div className="col mr-2">
                    <div className="d-flex font-weight-bold text text-uppercase mb-1">Sensor Name:</div>
                    <div className="d-flex text text-uppercase mb-1">Room:</div>
                        {content}
                        {/* <Outlet /> */}
                </div>
            </div>
        </React.Fragment>                
    );
}