import * as React from 'react';
import { Link } from "react-router-dom";

import { webappURL } from '../../../Common/URLs/CommonURLs';

export default function HomeAppButton() {
    return (
        <Link to={`${webappURL}index`} className="sidebar-brand d-flex align-items-center justify-content-center">
            <div className="sidebar-brand-icon rotate-n-15">
                <i className="fas fa-home" />
            </div>
            <div className="sidebar-brand-text mx-3">Home App <sup>2</sup></div>
        </Link>
    );
}
