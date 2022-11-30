import React, { Component, createContext } from 'react';

export const SensorDataContext = createContext();

export default class SensorDataContextProvider extends Component {
    state: { sensorReadingTypes: string[]; sensorTypes: string[]; };
    constructor(props: any) {
        super(props);
        this.state = {
            sensorReadingTypes: [],
            sensorTypes: [],
        }
    }

    componentDidMount() {
        this.handleSensorDataRequest();
    }


    handleSensorDataRequest = () => {
        if (
            this.state.sensorReadingTypes.length === 0
            || this.state.sensorTypes.length === 0
        ) {             
                //get sensorData request
        }
    }

    render() {
        return (
            <SensorDataContext.Provider value={{
                sensorReadingTypes: this.state.sensorReadingTypes,
                sensorTypes: this.state.sensorTypes,
            }}>
                 
            </SensorDataContext.Provider>
        );
    }
}