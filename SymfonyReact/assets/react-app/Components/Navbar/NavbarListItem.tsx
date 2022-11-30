import * as React from 'react';
import { useState } from 'react';
import { Link } from "react-router-dom";

import { NavbarListItemInterface, NavbarListInterface } from "./Interfaces/NavbarItemInterfaces";
import SmallWhiteBoxDisplay from '../../../OldApp/js/components/DisplayBoxes/SmallWhiteBoxDisplay';

export default function NavbarListItem(props: NavbarListInterface) {
    const heading: string = props.heading;
    const icon: string = props.icon;
    const createNewLink: string|null = props.createNewLink;
    const createNewText: string|null = props.createNewText;
    const dropdownItems: Array<NavbarListItemInterface> = props.listLinks;

    const [navbarItemToggleDropdown, setNavbarItemToggleDropdown] = useState<boolean>(false)
    
    const toggleNavTabElement = (): void => {            
        setNavbarItemToggleDropdown(!navbarItemToggleDropdown);
    }
    
    const navItemDropdownToggleClass: string = navbarItemToggleDropdown === true ? 'show' : '';

    return (
        <li className="nav-item" onClick={() => {toggleNavTabElement()}}>
            <div className="nav-link collapsed hover" data-toggle="collapse" aria-expanded="true" aria-controls="collapseUtilities">
                <i className={`fas fa-fw fa-${icon}`}/>
                <span>{ heading }</span>
            </div>
            <SmallWhiteBoxDisplay
                classes={navItemDropdownToggleClass}
                heading={heading}
                content={
                    <React.Fragment>
                        {                        
                            Array.isArray(dropdownItems) && dropdownItems.length > 0
                                ? dropdownItems.map((navListItem, index: number) => (
                                <Link to={navListItem.link} key={index} className="collapse-item">{navListItem.displayName}</Link>
                            ))
                            : null                    
                        }
                        <Link to={createNewLink} className="collapse-item">{createNewText}</Link>
                    </React.Fragment>
                }

            />
        </li>
    );
}
