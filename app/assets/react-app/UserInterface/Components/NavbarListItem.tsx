import * as React from 'react';
import { useState } from 'react';
import { Link } from "react-router-dom";

import { ListLinkItem } from '../Response/Navbar/NavBarResponseInterface';
import SmallWhiteBoxDisplay from '../../Common/Components/Elements/SmallWhiteBoxDisplay';

export default function NavbarListItem(props: {
    header: string,
    icon: string;
    listLinks: ListLinkItem[];
    createNewText: string|null;
    flagAddNewModal?: (show: boolean) => void;
    errors?: string[];
    isOpen?: boolean;
    onToggle?: () => void;
}) {
    const heading: string = props.header;
    const icon: string = props.icon;
    const createNewText: string|null = props.createNewText;
    const dropdownItems: Array<ListLinkItem>|[] = props.listLinks;
    const flagAddNewModal: (show: boolean) => void|null = props.flagAddNewModal ?? null;

    // Support both controlled (accordion) and uncontrolled usage
    const isControlled = props.isOpen !== undefined;
    const [localOpen, setLocalOpen] = useState<boolean>(false);
    const isOpen = isControlled ? props.isOpen : localOpen;

    const handleToggle = (): void => {
        if (isControlled) {
            props.onToggle?.();
        } else {
            setLocalOpen(prev => !prev);
        }
    };

    const navItemDropdownToggleClass: string = isOpen ? 'show' : '';

    return (
        <li className="nav-item sidebar-nav-item" onClick={handleToggle}>
            <div
                className={`nav-link sidebar-nav-link ${isOpen ? 'active-nav' : 'collapsed'} hover`}
                data-toggle="collapse"
                aria-expanded={isOpen}
                aria-controls="collapseUtilities"
            >
                <i className={`fas fa-fw fa-${icon} sidebar-nav-icon`} />
                <span className="sidebar-nav-label">{ heading }</span>
                <i
                    className="fas fa-chevron-down sidebar-nav-chevron"
                    style={{
                        transform: isOpen ? 'rotate(180deg)' : 'rotate(0deg)',
                        transition: 'transform 0.25s ease',
                    }}
                />
            </div>
            <SmallWhiteBoxDisplay
                classes={navItemDropdownToggleClass}
                heading={heading}
            >
                <React.Fragment>
                    {
                        Array.isArray(dropdownItems) && dropdownItems.length > 0
                            ? dropdownItems.map((navListItem: ListLinkItem, index: number) => (
                            <Link
                                to={navListItem.link}
                                key={index}
                                className="collapse-item collapse-item-modern"
                                onClick={e => { e.stopPropagation(); if (isControlled) props.onToggle?.(); else setLocalOpen(false); }}
                            >
                                {navListItem.displayName}
                            </Link>
                        ))
                        : null
                    }
                    {
                        flagAddNewModal !== null
                            ? <span
                                className="collapse-item collapse-item-modern collapse-item-add hover"
                                onClick={e => { e.stopPropagation(); flagAddNewModal(true); if (isControlled) props.onToggle?.(); else setLocalOpen(false); }}
                              >
                                <i className="fas fa-plus-circle mr-2" style={{ fontSize: '0.75rem' }} />
                                { createNewText }
                              </span>
                            : null
                    }
                </React.Fragment>
            </SmallWhiteBoxDisplay>
        </li>
    );
}
