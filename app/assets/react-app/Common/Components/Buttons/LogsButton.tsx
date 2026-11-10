import * as React from 'react';
import { Link } from "react-router-dom";
import { webappURL } from '../../URLs/CommonURLs';

export default function LogsButton() {
    return (
        <Link to={`${webappURL}admin/logs`}>
            <div className="nav-link">
                <i className="fas fa-fw fa-file-alt" />
                <span>System Logs</span>
            </div>
        </Link>
    );
}
