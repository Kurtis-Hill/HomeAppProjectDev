import * as React from 'react';
import { NavigateFunction, useNavigate } from "react-router-dom";
import { useState, useEffect, useRef } from 'react';

import UserResponseInterface from '../../Response/UserResponseInterface';
import { getSingleUserRequest } from '../../Request/User/GetSingleUserRequest';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import UpdateUserPasswordInputs from './UpdateUserPasswordInputs';
import UserUpdateRequest, { UserUpdateRequestType } from '../../Request/User/UserUpdateRequest';
import { logoutUrl } from '../../../Common/URLs/CommonURLs';

// ── Editable Field Row ───────────────────────────────────────────────────────
function EditableRow(props: {
    label: string;
    icon: string;
    value: string;
    fieldName: string;
    isEditing: boolean;
    canEdit: boolean;
    inputValue: string;
    onToggle: (e: any) => void;
    onChange: (e: any) => void;
    onAccept: (e: any) => void;
    onDecline: (e: any) => void;
}) {
    const { label, icon, value, fieldName, isEditing, canEdit, inputValue, onToggle, onChange, onAccept, onDecline } = props;

    return (
        <div className="py-3" style={{ borderBottom: '1px solid #e9ecef' }}>
            <div className="d-flex align-items-center justify-content-between">
                <div className="d-flex align-items-center" style={{ minWidth: 0 }}>
                    <div
                        className="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                        style={{ width: 36, height: 36, background: '#eef2ff' }}
                    >
                        <i className={`fas fa-fw fa-${icon} text-primary`} style={{ fontSize: '0.8rem' }} />
                    </div>
                    <div style={{ minWidth: 0 }}>
                        <div className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>{label}</div>
                        {isEditing && canEdit ? (
                            <div className="d-flex align-items-center flex-wrap" style={{ gap: 8 }}>
                                <input
                                    className="form-control form-control-sm"
                                    style={{ maxWidth: 260 }}
                                    name={fieldName}
                                    data-name={fieldName}
                                    value={inputValue ?? ''}
                                    onChange={(e: any) => onChange(e)}
                                    autoFocus
                                />
                                <button
                                    type="button"
                                    className="btn btn-sm btn-success"
                                    data-name={fieldName}
                                    onClick={(e: any) => onAccept(e)}
                                    title="Save"
                                >
                                    <i className="fas fa-check" />
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-sm btn-outline-secondary"
                                    data-name={fieldName}
                                    onClick={(e: any) => onDecline(e)}
                                    title="Cancel"
                                >
                                    <i className="fas fa-times" />
                                </button>
                            </div>
                        ) : (
                            <span className="font-weight-semibold text-gray-800" style={{ fontSize: '0.92rem' }}>
                                {value || <span className="text-muted font-italic">—</span>}
                            </span>
                        )}
                    </div>
                </div>
                {!isEditing && canEdit && (
                    <button
                        type="button"
                        className="btn btn-sm btn-light border ml-2 flex-shrink-0"
                        data-name={fieldName}
                        onClick={(e: any) => onToggle(e)}
                        title={`Edit ${label}`}
                        style={{ borderRadius: 6 }}
                    >
                        <i className="fas fa-pencil-alt fa-xs text-primary" style={{ pointerEvents: 'none' }} />
                    </button>
                )}
            </div>
        </div>
    );
}

