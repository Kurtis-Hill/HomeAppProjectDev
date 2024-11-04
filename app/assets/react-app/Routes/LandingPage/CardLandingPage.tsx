import * as React from 'react';
import { CardRowContainer } from '../../UserInterface/Components/CardRowContainer';
import {useParams} from "react-router-dom";

export function CardLandingPage() {
    const params = useParams();
    const entity: number = parseInt(params.entityID);

    return (
        <React.Fragment>
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content"> 
                    <div className="container-fluid">
                        <div className="row">
                            <CardRowContainer route={window.location.href.includes('room') ? `room/${entity}` : window.location.href.includes('device') ? `device/${entity}` : 'index'} />
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
