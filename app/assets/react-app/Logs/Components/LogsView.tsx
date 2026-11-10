import * as React from 'react';
import { useState, useCallback, useRef } from 'react';
import { getLogsRequest, LogSearchParams } from '../../Logs/Request/GetLogsRequest';
import DotCircleSpinner from '../../Common/Components/Spinners/DotCircleSpinner';

// ── Types ─────────────────────────────────────────────────────────────────────
interface LogHit {
    '@timestamp'?: string;
    message?: string;
    level?: string;
    monolog_level?: number;
    channel?: string;
    host?: string;
    context?: Record<string, any>;
    [key: string]: any;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
const LOG_LEVELS = ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];

function levelStyle(level: string = ''): { bg: string; text: string } {
    switch (level.toUpperCase()) {
        case 'DEBUG':     return { bg: '#e9ecef', text: '#495057' };
        case 'INFO':      return { bg: '#d1ecf1', text: '#0c5460' };
        case 'NOTICE':    return { bg: '#cce5ff', text: '#004085' };
        case 'WARNING':   return { bg: '#fff3cd', text: '#856404' };
        case 'ERROR':     return { bg: '#f8d7da', text: '#721c24' };
        case 'CRITICAL':  return { bg: '#f5c6cb', text: '#491217' };
        case 'ALERT':     return { bg: '#f1b0b7', text: '#3d0a0e' };
        case 'EMERGENCY': return { bg: '#d63031', text: '#fff' };
        default:          return { bg: '#e9ecef', text: '#495057' };
    }
}

function formatTimestamp(ts?: string): string {
    if (!ts) return '—';
    try {
        return new Date(ts).toLocaleString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
        });
    } catch { return ts; }
}

// ── Log Row ───────────────────────────────────────────────────────────────────
function LogRow({ hit, index }: { hit: LogHit; index: number }) {
    const [expanded, setExpanded] = useState(false);
    const { bg, text } = levelStyle(hit.level);
    const hasContext = hit.context && Object.keys(hit.context).length > 0;

    return (
        <>
            <tr
                style={{ cursor: hasContext ? 'pointer' : 'default', background: index % 2 === 0 ? '#fff' : '#f8f9fc' }}
                onClick={() => hasContext && setExpanded(e => !e)}
            >
                <td style={{ whiteSpace: 'nowrap', fontSize: '0.75rem', color: '#6c757d', paddingRight: 12 }}>
                    {formatTimestamp(hit['@timestamp'])}
                </td>
                <td>
                    <span
                        className="badge"
                        style={{ background: bg, color: text, fontSize: '0.7rem', fontWeight: 600, letterSpacing: '0.04em', padding: '3px 7px', borderRadius: 4 }}
                    >
                        {hit.level ?? '—'}
                    </span>
                </td>
                <td style={{ fontSize: '0.75rem', color: '#6c757d' }}>
                    {hit.channel ?? '—'}
                </td>
                <td style={{ fontSize: '0.82rem', wordBreak: 'break-word', maxWidth: 600 }}>
                    {hit.message ?? '—'}
                    {hasContext && (
                        <span className="ml-2 text-muted" style={{ fontSize: '0.7rem' }}>
                            <i className={`fas fa-chevron-${expanded ? 'up' : 'down'}`} />
                        </span>
                    )}
                </td>
                <td style={{ fontSize: '0.75rem', color: '#6c757d', whiteSpace: 'nowrap' }}>
                    {hit.host ?? '—'}
                </td>
            </tr>
            {expanded && hasContext && (
                <tr style={{ background: '#f0f4ff' }}>
                    <td colSpan={5} style={{ padding: '8px 16px' }}>
                        <pre style={{ margin: 0, fontSize: '0.72rem', color: '#333', whiteSpace: 'pre-wrap', wordBreak: 'break-all' }}>
                            {JSON.stringify(hit.context, null, 2)}
                        </pre>
                    </td>
                </tr>
            )}
        </>
    );
}

