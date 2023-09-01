import * as React from 'react';
import { Link } from "react-router-dom";

import { webappURL } from '../../../Common/URLs/CommonURLs';

export default function UserSettingsButton() {
    return (
        <Link to={`${webappURL}user-settings`}>
            <div className="nav-link">
                <i className="fas fa-fw fa-user" />
                <span>User Settings</span>
            </div>
        </Link>
    );
}