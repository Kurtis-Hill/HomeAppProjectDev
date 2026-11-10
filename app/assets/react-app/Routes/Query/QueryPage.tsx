import * as React from 'react';
import { useState, useCallback } from 'react';
import axios, { AxiosResponse } from 'axios';
import { apiURL } from '../../Common/URLs/CommonURLs';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';

type ReadingType = 'temperature' | 'humidity' | 'analog' | 'latitude';
type Direction   = 'above' | 'below';

interface OutOfBoundsReading {
    sensorReadingID: number;
    sensorReading:   number;
    createdAt:       string;
    readingType:     ReadingType;
}

interface FilterState {
    readingTypes:    ReadingType[];
    threshold:       string;
    direction:       Direction | '';
    startDate:       string;
    endDate:         string;
    sensorReadingID: string;
    limit:           number;
    offset:          number;
}

const ALL_READING_TYPES: ReadingType[] = ['temperature', 'humidity', 'analog', 'latitude'];

const DEFAULT_FILTERS: FilterState = {
    readingTypes:    [...ALL_READING_TYPES],
    threshold:       '',
    direction:       '',
    startDate:       '',
    endDate:         '',
    sensorReadingID: '',
    limit:           100,
    offset:          0,
};

const TYPE_COLOURS: Record<ReadingType, string> = {
    temperature: '#e74c3c',
    humidity:    '#3498db',
    analog:      '#9b59b6',
    latitude:    '#27ae60',
};

function Badge({ type }: { type: ReadingType }) {
    return (
        <span style={{ background: TYPE_COLOURS[type], color: '#fff', padding: '2px 8px', borderRadius: 12, fontSize: '0.75rem', fontWeight: 600, textTransform: 'uppercase' as const }}>
            {type}
        </span>
    );
}

async function fetchOutOfBoundsReadings(f: FilterState): Promise<AxiosResponse> {
    const p = new URLSearchParams();
    f.readingTypes.forEach((t) => p.append('readingTypes[]', t));
    if (f.threshold !== '')       p.append('threshold', f.threshold);
    if (f.direction !== '')       p.append('direction', f.direction);
    if (f.startDate !== '')       p.append('startDate', f.startDate);
    if (f.endDate !== '')         p.append('endDate', f.endDate);
    if (f.sensorReadingID !== '') p.append('sensorReadingID', f.sensorReadingID);
    p.append('limit', String(f.limit));
    p.append('offset', String(f.offset));
    return axios.get(`${apiURL}out-of-bounds/readings?${p.toString()}`);
}

function validate(f: FilterState): string[] {
    const errs: string[] = [];
    if (f.readingTypes.length === 0)              errs.push('Select at least one reading type.');
    if (f.threshold !== '' && f.direction === '') errs.push('Direction is required when a threshold is set.');
    if (f.startDate !== '' && f.endDate === '')   errs.push('End date is required when a start date is set.');
    if (f.endDate !== '' && f.startDate === '')   errs.push('Start date is required when an end date is set.');
    if (f.limit < 1 || f.limit > 1000)           errs.push('Limit must be between 1 and 1000.');
    if (f.offset < 0)                             errs.push('Offset must be 0 or greater.');
    return errs;
}

