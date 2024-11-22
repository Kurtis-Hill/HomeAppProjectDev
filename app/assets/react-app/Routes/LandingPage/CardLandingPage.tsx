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
                            <CardRowContainer route={route ?? 'index'} horizontal={false} />
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
