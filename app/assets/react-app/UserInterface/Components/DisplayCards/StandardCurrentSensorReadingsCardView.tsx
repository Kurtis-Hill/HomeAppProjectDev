import * as React from 'react';
import { CurrentSensorDataTypeStandardCard } from '../../Factories/CurrentReadingSensorDataOutputFactory';
import { SensorTypesEnum } from '../../../Sensors/Enum/SensorTypesEnum';
import { BaseCard } from '../../../Common/Components/BaseCard';
import { getSensorTypeColour, getReadingTypeColour } from '../../../Sensors/Enum/SensorTypeColours';
import {getFaPrefix} from "../../../Common/FontAwesomeHelper";

function formatUpdatedAt(updatedAt: string): string {
    return updatedAt;
    // try {
        // const d = typeof updatedAt === 'string' ? new Date(updatedAt) : updatedAt;
        // return d.toLocaleString(undefined, { dateStyle: 'short', timeStyle: 'short' });
    // } catch {
    //     return String(updatedAt);
    // }
}

function ReadingRow({ data }: { data: CurrentSensorDataTypeStandardCard }) {
    const { currentReading, highReading: highReading, lowReading, readingSymbol, readingType, lastState, updatedAt } = data;

    const isAboveHigh = currentReading > highReading;
    const isBelowLow  = currentReading < lowReading;
    const isOob       = isAboveHigh || isBelowLow;

    const range    = highReading - lowReading;
    const barPct   = range > 0
        ? Math.min(100, Math.max(0, ((currentReading - lowReading) / range) * 100))
        : 50;
    const barColor = isAboveHigh ? '#e74c3c' : isBelowLow ? '#3498db' : '#1cc88a';
    const valueClass = isAboveHigh ? 'text-danger' : isBelowLow ? 'text-primary' : 'text-success';
    const trendClass = lastState === 'up'   ? 'fa-arrow-up text-danger'
                     : lastState === 'down' ? 'fa-arrow-down text-primary'
                     : 'fa-minus text-muted';

    const typeColour = getReadingTypeColour(readingType);
    const displayValue = typeof currentReading === 'number' ? currentReading.toFixed(1) : currentReading;

    return (
        <div className="mb-3">
            {/* Reading type + value row */}
            <div className="d-flex justify-content-between align-items-center mb-1">
                <span className="small font-weight-bold text-uppercase" style={{ color: typeColour, letterSpacing: '0.05em' }}>
                    <i className="fas fa-circle fa-xs mr-1" style={{ color: typeColour }} />
                    {readingType}
                </span>
                <div className="d-flex align-items-center">
                    <span className={`h6 mb-0 font-weight-bold ${valueClass}`}>
                        {displayValue}{readingSymbol}
                    </span>
                    <i className={`fas fa-xs ml-1 ${trendClass}`} />
                    {isOob && (
                        <span
                            className="badge badge-danger ml-1"
                            style={{ fontSize: '0.6rem', letterSpacing: '0.05em' }}
                            title={isAboveHigh ? `Above high boundary (${highReading}${readingSymbol})` : `Below low boundary (${lowReading}${readingSymbol})`}
                        >
                            OOB
                        </span>
                    )}
                </div>
            </div>

            {/* Progress bar */}
            <div className="progress mb-1" style={{ height: 5, borderRadius: 3 }}>
                <div
                    className="progress-bar"
                    role="progressbar"
                    style={{
                        width: `${barPct}%`,
                        backgroundColor: barColor,
                        transition: 'width 0.4s ease',
                        borderRadius: 3,
                    }}
                />
            </div>

            {/* Low / updated / High labels */}
            <div className="d-flex justify-content-between" style={{ fontSize: '0.65rem' }}>
                <span className="text-muted">↓ {lowReading}{readingSymbol}</span>
                <span className="text-muted">{formatUpdatedAt(updatedAt)}</span>
                <span className="text-muted">↑ {highReading}{readingSymbol}</span>
            </div>
        </div>
    );
}

export function StandardCurrentSensorReadingsCardView(props: {
    cardViewID: number;
    sensorType: SensorTypesEnum;
    sensorName: string;
    room: string;
    sensorData: CurrentSensorDataTypeStandardCard[];
    cardIcon: string;
    loadingCardModalView: boolean;
    setLoadingCardModalView: (v: boolean) => void;
    setSelectedCardForQuickUpdate: (cardViewID: number) => void;
    cardColour?: string | undefined;
}): React.ReactElement {
    const {
        cardViewID, sensorType, sensorName, cardIcon, cardColour,
        sensorData, setSelectedCardForQuickUpdate,
    } = props;

    const sensorRoom  = props.room;
    const iconName    = (cardIcon ?? 'microchip').toLowerCase();
    const typeColour  = getSensorTypeColour(String(sensorType));

    return (
        <BaseCard
            colour={cardColour}
            cardClasses="col-xl-3 col-md-6 mb-4"
            setVariableToUpdate={setSelectedCardForQuickUpdate}
            id={cardViewID}
            loading={props.loadingCardModalView}
            setCardLoading={props.setLoadingCardModalView}
        >
            {/* ── Card header ─────────────────────────────── */}
            <div className="d-flex align-items-start justify-content-between mb-2">
                <div style={{ minWidth: 0 }}>
                    <div className="font-weight-bold text-gray-800 text-truncate" style={{ fontSize: '0.95rem' }}>
                        {sensorName}
                    </div>
                    <div className="small text-muted text-truncate">
                        <i className="fas fa-map-marker-alt fa-xs mr-1" />{sensorRoom}
                    </div>
                </div>
                <div className="d-flex flex-column align-items-end ml-2 flex-shrink-0">
                    <i className={`${getFaPrefix(iconName)} fa-lg text-gray-200 fa-${iconName} mb-1`} />
                    <span
                        className="badge badge-pill"
                        style={{ background: typeColour, color: '#fff', fontSize: '0.6rem' }}
                    >
                        {sensorType}
                    </span>
                </div>
            </div>

            <hr className="my-2" style={{ borderColor: '#e3e6f0' }} />

            {/* ── Reading rows ─────────────────────────────── */}
            {sensorData.map((data, i) => (
                <React.Fragment key={i}>
                    <ReadingRow data={data as CurrentSensorDataTypeStandardCard} />
                </React.Fragment>
            ))}
        </BaseCard>
    );
}
