import * as React from 'react';
import { SensorTypesEnum } from '../../../Sensors/Enum/SensorTypesEnum';
import { BaseCard } from '../../../Common/Components/BaseCard';
import { getSensorTypeColour } from '../../../Sensors/Enum/SensorTypeColours';
import { getFaPrefix } from '../../../Common/FontAwesomeHelper';

interface BoolReadingData {
    currentReading: boolean;
    expectedReading?: boolean;
    requestedReading?: boolean;
    readingType: string;
    updatedAt: string;
    readingSymbol?: string | null;
}

export function BoolCurrentSensorReadingsCardView(props: {
    cardViewID: number;
    sensorType: SensorTypesEnum;
    sensorName: string;
    room: string;
    sensorData: BoolReadingData[];
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

    const sensorRoom = props.room;
    const iconName   = (cardIcon ?? 'microchip').toLowerCase();
    const typeColour = getSensorTypeColour(String(sensorType));

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

            {/* ── Bool reading rows ──────────────────────── */}
            {sensorData.map((data, i) => {
                const isOn            = data.currentReading === true;
                const matchesExpected = data.expectedReading === undefined || data.currentReading === data.expectedReading;

                return (
                    <div key={i} className="mb-2">
                        <div className="d-flex justify-content-between align-items-center">
                            <span
                                className="small font-weight-bold text-uppercase"
                                style={{ color: typeColour, letterSpacing: '0.05em' }}
                            >
                                <i className="fas fa-circle fa-xs mr-1" style={{ color: typeColour }} />
                                {data.readingType}
                            </span>

                            <span
                                className={`badge badge-pill px-2 py-1 font-weight-bold ${isOn ? 'badge-success' : 'badge-secondary'}`}
                                style={{ fontSize: '0.75rem', letterSpacing: '0.05em' }}
                                title={matchesExpected ? 'Matches expected state' : `Expected: ${data.expectedReading ? 'ON' : 'OFF'}`}
                            >
                                <i className={`fas fa-${isOn ? 'toggle-on' : 'toggle-off'} mr-1`} />
                                {isOn ? 'ON' : 'OFF'}
                                {!matchesExpected && (
                                    <i className="fas fa-exclamation-circle ml-1 text-warning" title={`Expected: ${data.expectedReading ? 'ON' : 'OFF'}`} />
                                )}
                            </span>
                        </div>
                        <div className="text-muted mt-1" style={{ fontSize: '0.65rem' }}>{data.updatedAt}</div>
                    </div>
                );
            })}
        </BaseCard>
    );
}
