import * as React from 'react';
import {useState, useEffect, Context} from 'react';
import { Link, useOutletContext  } from "react-router-dom";
import { CardReadingHandler } from '../../Components/Cards/Readings/CardReadingHandler';

export function LandingPage() {

    const [setRefreshNavDataFlag, showErrorAnnouncementFlash]: Context<Array<(newValue: boolean) => void>> = useOutletContext();

    // setRefreshNavDataFlag(true);
    // showErrorAnnouncementFlash(['test'], 'test');
    // console.log('ele', setRefreshNavDataFlag)
    // console.log('ele2', showErrorAnnouncementFlash)
    // const [showErrorAnnouncementFlash]: Context<(errors: Array<string>, title: string) => void> = useOutletContext();

    // useEffect(() => {
    //     setTimeout(() => {
    //         console.log('lol')
            // showErrorAnnouncementFlash(['test'], 'test');
            // setRefreshNavDataFlag(true);
        // }, 1000);

        // console.log('unmounting')
        // setRefreshNavDataFlag(true)
    // }, []);

    // setRefreshNavDataFlag(true);
    // console.log(refreshNavbar, 'refresh nav bool')
    return (
        <React.Fragment>
            <div id="content-wrapper" className="d-flex flex-column">
                <div id="content"> 
                    <div className="container-fluid">
                        <CardReadingHandler
                            route={'index'} 
                        />
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