// ── UserUpdateForm ───────────────────────────────────────────────────────────
export function UserUpdateForm(props: { userID: number }) {
    const navigate: NavigateFunction = useNavigate();
    const { userID } = props;

    const [userData, setUserData] = useState<Partial<UserResponseInterface>>({});
    const [activeForm, setActiveForm] = useState({
        firstName: false,
        lastName: false,
        email: false,
        password: false,
    });
    const [inputs, setInputs] = useState({
        firstName: '',
        lastName: '',
        email: '',
        newPassword: '',
        oldPassword: '',
    });
    const [loading, setLoading] = useState(false);
    const [successField, setSuccessField] = useState<string | null>(null);
    const originalUserData = useRef<Partial<UserResponseInterface>>({});

    useEffect(() => {
        if (userData.userID !== originalUserData.current.userID || userData.userID === undefined) {
            handleUserChangeRequest(userID);
        }
    });

    const handleUserChangeRequest = async (id: number) => {
        const resp = await getSingleUserRequest(id);
        const data: UserResponseInterface = resp.data.payload;
        setUserData(data);
        originalUserData.current = data;
        setInputs({
            firstName: data.firstName ?? '',
            lastName: data.lastName ?? '',
            email: data.email ?? '',
            newPassword: '',
            oldPassword: '',
        });
    };

    const getFieldName = (event: any): string =>
        event.currentTarget?.dataset?.name
        ?? (event.target as HTMLElement)?.closest?.('[data-name]')?.getAttribute('data-name')
        ?? event.target?.dataset?.name
        ?? event.target?.name;

    const toggleFormInput = (event: any) => {
        const name = getFieldName(event);
        setActiveForm(prev => ({ ...prev, [name]: !prev[name] }));
        if (name !== 'password') {
            setInputs(prev => ({ ...prev, [name]: originalUserData.current[name] ?? '' }));
        }
    };

    const handleFormInputChange = (event: any) => {
        const name = event.target?.dataset?.name || event.target?.name;
        const value = event.target?.value;
        setInputs(prev => ({ ...prev, [name]: value }));
    };

    const sendUserUpdateRequest = async (event: any) => {
        event.preventDefault();
        const name = getFieldName(event);

        let requestData: UserUpdateRequestType = {};
        switch (name) {
            case 'firstName': requestData = { firstName: inputs.firstName }; break;
            case 'lastName':  requestData = { lastName: inputs.lastName };   break;
            case 'email':     requestData = { email: inputs.email };         break;
            case 'password':  requestData = { newPassword: inputs.newPassword, oldPassword: inputs.oldPassword }; break;
            default: throw new Error('Invalid field');
        }

        setLoading(true);
        const resp = await UserUpdateRequest(requestData, userID);
        setLoading(false);

        if (resp.status === 200) {
            setSuccessField(name);
            toggleFormInput(event);
            await handleUserChangeRequest(userID);
            setTimeout(() => setSuccessField(null), 2500);
            if (requestData.email !== undefined) {
                navigate(logoutUrl);
            }
        }
    };

    if (userData.userID === undefined) {
        return <DotCircleSpinner classes="spinner-absolute-center" />;
    }

    const initials = `${userData.firstName?.[0] ?? ''}${userData.lastName?.[0] ?? ''}`.toUpperCase() || '?';
    const memberSince = userData.createdAt
        ? new Date(userData.createdAt as unknown as string).toLocaleDateString(undefined, { year: 'numeric', month: 'long' })
        : null;

    return (
        <div className="row">
            {loading && <DotCircleSpinner classes="spinner-absolute-center" />}

            {/* ── Left: Profile Card ────────────────────────────────── */}
            <div className="col-xl-4 col-lg-5 mb-4">
                <div className="card shadow h-100">
                    <div className="card-header py-3 d-flex align-items-center" style={{ background: 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)' }}>
                        <h6 className="m-0 font-weight-bold text-white">
                            <i className="fas fa-id-card mr-2" />
                            Profile
                        </h6>
                    </div>
                    <div className="card-body text-center py-4">
                        {/* Avatar */}
                        <div
                            className="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style={{
                                width: 80, height: 80,
                                background: 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)',
                                fontSize: '1.8rem', fontWeight: 700, color: '#fff',
                                boxShadow: '0 4px 12px rgba(78,115,223,0.35)'
                            }}
                        >
                            {initials}
                        </div>
                        <h5 className="font-weight-bold text-gray-800 mb-1">
                            {userData.firstName} {userData.lastName}
                        </h5>
                        <p className="text-muted mb-1" style={{ fontSize: '0.82rem' }}>{userData.email}</p>

                        {/* Roles */}
                        {userData.roles && userData.roles.length > 0 && (
                            <div className="mt-2 mb-3">
                                {userData.roles.map((role, i) => (
                                    <span key={i} className={`badge badge-pill mr-1 ${role === 'ROLE_ADMIN' ? 'badge-danger' : 'badge-primary'}`} style={{ fontSize: '0.7rem' }}>
                                        {role === 'ROLE_ADMIN' ? 'Admin' : role === 'ROLE_USER' ? 'User' : role}
                                    </span>
                                ))}
                            </div>
                        )}

                        <hr className="my-3" />

                        {/* Meta info */}
                        <div className="text-left px-2">
                            <div className="d-flex justify-content-between mb-2">
                                <span className="text-xs text-uppercase text-muted font-weight-bold">User ID</span>
                                <span className="font-weight-bold text-gray-700" style={{ fontSize: '0.85rem' }}>#{userData.userID}</span>
                            </div>
                            {userData.groups && (
                                <div className="d-flex justify-content-between mb-2">
                                    <span className="text-xs text-uppercase text-muted font-weight-bold">Group</span>
                                    <span className="font-weight-bold text-gray-700" style={{ fontSize: '0.85rem' }}>{userData.groups.groupName}</span>
                                </div>
                            )}
                            {memberSince && (
                                <div className="d-flex justify-content-between">
                                    <span className="text-xs text-uppercase text-muted font-weight-bold">Member Since</span>
                                    <span className="font-weight-bold text-gray-700" style={{ fontSize: '0.85rem' }}>{memberSince}</span>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* ── Right: Account Details + Password ───────────────── */}
            <div className="col-xl-8 col-lg-7">

                {/* Success toast */}
                {successField && (
                    <div className="alert alert-success alert-dismissible d-flex align-items-center mb-3 py-2" role="alert" style={{ borderRadius: 8 }}>
                        <i className="fas fa-check-circle mr-2" />
                        <span><strong>{successField.charAt(0).toUpperCase() + successField.slice(1)}</strong> updated successfully.</span>
                    </div>
                )}

                {/* Account Information Card */}
                <div className="card shadow mb-4">
                    <div className="card-header py-3 d-flex align-items-center" style={{ background: 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)' }}>
                        <h6 className="m-0 font-weight-bold text-white">
                            <i className="fas fa-user-edit mr-2" />
                            Account Information
                        </h6>
                        <span className="ml-auto badge badge-light text-primary" style={{ fontSize: '0.7rem' }}>
                            Click <i className="fas fa-pencil-alt fa-xs" /> to edit
                        </span>
                    </div>
                    <div className="card-body px-4 py-2">
                        <EditableRow
                            label="First Name"
                            icon="user"
                            fieldName="firstName"
                            value={userData.firstName}
                            isEditing={activeForm.firstName}
                            canEdit={userData.canEdit}
                            inputValue={inputs.firstName}
                            onToggle={toggleFormInput}
                            onChange={handleFormInputChange}
                            onAccept={sendUserUpdateRequest}
                            onDecline={toggleFormInput}
                        />
                        <EditableRow
                            label="Last Name"
                            icon="user"
                            fieldName="lastName"
                            value={userData.lastName}
                            isEditing={activeForm.lastName}
                            canEdit={userData.canEdit}
                            inputValue={inputs.lastName}
                            onToggle={toggleFormInput}
                            onChange={handleFormInputChange}
                            onAccept={sendUserUpdateRequest}
                            onDecline={toggleFormInput}
                        />
                        <EditableRow
                            label="Email Address"
                            icon="envelope"
                            fieldName="email"
                            value={userData.email}
                            isEditing={activeForm.email}
                            canEdit={userData.canEdit}
                            inputValue={inputs.email}
                            onToggle={toggleFormInput}
                            onChange={handleFormInputChange}
                            onAccept={sendUserUpdateRequest}
                            onDecline={toggleFormInput}
                        />
                        {userData.canEdit && (
                            <div className="pt-2 pb-1">
                                <small className="text-muted">
                                    <i className="fas fa-info-circle mr-1 text-warning" />
                                    Changing your email address will log you out and require you to log in again.
                                </small>
                            </div>
                        )}
                    </div>
                </div>

                {/* Security Card */}
                <div className="card shadow mb-4">
                    <div className="card-header py-3" style={{ background: 'linear-gradient(135deg, #1cc88a 0%, #13855c 100%)' }}>
                        <h6 className="m-0 font-weight-bold text-white">
                            <i className="fas fa-lock mr-2" />
                            Security
                        </h6>
                    </div>
                    <div className="card-body px-4 py-3">
                        {activeForm.password && userData.canEdit ? (
                            <UpdateUserPasswordInputs
                                toggleUserInput={(e: Event) => toggleFormInput(e)}
                                updateUser={(e: Event) => sendUserUpdateRequest(e)}
                                onInputChange={(e: Event) => handleFormInputChange(e)}
                            />
                        ) : (
                            <div className="d-flex align-items-center justify-content-between py-1">
                                <div className="d-flex align-items-center">
                                    <div
                                        className="rounded-circle d-flex align-items-center justify-content-center mr-3"
                                        style={{ width: 36, height: 36, background: '#d1fae5' }}
                                    >
                                        <i className="fas fa-key text-success" style={{ fontSize: '0.8rem' }} />
                                    </div>
                                    <div>
                                        <div className="text-xs font-weight-bold text-uppercase text-muted mb-1" style={{ letterSpacing: '0.05em' }}>Password</div>
                                        <span className="text-gray-600" style={{ fontSize: '0.9rem', letterSpacing: '0.2em' }}>••••••••</span>
                                    </div>
                                </div>
                                {userData.canEdit && (
                                    <button
                                        type="button"
                                        className="btn btn-sm btn-outline-success"
                                        data-name="password"
                                        onClick={(e: any) => toggleFormInput(e)}
                                        style={{ borderRadius: 6 }}
                                    >
                                        <i className="fas fa-key mr-1" style={{ pointerEvents: 'none' }} />
                                        Change Password
                                    </button>
                                )}
                            </div>
                        )}
                    </div>
                </div>

            </div>
        </div>
    );
}