function FilterPanel(props: {
    filters:  FilterState;
    onChange: (f: FilterState) => void;
    onSearch: () => void;
    onReset:  () => void;
    loading:  boolean;
    errors:   string[];
}) {
    const { filters: f, onChange, onSearch, onReset, loading, errors } = props;
    const set = <K extends keyof FilterState>(k: K, v: FilterState[K]) => onChange({ ...f, [k]: v });
    const toggleType = (t: ReadingType) =>
        set('readingTypes', f.readingTypes.includes(t)
            ? f.readingTypes.filter((x) => x !== t)
            : [...f.readingTypes, t]);

    return (
        <div className="card shadow mb-4">
            <div className="card-header py-3">
                <h6 className="m-0 font-weight-bold text-primary">
                    <i className="fas fa-filter mr-2" />Filter Out-of-Bounds Readings
                </h6>
            </div>
            <div className="card-body">
                {errors.length > 0 && (
                    <div className="alert alert-danger py-2">
                        {errors.map((e, i) => <div key={i}><i className="fas fa-exclamation-circle mr-1" />{e}</div>)}
                    </div>
                )}
                <div className="row">
                    <div className="col-md-3 mb-3">
                        <label className="font-weight-bold small text-uppercase mb-1 d-block">Reading Types</label>
                        {ALL_READING_TYPES.map((t) => (
                            <div key={t} className="custom-control custom-checkbox mb-1">
                                <input
                                    type="checkbox"
                                    className="custom-control-input"
                                    id={`rt-${t}`}
                                    checked={f.readingTypes.includes(t)}
                                    onChange={() => toggleType(t)}
                                />
                                <label className="custom-control-label" htmlFor={`rt-${t}`}>
                                    <span style={{ color: TYPE_COLOURS[t], fontWeight: 600 }}>
                                        {t.charAt(0).toUpperCase() + t.slice(1)}
                                    </span>
                                </label>
                            </div>
                        ))}
                    </div>

                    <div className="col-md-3 mb-3">
                        <label className="font-weight-bold small text-uppercase mb-1 d-block">Threshold Value</label>
                        <input
                            type="number"
                            className="form-control form-control-sm"
                            placeholder="e.g. 25.5"
                            value={f.threshold}
                            step="any"
                            onChange={(e) => { set('threshold', e.target.value); if (!e.target.value) set('direction', ''); }}
                        />
                        <label className="font-weight-bold small text-uppercase mt-2 mb-1 d-block">Direction</label>
                        <select
                            className="form-control form-control-sm"
                            value={f.direction}
                            disabled={f.threshold === ''}
                            onChange={(e) => set('direction', e.target.value as Direction | '')}
                        >
                            <option value="">— select —</option>
                            <option value="above">Above (≥ threshold)</option>
                            <option value="below">Below (≤ threshold)</option>
                        </select>
                        <small className="text-muted">Find readings beyond this value.</small>
                    </div>

                    <div className="col-md-3 mb-3">
                        <label className="font-weight-bold small text-uppercase mb-1 d-block">Start Date / Time</label>
                        <input
                            type="datetime-local"
                            className="form-control form-control-sm"
                            value={f.startDate}
                            onChange={(e) => set('startDate', e.target.value)}
                        />
                        <label className="font-weight-bold small text-uppercase mt-2 mb-1 d-block">End Date / Time</label>
                        <input
                            type="datetime-local"
                            className="form-control form-control-sm"
                            value={f.endDate}
                            onChange={(e) => set('endDate', e.target.value)}
                        />
                    </div>

                    <div className="col-md-3 mb-3">
                        <label className="font-weight-bold small text-uppercase mb-1 d-block">Sensor Reading ID</label>
                        <input
                            type="number"
                            className="form-control form-control-sm"
                            placeholder="e.g. 42"
                            min={1}
                            value={f.sensorReadingID}
                            onChange={(e) => set('sensorReadingID', e.target.value)}
                        />
                        <small className="text-muted d-block mb-2">Overrides other filters.</small>
                        <label className="font-weight-bold small text-uppercase mb-1 d-block">Limit / Offset</label>
                        <div className="d-flex">
                            <input type="number" className="form-control form-control-sm mr-1" min={1} max={1000} value={f.limit} onChange={(e) => set('limit', Number(e.target.value))} />
                            <input type="number" className="form-control form-control-sm" min={0} value={f.offset} onChange={(e) => set('offset', Number(e.target.value))} />
                        </div>
                    </div>
                </div>

                <div className="d-flex">
                    <button className="btn btn-primary btn-sm mr-2" onClick={onSearch} disabled={loading}>
                        {loading
                            ? <><i className="fas fa-spinner fa-spin mr-1" />Searching&hellip;</>
                            : <><i className="fas fa-search mr-1" />Search</>}
                    </button>
                    <button className="btn btn-secondary btn-sm" onClick={onReset} disabled={loading}>
                        <i className="fas fa-undo mr-1" />Reset
                    </button>
                </div>
            </div>
        </div>
    );
}

