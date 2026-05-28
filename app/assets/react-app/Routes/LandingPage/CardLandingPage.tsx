import * as React from 'react';
import { CardRowContainer } from '../../UserInterface/Components/CardRowContainer';

export function CardLandingPage(props: {route?: string}) {
    const { route } = props;

    return (
        <React.Fragment>
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content">
                    <div className="container-fluid">
                        <div className="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 className="h3 mb-0 text-gray-800">
                                <i className="fas fa-tachometer-alt mr-2 text-primary" />
                                Sensor Dashboard
                            </h1>
                            <div className="d-none d-sm-flex align-items-center">
                                <span className="badge badge-pill mr-2"
                                      style={{ background: '#1cc88a', color: '#fff', padding: '5px 10px' }}>
                                    <i className="fas fa-circle fa-xs mr-1" />Live
                                </span>
                                <small className="text-muted">Auto-refreshing sensor readings</small>
                            </div>
                        </div>
                        <CardRowContainer route={route ?? 'index'} horizontal={false} />
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
