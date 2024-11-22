import * as React from 'react';
import { cardIndex } from '../../Common/URLs/CommonURLs';
import { Link } from 'react-router-dom';


export function LandingPage() {
    return (
        <>
            <Link to={cardIndex('index')}>
                <span>Sensor Card Display Index</span>
            </Link>
        </>
    );
}
