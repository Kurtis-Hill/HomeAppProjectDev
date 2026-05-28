import * as React from 'react';
import { useState } from 'react';
import SensorResponseInterface from '../../Response/Sensor/SensorResponseInterface';
import { SensorDisplayTable } from '../SensorDisplayTable';
import { ReadingTypeDisplayTable } from '../ReadingTypes/ReadingTypeDisplayTable';

export function UpdateSingleSensorCard(props: { sensor: SensorResponseInterface; refreshData?: () => void }) {
    const { sensor, refreshData } = props;
    const [expanded, setExpanded] = useState<boolean>(false);

    return (
        <div className="sensor-card">
            <SensorDisplayTable sensor={sensor} refreshData={refreshData} />

            <button
                className="sensor-card-expand-btn"
                onClick={() => setExpanded(prev => !prev)}
                type="button"
            >
                <span>{expanded ? 'Hide Reading Boundaries' : 'Show Reading Boundaries'}</span>
                <i className={`fas fa-chevron-${expanded ? 'up' : 'down'}`} style={{ fontSize: '0.7rem' }} />
            </button>

            {expanded && sensor.sensorReadingTypes && (
                <div className="reading-types-section">
                    <ReadingTypeDisplayTable
                        sensorReadingTypes={sensor.sensorReadingTypes}
                        canEdit={sensor.canEdit}
                        refreshData={refreshData}
                    />
                </div>
            )}
        </div>
    );
}
