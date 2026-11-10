import * as React from 'react';
import { UserUpdateForm } from './UserUpdateForm';
import {getUserSessionValue} from "../../../Authentication/Session/UserSessionHelper";

export function UserSettingsView() {
    const userID: number = parseInt(getUserSessionValue('userID'));

    return (
        <div className="container-fluid">
            {/* Page Header */}
            <div className="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 className="h3 mb-0 text-gray-800">
                    <i className="fas fa-fw fa-user-circle mr-2 text-primary" />
                    Account Settings
                    <span className="badge badge-pill badge-success ml-2" style={{ fontSize: '0.55rem', verticalAlign: 'middle' }}>
                        Profile
                    </span>
                </h1>
                <small className="text-muted d-none d-sm-inline">Manage your personal information and security settings</small>
            </div>

            <UserUpdateForm userID={userID} />
        </div>
    );
}
