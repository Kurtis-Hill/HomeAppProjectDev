import * as React from 'react';
import { CardRowContainer } from '../../UserInterface/Components/CardRowContainer';

export function CardLandingPage(props: {route?: string}) {
    const { route } = props;

    return (
        <React.Fragment>
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content"> 
                    <div className="container-fluid">
                        <div className="row">
                            {/*<CardRowContainer route={window.location.href.includes('room') ? `room/${entity}` : window.location.href.includes('device') ? `device/${entity}` : 'index'} />*/}
                            <CardRowContainer route={route ?? 'index'} />
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
