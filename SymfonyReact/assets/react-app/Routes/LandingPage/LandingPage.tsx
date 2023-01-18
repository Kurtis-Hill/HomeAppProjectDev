import * as React from 'react';
import { Context } from 'react';
import {  useOutletContext  } from "react-router-dom";
import { CardRowContainer } from '../../UserInterface/Cards/Components/CardRowContainer';

export function LandingPage() {
    const [setRefreshNavDataFlag, showErrorAnnouncementFlash]: Context<Array<(newValue: boolean) => void>> = useOutletContext();

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
