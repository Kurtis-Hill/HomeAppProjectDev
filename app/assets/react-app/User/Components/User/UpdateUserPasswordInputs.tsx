import * as React from 'react';
import { checkAdmin } from '../../../Authentication/Session/UserSessionHelper';

export default function UpdateUserPasswordInputs(props: {
    toggleUserInput: (event: Event) => void,
    updateUser: (event: Event) => void,
    onInputChange: (event: Event) => void,
}) {
    const { toggleUserInput, updateUser, onInputChange } = props;

    return (
        <React.Fragment>
            <div className="row">
                <div className={`col-sm-${checkAdmin() !== false ? '6' : '12'} mb-3 mb-sm-0`}>
                    <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                        New Password
                    </label>
                    <input
                        type="password"
                        className="form-control form-control-sm"
                        name="newPassword"
                        data-name="newPassword"
                        placeholder="Enter new password"
                        onChange={(e: any) => onInputChange(e)}
                        autoFocus
                    />
                </div>
                {checkAdmin() !== false && (
                    <div className="col-sm-6 mb-3 mb-sm-0">
                        <label className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>
                            Current Password
                        </label>
                        <input
                            type="password"
                            className="form-control form-control-sm"
                            name="oldPassword"
                            data-name="oldPassword"
                            placeholder="Enter current password"
                            onChange={(e: any) => onInputChange(e)}
                        />
                    </div>
                )}
            </div>
            <div className="d-flex mt-3" style={{ gap: 8 }}>
                <button
                    type="button"
                    className="btn btn-sm btn-success"
                    data-name="password"
                    onClick={(e: any) => updateUser(e)}
                >
                    <i className="fas fa-check mr-1" style={{ pointerEvents: 'none' }} />
                    Update Password
                </button>
                <button
                    type="button"
                    className="btn btn-sm btn-outline-secondary"
                    data-name="password"
                    onClick={(e: any) => toggleUserInput(e)}
                >
                    <i className="fas fa-times mr-1" style={{ pointerEvents: 'none' }} />
                    Cancel
                </button>
            </div>
        </React.Fragment>
    );
}