// ── LogsView ─────────────────────────────────────────────────────────────────
export default function LogsView() {
    const [form, setForm] = useState<LogSearchParams>({
        keyword: '',
        level: '',
        startDate: '',
        endDate: '',
        limit: 50,
        offset: 0,
    });

    const [hits, setHits] = useState<LogHit[]>([]);
    const [total, setTotal] = useState<number | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [searched, setSearched] = useState(false);

    const abortRef = useRef<AbortController | null>(null);

    const runSearch = useCallback(async (params: LogSearchParams) => {
        abortRef.current?.abort();
        abortRef.current = new AbortController();

        setLoading(true);
        setError(null);
        setSearched(true);

        try {
            const resp = await getLogsRequest({
                keyword: params.keyword || undefined,
                level: params.level || undefined,
                startDate: params.startDate || undefined,
                endDate: params.endDate || undefined,
                limit: params.limit,
                offset: params.offset,
            });
            const data = resp.data.payload;
            setHits(data.hits ?? []);
            setTotal(data.total ?? 0);
        } catch (err: any) {
            if (err?.code !== 'ERR_CANCELED') {
                setError('Failed to fetch logs. Make sure you have admin access and the log index exists.');
                setHits([]);
                setTotal(null);
            }
        } finally {
            setLoading(false);
        }
    }, []);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        runSearch({ ...form, offset: 0 });
        setForm(f => ({ ...f, offset: 0 }));
    };

    const handlePageChange = (newOffset: number) => {
        const updated = { ...form, offset: newOffset };
        setForm(updated);
        runSearch(updated);
    };

    const totalPages = total !== null ? Math.ceil(total / (form.limit ?? 50)) : 0;
    const currentPage = Math.floor((form.offset ?? 0) / (form.limit ?? 50));

    return (
        <div className="container-fluid">
            {/* Page Header */}
            <div className="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 className="h3 mb-0 text-gray-800">
                    <i className="fas fa-fw fa-file-alt mr-2 text-primary" />
                    System Logs
                    <span className="badge badge-pill badge-danger ml-2" style={{ fontSize: '0.55rem', verticalAlign: 'middle' }}>Admin</span>
                </h1>
                {total !== null && (
                    <small className="text-muted d-none d-sm-inline">
                        <strong>{total.toLocaleString()}</strong> matching records
                    </small>
                )}
            </div>

            {/* ── Filter Card ─────────────────────────────────────────── */}
            <div className="card shadow mb-4">
                <div
                    className="card-header py-3 d-flex align-items-center"
                    style={{ background: 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)' }}
                >
                    <h6 className="m-0 font-weight-bold text-white">
                        <i className="fas fa-filter mr-2" />
                        Search &amp; Filter
                    </h6>
                </div>
                <div className="card-body">
                    <form onSubmit={handleSubmit}>
                        <div className="row">
                            {/* Keyword */}
                            <div className="col-lg-4 col-md-6 mb-3">
                                <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                                    <i className="fas fa-search mr-1" />Keyword / Phrase
                                </label>
                                <input
                                    type="text"
                                    className="form-control form-control-sm"
                                    placeholder="Search log messages..."
                                    value={form.keyword}
                                    onChange={e => setForm(f => ({ ...f, keyword: e.target.value }))}
                                />
                            </div>

                            {/* Level */}
                            <div className="col-lg-2 col-md-3 mb-3">
                                <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                                    <i className="fas fa-layer-group mr-1" />Log Level
                                </label>
                                <select
                                    className="form-control form-control-sm"
                                    value={form.level}
                                    onChange={e => setForm(f => ({ ...f, level: e.target.value }))}
                                >
                                    <option value="">All Levels</option>
                                    {LOG_LEVELS.map(l => (
                                        <option key={l} value={l}>{l}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Start Date */}
                            <div className="col-lg-2 col-md-3 mb-3">
                                <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                                    <i className="fas fa-calendar-alt mr-1" />From
                                </label>
                                <input
                                    type="datetime-local"
                                    className="form-control form-control-sm"
                                    value={form.startDate}
                                    onChange={e => setForm(f => ({ ...f, startDate: e.target.value }))}
                                />
                            </div>

                            {/* End Date */}
                            <div className="col-lg-2 col-md-3 mb-3">
                                <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                                    <i className="fas fa-calendar-alt mr-1" />To
                                </label>
                                <input
                                    type="datetime-local"
                                    className="form-control form-control-sm"
                                    value={form.endDate}
                                    onChange={e => setForm(f => ({ ...f, endDate: e.target.value }))}
                                />
                            </div>

                            {/* Limit */}
                            <div className="col-lg-1 col-md-2 mb-3">
                                <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                                    Limit
                                </label>
                                <select
                                    className="form-control form-control-sm"
                                    value={form.limit}
                                    onChange={e => setForm(f => ({ ...f, limit: parseInt(e.target.value) }))}
                                >
                                    {[10, 25, 50, 100, 200, 500].map(n => (
                                        <option key={n} value={n}>{n}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Submit */}
                            <div className="col-lg-1 col-md-2 mb-3 d-flex align-items-end">
                                <button
                                    type="submit"
                                    className="btn btn-primary btn-sm w-100"
                                    disabled={loading}
                                    style={{ borderRadius: 6 }}
                                >
                                    {loading
                                        ? <i className="fas fa-spinner fa-spin" />
                                        : <><i className="fas fa-search mr-1" />Search</>
                                    }
                                </button>
                            </div>
                        </div>

                        {/* Quick clear */}
                        {(form.keyword || form.level || form.startDate || form.endDate) && (
                            <div className="mt-1">
                                <button
                                    type="button"
                                    className="btn btn-link btn-sm p-0 text-muted"
                                    onClick={() => setForm(f => ({ ...f, keyword: '', level: '', startDate: '', endDate: '', offset: 0 }))}
                                >
                                    <i className="fas fa-times mr-1" />Clear filters
                                </button>
                            </div>
                        )}
                    </form>
                </div>
            </div>

            {/* ── Results Card ─────────────────────────────────────────── */}
            <div className="card shadow mb-4">
                <div
                    className="card-header py-3 d-flex align-items-center"
                    style={{ background: 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)' }}
                >
                    <h6 className="m-0 font-weight-bold text-white">
                        <i className="fas fa-list mr-2" />
                        Results
                        {total !== null && (
                            <span className="badge badge-light text-primary ml-2" style={{ fontSize: '0.7rem' }}>
                                {total.toLocaleString()} total
                            </span>
                        )}
                    </h6>
                    {total !== null && totalPages > 1 && (
                        <span className="ml-auto text-white" style={{ fontSize: '0.75rem', opacity: 0.85 }}>
                            Page {currentPage + 1} of {totalPages}
                        </span>
                    )}
                </div>
                <div className="card-body p-0">
                    {loading && (
                        <div className="text-center py-5">
                            <DotCircleSpinner classes="center-spinner" />
                            <p className="text-muted mt-3">Searching logs...</p>
                        </div>
                    )}
                    {error && (
                        <div className="alert alert-danger m-3 d-flex align-items-center" style={{ borderRadius: 8 }}>
                            <i className="fas fa-exclamation-triangle mr-2" />{error}
                        </div>
                    )}
                    {!loading && !error && !searched && (
                        <div className="text-center py-5 text-muted">
                            <i className="fas fa-search fa-3x mb-3 d-block" style={{ opacity: 0.2 }} />
                            Use the filters above and click <strong>Search</strong> to query system logs.
                        </div>
                    )}
                    {!loading && !error && searched && hits.length === 0 && (
                        <div className="text-center py-5 text-muted">
                            <i className="fas fa-inbox fa-3x mb-3 d-block" style={{ opacity: 0.2 }} />
                            No logs found matching your criteria.
                        </div>
                    )}
                    {!loading && !error && hits.length > 0 && (
                        <div style={{ overflowX: 'auto' }}>
                            <table className="table table-hover mb-0" style={{ fontSize: '0.82rem' }}>
                                <thead style={{ background: '#f8f9fc', borderBottom: '2px solid #e3e6f0' }}>
                                    <tr>
                                        <th className="text-xs text-uppercase text-muted font-weight-bold" style={{ whiteSpace: 'nowrap', padding: '10px 12px' }}>Timestamp</th>
                                        <th className="text-xs text-uppercase text-muted font-weight-bold" style={{ padding: '10px 12px' }}>Level</th>
                                        <th className="text-xs text-uppercase text-muted font-weight-bold" style={{ padding: '10px 12px' }}>Channel</th>
                                        <th className="text-xs text-uppercase text-muted font-weight-bold" style={{ padding: '10px 12px' }}>Message</th>
                                        <th className="text-xs text-uppercase text-muted font-weight-bold" style={{ padding: '10px 12px' }}>Host</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {hits.map((hit, i) => (
                                        <React.Fragment key={i}>
                                            <LogRow hit={hit} index={i} />
                                        </React.Fragment>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {!loading && totalPages > 1 && (
                    <div className="card-footer d-flex align-items-center justify-content-between py-2">
                        <small className="text-muted">
                            Showing {(form.offset ?? 0) + 1}–{Math.min((form.offset ?? 0) + (form.limit ?? 50), total ?? 0)} of {total?.toLocaleString()} results
                        </small>
                        <div className="d-flex" style={{ gap: 6 }}>
                            <button
                                className="btn btn-sm btn-outline-primary"
                                disabled={currentPage === 0}
                                onClick={() => handlePageChange(0)}
                                style={{ borderRadius: 6 }}
                            >
                                <i className="fas fa-angle-double-left" />
                            </button>
                            <button
                                className="btn btn-sm btn-outline-primary"
                                disabled={currentPage === 0}
                                onClick={() => handlePageChange((currentPage - 1) * (form.limit ?? 50))}
                                style={{ borderRadius: 6 }}
                            >
                                <i className="fas fa-angle-left" />
                            </button>
                            <span className="btn btn-sm btn-primary disabled" style={{ borderRadius: 6, cursor: 'default' }}>
                                {currentPage + 1} / {totalPages}
                            </span>
                            <button
                                className="btn btn-sm btn-outline-primary"
                                disabled={currentPage >= totalPages - 1}
                                onClick={() => handlePageChange((currentPage + 1) * (form.limit ?? 50))}
                                style={{ borderRadius: 6 }}
                            >
                                <i className="fas fa-angle-right" />
                            </button>
                            <button
                                className="btn btn-sm btn-outline-primary"
                                disabled={currentPage >= totalPages - 1}
                                onClick={() => handlePageChange((totalPages - 1) * (form.limit ?? 50))}
                                style={{ borderRadius: 6 }}
                            >
                                <i className="fas fa-angle-double-right" />
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
