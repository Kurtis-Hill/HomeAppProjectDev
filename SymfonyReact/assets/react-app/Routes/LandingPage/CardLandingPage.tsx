import * as React from 'react';
import { CardRowContainer } from '../../UserInterface/Cards/Components/CardRowContainer';

export function CardLandingPage() {
    return (
        <React.Fragment>
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content"> 
                    <div className="container-fluid">
                        <div className="row">
                            <CardRowContainer route={'index'} classes='' />
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
