import * as React from 'react';
import { Link } from "react-router-dom";

import { webappURL } from '../../URLs/CommonURLs';

export default function QueryButton() {
    return (
        <Link to={`${webappURL}query`}>
            <div className="nav-link">
                <i className="fas fa-fw fa-question" />
                <span>Query</span>
            </div>
        </Link>
    );
}