function SummaryBar({ readings }: { readings: OutOfBoundsReading[] }) {
    const icons: Record<ReadingType, string> = {
        temperature: 'fa-thermometer-half',
        humidity:    'fa-tint',
        analog:      'fa-wave-square',
        latitude:    'fa-map-marker-alt',
    };
    return (
        <div className="row mb-4">
            {ALL_READING_TYPES.map((t) => (
                <div key={t} className="col-xl-3 col-md-6 mb-2">
                    <div className="card shadow h-100 py-2" style={{ borderLeft: `4px solid ${TYPE_COLOURS[t]}` }}>
                        <div className="card-body py-2">
                            <div className="row no-gutters align-items-center">
                                <div className="col">
                                    <div className="text-xs font-weight-bold text-uppercase mb-1" style={{ color: TYPE_COLOURS[t] }}>{t}</div>
                                    <div className="h5 mb-0 font-weight-bold text-gray-800">{readings.filter((r) => r.readingType === t).length}</div>
                                </div>
                                <div className="col-auto">
                                    <i className={`fas fa-2x text-gray-300 ${icons[t]}`} />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}

function ResultsTable(props: { readings: OutOfBoundsReading[]; filters: FilterState; onPageChange: (offset: number) => void }) {
    const { readings, filters, onPageChange } = props;
    const fmt = (iso: string) => { try { return new Date(iso).toLocaleString(); } catch { return iso; } };

    return (
        <div className="card shadow mb-4">
            <div className="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 className="m-0 font-weight-bold text-primary">
                    <i className="fas fa-list mr-2" />Results
                    <span className="badge badge-primary ml-1">{readings.length}</span>
                </h6>
                <small className="text-muted">
                    Showing {filters.offset + 1}&ndash;{filters.offset + readings.length} &middot; limit {filters.limit}
                </small>
            </div>

            {readings.length === 0
                ? (
                    <div className="card-body text-center text-muted py-5">
                        <i className="fas fa-inbox fa-3x d-block mb-3" />
                        No out-of-bounds readings matched your query.
                    </div>
                )
                : (
                    <>
                        <div className="table-responsive">
                            <table className="table table-bordered table-hover table-sm mb-0">
                                <thead className="thead-light">
                                    <tr>
                                        <th>Sensor Reading ID</th>
                                        <th>Reading Value</th>
                                        <th>Type</th>
                                        <th>Recorded At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {readings.map((r, i) => (
                                        <tr key={i}>
                                            <td className="text-monospace">{r.sensorReadingID}</td>
                                            <td>
                                                <strong style={{ color: TYPE_COLOURS[r.readingType] }}>
                                                    {r.sensorReading.toFixed ? r.sensorReading.toFixed(2) : r.sensorReading}
                                                </strong>
                                            </td>
                                            <td><Badge type={r.readingType} /></td>
                                            <td className="text-muted small">{fmt(r.createdAt)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="card-footer d-flex justify-content-between align-items-center">
                            <button
                                className="btn btn-outline-secondary btn-sm"
                                disabled={filters.offset === 0}
                                onClick={() => onPageChange(Math.max(0, filters.offset - filters.limit))}
                            >
                                <i className="fas fa-chevron-left mr-1" />Previous
                            </button>
                            <span className="text-muted small">Offset: {filters.offset}</span>
                            <button
                                className="btn btn-outline-secondary btn-sm"
                                disabled={readings.length < filters.limit}
                                onClick={() => onPageChange(filters.offset + filters.limit)}
                            >
                                Next <i className="fas fa-chevron-right ml-1" />
                            </button>
                        </div>
                    </>
                )
            }
        </div>
    );
}

export default function QueryPage() {
    const [filters, setFilters]   = useState<FilterState>({ ...DEFAULT_FILTERS });
    const [readings, setReadings] = useState<OutOfBoundsReading[] | null>(null);
    const [loading, setLoading]   = useState(false);
    const [errors, setErrors]     = useState<string[]>([]);
    const [apiError, setApiError] = useState<string | null>(null);
    const [searched, setSearched] = useState(false);

    const handleSearch = useCallback(async (overrideFilters?: FilterState) => {
        const active = overrideFilters ?? filters;
        const ve = validate(active);
        if (ve.length > 0) { setErrors(ve); return; }
        setErrors([]);
        setApiError(null);
        setLoading(true);
        setSearched(true);
        const norm = (dt: string) => dt ? new Date(dt).toISOString() : '';
        try {
            const res = await fetchOutOfBoundsReadings({ ...active, startDate: norm(active.startDate), endDate: norm(active.endDate) });
            const payload = res.data?.payload;
            setReadings(Array.isArray(payload) ? payload : []);
        } catch (err: any) {
            const msg = err?.response?.data?.errors;
            setApiError(
                Array.isArray(msg) ? msg.join(' ')
                    : typeof msg === 'string' ? msg
                        : 'An unexpected error occurred. Please try again.'
            );
            setReadings([]);
        } finally {
            setLoading(false);
        }
    }, [filters]);

    const handlePageChange = (newOffset: number) => {
        const updated = { ...filters, offset: newOffset };
        setFilters(updated);
        handleSearch(updated);
    };

    const handleReset = () => {
        setFilters({ ...DEFAULT_FILTERS });
        setReadings(null);
        setErrors([]);
        setApiError(null);
        setSearched(false);
    };

    return (
        <div id="content-wrapper" className="d-flex flex-column">
            <div id="content">
                <div className="container-fluid">
                    <div className="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 className="h3 mb-0 text-gray-800">
                            <i className="fas fa-search mr-2 text-primary" />
                            Out-of-Bounds Query
                        </h1>
                        <small className="text-muted">
                            Query sensor readings that exceeded their boundaries across Elasticsearch indices.
                        </small>
                    </div>

                    <FilterPanel
                        filters={filters}
                        onChange={setFilters}
                        onSearch={() => handleSearch()}
                        onReset={handleReset}
                        loading={loading}
                        errors={errors}
                    />

                    {apiError && (
                        <div className="alert alert-danger mb-4">
                            <i className="fas fa-exclamation-triangle mr-2" />{apiError}
                        </div>
                    )}

                    {loading && <DotCircleSpinner spinnerSize={4} classes="center-spinner-card-row" />}

                    {!loading && searched && readings !== null && (
                        <>
                            <SummaryBar readings={readings} />
                            <ResultsTable readings={readings} filters={filters} onPageChange={handlePageChange} />
                        </>
                    )}

                    {!loading && !searched && (
                        <div className="text-center text-muted py-5">
                            <i className="fas fa-database fa-4x d-block mb-3 text-gray-300" />
                            <p>Set your filters above and click <strong>Search</strong> to query Elasticsearch.</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
