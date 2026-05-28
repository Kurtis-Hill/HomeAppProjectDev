import * as React from 'react';
import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { getAllUsersRequest } from '../../Request/User/GetAllUsersRequest';
import { deleteUserRequest } from '../../Request/User/DeleteUserRequest';
import { addUserToHomeGroupRequest } from '../../Request/User/AddUserToHomeGroupRequest';
import UserResponseInterface from '../../Response/UserResponseInterface';
import GroupMappingResponseInterface from '../../Response/GroupMapping/GroupMappingResponseInterface';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import { getUserSessionValue } from '../../../Authentication/Session/UserSessionHelper';
import { apiURL } from '../../../Common/URLs/CommonURLs';

const HOME_GROUP_NAME = 'home-app-group';

// ── Confirm Delete Modal ─────────────────────────────────────────────────────
function ConfirmDeleteModal(props: {
    user: UserResponseInterface;
    onConfirm: () => void;
    onCancel: () => void;
    loading: boolean;
}) {
    const { user, onConfirm, onCancel, loading } = props;
    return (
        <div className="modal d-block" tabIndex={-1} style={{ background: 'rgba(0,0,0,0.5)' }}>
            <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content shadow-lg" style={{ borderRadius: 10 }}>
                    <div className="modal-header border-0 pb-0">
                        <h5 className="modal-title font-weight-bold text-danger">
                            <i className="fas fa-exclamation-triangle mr-2" />
                            Confirm Delete
                        </h5>
                        <button type="button" className="close" onClick={onCancel}>
                            <span>&times;</span>
                        </button>
                    </div>
                    <div className="modal-body pt-2">
                        <p className="text-gray-700">
                            Are you sure you want to permanently delete{' '}
                            <strong>{user.firstName} {user.lastName}</strong>?
                        </p>
                        <div className="alert alert-warning py-2 mb-0" style={{ borderRadius: 6 }}>
                            <i className="fas fa-info-circle mr-1" />
                            <small>This action cannot be undone. All data associated with this user will be removed.</small>
                        </div>
                    </div>
                    <div className="modal-footer border-0 pt-0">
                        <button className="btn btn-outline-secondary btn-sm" onClick={onCancel}>
                            <i className="fas fa-times mr-1" />Cancel
                        </button>
                        <button
                            className="btn btn-danger btn-sm"
                            onClick={onConfirm}
                            disabled={loading}
                        >
                            {loading
                                ? <><i className="fas fa-spinner fa-spin mr-1" />Deleting...</>
                                : <><i className="fas fa-trash mr-1" />Delete User</>
                            }
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

// ── Role Badge ───────────────────────────────────────────────────────────────
function RoleBadge({ role }: { role: string }) {
    const isAdmin = role === 'ROLE_ADMIN';
    return (
        <span
            className={`badge badge-pill mr-1 ${isAdmin ? 'badge-danger' : 'badge-primary'}`}
            style={{ fontSize: '0.68rem' }}
        >
            {isAdmin ? 'Admin' : role === 'ROLE_USER' ? 'User' : role}
        </span>
    );
}

// ── User Row Card ────────────────────────────────────────────────────────────
function UserRowCard(props: {
    user: UserResponseInterface;
    currentUserID: number;
    onDelete: (user: UserResponseInterface) => void;
    onAddToHomeGroup: (user: UserResponseInterface) => void;
    addingToGroup: boolean;
    inHomeGroup: boolean;
}) {
    const { user, currentUserID, onDelete, onAddToHomeGroup, addingToGroup, inHomeGroup } = props;
    const isSelf = user.userID === currentUserID;
    const initials = `${user.firstName?.[0] ?? ''}${user.lastName?.[0] ?? ''}`.toUpperCase() || '?';
    const memberSince = (() => {
        if (!user.createdAt) return '—';
        // API may return "2022-03-03 16:08:23" (space) or ISO "2022-03-03T16:08:23+00:00"
        const raw = String(user.createdAt).replace(' ', 'T');
        const d = new Date(raw);
        if (isNaN(d.getTime())) return '—';
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    })();

    return (
        <div className="card shadow-sm mb-3" style={{ borderRadius: 10, border: isSelf ? '2px solid #4e73df' : undefined }}>
            <div className="card-body py-3 px-4">
                <div className="d-flex align-items-center">
                    {/* Avatar */}
                    <div
                        className="rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0"
                        style={{
                            width: 48, height: 48,
                            background: isSelf
                                ? 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)'
                                : 'linear-gradient(135deg, #858796 0%, #60616f 100%)',
                            color: '#fff', fontWeight: 700, fontSize: '1.1rem',
                        }}
                    >
                        {initials}
                    </div>

                    {/* Info */}
                    <div className="flex-grow-1 min-width-0">
                        <div className="d-flex align-items-center flex-wrap" style={{ gap: 6 }}>
                            <span className="font-weight-bold text-gray-800 mr-1" style={{ fontSize: '0.95rem' }}>
                                {user.firstName} {user.lastName}
                            </span>
                            {isSelf && (
                                <span className="badge badge-pill badge-info" style={{ fontSize: '0.65rem' }}>You</span>
                            )}
                            {user.roles?.map((r, i) => <React.Fragment key={i}><RoleBadge role={r} /></React.Fragment>)}
                        </div>
                        <div className="text-muted" style={{ fontSize: '0.8rem' }}>
                            <i className="fas fa-envelope mr-1" />{user.email}
                        </div>
                        <div className="d-flex flex-wrap mt-1" style={{ gap: 12, fontSize: '0.75rem' }}>
                            <span className="text-muted">
                                <i className="fas fa-hashtag mr-1 text-primary" />ID: <strong>{user.userID}</strong>
                            </span>
                            {user.groups && (
                                <span className="text-muted">
                                    <i className="fas fa-layer-group mr-1 text-success" />Group: <strong>{user.groups.groupName}</strong>
                                </span>
                            )}
                            <span className="text-muted">
                                <i className="fas fa-calendar-alt mr-1 text-warning" />Joined: <strong>{memberSince}</strong>
                            </span>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="d-flex flex-shrink-0 ml-3" style={{ gap: 6 }}>
                        {inHomeGroup ? (
                            <span
                                className="badge badge-pill badge-success d-flex align-items-center"
                                style={{ fontSize: '0.72rem', padding: '0.35rem 0.65rem' }}
                                title="Already a member of home-app-group"
                            >
                                <i className="fas fa-home mr-1" />In Home Group
                            </span>
                        ) : (
                            <button
                                className="btn btn-sm btn-outline-success"
                                style={{ borderRadius: 6, whiteSpace: 'nowrap' }}
                                onClick={() => onAddToHomeGroup(user)}
                                disabled={addingToGroup}
                                title="Add user to home-app-group"
                            >
                                {addingToGroup
                                    ? <i className="fas fa-spinner fa-spin" />
                                    : <><i className="fas fa-home mr-1" />Add to Home Group</>
                                }
                            </button>
                        )}
                        {!isSelf && user.canDelete && (
                            <button
                                className="btn btn-sm btn-outline-danger"
                                style={{ borderRadius: 6 }}
                                onClick={() => onDelete(user)}
                                title="Delete user"
                            >
                                <i className="fas fa-trash" />
                            </button>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

// ── AdminView ────────────────────────────────────────────────────────────────
export default function AdminView() {
    const currentUserID = parseInt(getUserSessionValue('userID'));

    const [users, setUsers] = useState<UserResponseInterface[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const [userToDelete, setUserToDelete] = useState<UserResponseInterface | null>(null);
    const [deleting, setDeleting] = useState(false);

    const [addingToGroupID, setAddingToGroupID] = useState<number | null>(null);
    /** Set of userIDs that are already mapped to home-app-group */
    const [homeGroupUserIDs, setHomeGroupUserIDs] = useState<Set<number>>(new Set());

    const [toastMessage, setToastMessage] = useState<{ text: string; type: 'success' | 'danger' } | null>(null);
    const [search, setSearch] = useState('');

    const fetchUsers = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const [usersResp, mappingsResp] = await Promise.all([
                getAllUsersRequest(),
                axios.get<{ payload: GroupMappingResponseInterface[] }>(`${apiURL}group-mapping`),
            ]);
            setUsers(usersResp.data.payload ?? []);

            // Build a set of userIDs already in home-app-group
            const mappings: GroupMappingResponseInterface[] = mappingsResp.data.payload ?? [];
            const homeIDs = new Set<number>(
                mappings
                    .filter(m => m.group?.groupName === HOME_GROUP_NAME)
                    .map(m => m.user?.userID)
                    .filter((id): id is number => id !== undefined)
            );
            setHomeGroupUserIDs(homeIDs);
        } catch {
            setError('Failed to load users. You may not have admin access.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchUsers(); }, [fetchUsers]);

    const showToast = (text: string, type: 'success' | 'danger') => {
        setToastMessage({ text, type });
        setTimeout(() => setToastMessage(null), 3000);
    };

    const handleDeleteConfirm = async () => {
        if (!userToDelete) return;
        setDeleting(true);
        try {
            await deleteUserRequest(userToDelete.userID);
            showToast(`${userToDelete.firstName} ${userToDelete.lastName} deleted successfully.`, 'success');
            setUserToDelete(null);
            await fetchUsers();
        } catch {
            showToast('Failed to delete user.', 'danger');
        } finally {
            setDeleting(false);
        }
    };

    const handleAddToHomeGroup = async (user: UserResponseInterface) => {
        setAddingToGroupID(user.userID);
        try {
            await addUserToHomeGroupRequest(user.userID);
            showToast(`${user.firstName} ${user.lastName} added to home-app-group.`, 'success');
            // Mark this user as now being in the home group
            setHomeGroupUserIDs(prev => new Set([...prev, user.userID]));
        } catch (err: any) {
            const msg = err?.response?.data?.errors?.[0] ?? 'Failed to add user to home group.';
            showToast(msg, 'danger');
        } finally {
            setAddingToGroupID(null);
        }
    };

    const filteredUsers = users.filter(u => {
        if (!search.trim()) return true;
        const q = search.toLowerCase();
        return (
            u.firstName?.toLowerCase().includes(q) ||
            u.lastName?.toLowerCase().includes(q) ||
            u.email?.toLowerCase().includes(q) ||
            String(u.userID).includes(q) ||
            u.groups?.groupName?.toLowerCase().includes(q)
        );
    });

    const adminCount = users.filter(u => u.roles?.includes('ROLE_ADMIN')).length;
    const userCount = users.filter(u => !u.roles?.includes('ROLE_ADMIN')).length;

    return (
        <div className="container-fluid">
            {/* Page Header */}
            <div className="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 className="h3 mb-0 text-gray-800">
                    <i className="fas fa-fw fa-users-cog mr-2 text-primary" />
                    User Administration
                    <span className="badge badge-pill badge-danger ml-2" style={{ fontSize: '0.55rem', verticalAlign: 'middle' }}>
                        Admin
                    </span>
                </h1>
                <button
                    className="btn btn-sm btn-primary shadow-sm"
                    onClick={fetchUsers}
                    disabled={loading}
                    style={{ borderRadius: 6 }}
                >
                    <i className={`fas fa-sync-alt mr-1 ${loading ? 'fa-spin' : ''}`} />
                    Refresh
                </button>
            </div>

            {/* Stats row */}
            {!loading && !error && (
                <div className="row mb-4">
                    {[
                        { label: 'Total Users', value: users.length, icon: 'users', color: '#4e73df' },
                        { label: 'Admins', value: adminCount, icon: 'user-shield', color: '#e74a3b' },
                        { label: 'Standard Users', value: userCount, icon: 'user', color: '#1cc88a' },
                    ].map((stat, i) => (
                        <div key={i} className="col-xl-3 col-md-4 mb-3">
                            <div className="card shadow h-100 py-2" style={{ borderLeft: `4px solid ${stat.color}`, borderRadius: 8 }}>
                                <div className="card-body py-2">
                                    <div className="d-flex align-items-center">
                                        <div className="mr-3">
                                            <div className="text-xs font-weight-bold text-uppercase mb-1" style={{ color: stat.color, letterSpacing: '0.05em' }}>
                                                {stat.label}
                                            </div>
                                            <div className="h4 mb-0 font-weight-bold text-gray-800">{stat.value}</div>
                                        </div>
                                        <div className="ml-auto">
                                            <i className={`fas fa-${stat.icon} fa-2x`} style={{ color: stat.color, opacity: 0.3 }} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Toast */}
            {toastMessage && (
                <div
                    className={`alert alert-${toastMessage.type} d-flex align-items-center mb-3 py-2`}
                    style={{ borderRadius: 8 }}
                >
                    <i className={`fas fa-${toastMessage.type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2`} />
                    {toastMessage.text}
                </div>
            )}

            {/* User list card */}
            <div className="card shadow mb-4">
                <div
                    className="card-header py-3 d-flex align-items-center"
                    style={{ background: 'linear-gradient(135deg, #4e73df 0%, #224abe 100%)' }}
                >
                    <h6 className="m-0 font-weight-bold text-white">
                        <i className="fas fa-list mr-2" />
                        All Users
                    </h6>
                    <div className="ml-auto" style={{ maxWidth: 280, width: '100%' }}>
                        <div className="input-group input-group-sm">
                            <div className="input-group-prepend">
                                <span className="input-group-text bg-transparent border-right-0" style={{ borderColor: 'rgba(255,255,255,0.3)', color: 'rgba(255,255,255,0.8)' }}>
                                    <i className="fas fa-search" />
                                </span>
                            </div>
                            <input
                                type="text"
                                className="form-control border-left-0"
                                placeholder="Search users..."
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                style={{ background: 'rgba(255,255,255,0.15)', color: '#fff', borderColor: 'rgba(255,255,255,0.3)' }}
                            />
                        </div>
                    </div>
                </div>
                <div className="card-body">
                    {loading && (
                        <div className="text-center py-5">
                            <DotCircleSpinner classes="center-spinner" />
                            <p className="text-muted mt-3">Loading users...</p>
                        </div>
                    )}
                    {error && (
                        <div className="alert alert-danger d-flex align-items-center" style={{ borderRadius: 8 }}>
                            <i className="fas fa-exclamation-triangle mr-2" />
                            {error}
                        </div>
                    )}
                    {!loading && !error && filteredUsers.length === 0 && (
                        <div className="text-center py-5 text-muted">
                            <i className="fas fa-user-slash fa-3x mb-3 d-block" style={{ opacity: 0.3 }} />
                            {search ? 'No users match your search.' : 'No users found.'}
                        </div>
                    )}
                    {!loading && !error && filteredUsers.map(user => (
                        <React.Fragment key={user.userID}>
                            <UserRowCard
                                user={user}
                                currentUserID={currentUserID}
                                onDelete={setUserToDelete}
                                onAddToHomeGroup={handleAddToHomeGroup}
                                addingToGroup={addingToGroupID === user.userID}
                                inHomeGroup={homeGroupUserIDs.has(user.userID)}
                            />
                        </React.Fragment>
                    ))}
                </div>
            </div>

            {/* Delete confirmation modal */}
            {userToDelete && (
                <ConfirmDeleteModal
                    user={userToDelete}
                    onConfirm={handleDeleteConfirm}
                    onCancel={() => setUserToDelete(null)}
                    loading={deleting}
                />
            )}
        </div>
    );
}
