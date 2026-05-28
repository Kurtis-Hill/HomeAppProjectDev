import * as React from 'react';
import { useState } from 'react';
import SensorDataContext from '../../../Sensors/Contexts/SensorDataContext';
import { SensorDataContextDataInterface } from '../../../Sensors/DataProviders/SensorDataProvider';
import { capitalizeFirstLetter } from '../../../Common/StringFormatter';
import ReadingTypeResponseInterface from '../../../Sensors/Response/ReadingTypes/ReadingTypeResponseInterface';
import { SensorTypeResponseInterface } from '../../../Sensors/Response/SensorType/SensorTypeResponseInterface';
import { getSensorTypeColour, getReadingTypeColour } from '../../../Sensors/Enum/SensorTypeColours';

export type CardFilterBarType = {
    sensorTypes?: string[];
    readingTypes?: string[];
}


export default function CardFilterBarView(props: {
    filterParams: CardFilterBarType|[];
    addFilterParams: (filterParams: {type: string, value: string}) => void;
    removeFilterParams: (filterParams: {type: string, value: string}) => void;
    setCardRefreshTimer: (timer: number) => void;
    cardRefreshTimer: number;
    setCardFilterSettingsForceReset?: (v: boolean) => void;
}) {
    const [showFilters, setShowFilters] = useState<boolean>(false);
    const [sliderSeconds, setSliderSeconds] = useState<number>(props.cardRefreshTimer / 1000);

    const { addFilterParams, removeFilterParams, setCardRefreshTimer } = props;

    const cardRefreshMaxLimit = 60;
    const cardRefreshMinLimit = 1;
    const readingTypesString  = 'readingType';
    const sensorTypesString   = 'sensorType';

    const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const el = e.currentTarget;
        let filterParamType: string;
        switch (el.name) {
            case readingTypesString: filterParamType = readingTypesString; break;
            case sensorTypesString:  filterParamType = sensorTypesString;  break;
            default: return;
        }
        if (el.checked === true) {
            removeFilterParams({ type: filterParamType, value: el.value });
        } else {
            addFilterParams({ type: filterParamType, value: el.value });
        }
    };

    const handleSliderChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const v = parseInt(e.currentTarget.value);
        if (v >= cardRefreshMinLimit && v <= cardRefreshMaxLimit) {
            setSliderSeconds(v);
        }
    };

    const applyRefreshRate = () => {
        setCardRefreshTimer(sliderSeconds * 1000);
    };

    return (
        <div className="card shadow mb-4">
            {/* ── Header (always visible, click to toggle) ── */}
            <div
                className="card-header py-3 d-flex align-items-center justify-content-between"
                onClick={() => setShowFilters(!showFilters)}
                style={{ cursor: 'pointer', userSelect: 'none' }}
            >
                <h6 className="m-0 font-weight-bold text-primary">
                    <i className="fas fa-sliders-h mr-2" />
                    Display Filters
                </h6>
                <div className="d-flex align-items-center">
                    <span className="badge badge-light mr-2" style={{ fontSize: '0.75rem' }}>
                        <i className="fas fa-clock mr-1 text-muted" />{sliderSeconds}s refresh
                    </span>
                    <i className={`fas text-gray-400 fa-chevron-${showFilters ? 'up' : 'down'}`} />
                </div>
            </div>

            {/* ── Collapsible body ── */}
            {showFilters && (
                <div className="card-body">
                    <SensorDataContext.Consumer>
                        {(sensorData: SensorDataContextDataInterface) => (
                            <div className="row">
                                {/* Reading type filters */}
                                <div className="col-md-4 mb-3">
                                    <label className="font-weight-bold small text-uppercase mb-2 d-block text-gray-600">
                                        <i className="fas fa-chart-line mr-1" />Reading Types
                                    </label>
                                    {sensorData.readingTypes.map((rt: ReadingTypeResponseInterface, i: number) => {
                                        const colour = getReadingTypeColour(rt.readingTypeName);
                                        return (
                                            <div key={i} className="custom-control custom-checkbox mb-2">
                                                <input
                                                    type="checkbox"
                                                    className="custom-control-input"
                                                    id={`rt-${rt.readingTypeName}`}
                                                    name={readingTypesString}
                                                    value={rt.readingTypeName}
                                                    defaultChecked
                                                    onChange={handleCheckboxChange}
                                                />
                                                <label className="custom-control-label" htmlFor={`rt-${rt.readingTypeName}`}>
                                                    <span style={{ color: colour, fontWeight: 600 }}>
                                                        <i className="fas fa-circle fa-xs mr-1" style={{ color: colour }} />
                                                        {capitalizeFirstLetter(rt.readingTypeName)}
                                                    </span>
                                                </label>
                                            </div>
                                        );
                                    })}
                                </div>

                                {/* Sensor type filters */}
                                <div className="col-md-4 mb-3">
                                    <label className="font-weight-bold small text-uppercase mb-2 d-block text-gray-600">
                                        <i className="fas fa-microchip mr-1" />Sensor Types
                                    </label>
                                    {sensorData.sensorTypes.map((st: SensorTypeResponseInterface, i: number) => {
                                        const colour = getSensorTypeColour(st.sensorTypeName);
                                        return (
                                            <div key={i} className="custom-control custom-checkbox mb-2">
                                                <input
                                                    type="checkbox"
                                                    className="custom-control-input"
                                                    id={`st-${st.sensorTypeName}`}
                                                    name={sensorTypesString}
                                                    value={st.sensorTypeName}
                                                    defaultChecked
                                                    onChange={handleCheckboxChange}
                                                />
                                                <label className="custom-control-label" htmlFor={`st-${st.sensorTypeName}`}>
                                                    <span style={{ color: colour, fontWeight: 600 }}>
                                                        <i className="fas fa-circle fa-xs mr-1" style={{ color: colour }} />
                                                        {capitalizeFirstLetter(st.sensorTypeName)}
                                                    </span>
                                                </label>
                                            </div>
                                        );
                                    })}
                                </div>

                                {/* Refresh rate */}
                                <div className="col-md-4 mb-3">
                                    <label className="font-weight-bold small text-uppercase mb-2 d-block text-gray-600">
                                        <i className="fas fa-sync-alt mr-1" />Refresh Rate
                                    </label>
                                    <div className="d-flex justify-content-between mb-1">
                                        <small className="text-muted">{cardRefreshMinLimit}s</small>
                                        <small className="font-weight-bold text-primary">{sliderSeconds}s</small>
                                        <small className="text-muted">{cardRefreshMaxLimit}s</small>
                                    </div>
                                    <input
                                        type="range"
                                        className="custom-range"
                                        id="card-refresh-slider"
                                        min={cardRefreshMinLimit}
                                        max={cardRefreshMaxLimit}
                                        value={sliderSeconds}
                                        onChange={handleSliderChange}
                                    />
                                    <button
                                        className="btn btn-primary btn-sm btn-block mt-2"
                                        onClick={applyRefreshRate}
                                    >
                                        <i className="fas fa-check mr-1" />Apply Rate
                                    </button>
                                </div>
                            </div>
                        )}
                    </SensorDataContext.Consumer>
                </div>
            )}
        </div>
    );
}
