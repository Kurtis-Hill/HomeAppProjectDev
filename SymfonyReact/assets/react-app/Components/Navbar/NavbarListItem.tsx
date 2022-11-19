import * as React from 'react';
import { useState, useEffect } from 'react';
import { Link, useNavigate } from "react-router-dom";

import { NavbarListItemInterface, NavbarListInterface } from "./Interfaces/NavbarItemInterfaces";

export default function NavbarListItem(props: NavbarListInterface) {
    const heading: string = props.heading;
    const icon: string = props.icon;
    const createNewLink: string|null = props.createNewLink;
    const createNewText: string|null = props.createNewText;
    const dropdownItems: Array<NavbarListItemInterface> = props.listLinks;
    // const [dropdownItems, setDropdownItems] = useState<Array<NavbarListItemInterface>>(props.listLinks);
    
    const [navbarItemToggleDropdown, setNavbarItemToggleDropdown] = useState<boolean>(false)
    
    const toggleNavTabElement = (): void => {            
        setNavbarItemToggleDropdown(!navbarItemToggleDropdown);
    }
    
    const navItemDropdownToggleClass: string = navbarItemToggleDropdown === true ? 'show' : '';
    
    // useEffect(() => {
    //     console.log(props.dropDownItems, ',e', heading, objectsToList);
    //     setDropdownItems(props.dropDownItems);
    //   }, [props.dropDownItems]);

    return (
        <li className="nav-item" onClick={() => {toggleNavTabElement()}}>
            <div className="nav-link collapsed hover" data-toggle="collapse" aria-expanded="true" aria-controls="collapseUtilities">
                <i className={`fas fa-fw fa-${icon}`}/>
                <span>{ heading }</span>
            </div>
            <div className={`collapse ${navItemDropdownToggleClass}`} aria-labelledby="headingTwo">
                <div className="bg-white py-2 collapse-inner rounded">
                    <h6 className="collapse-header">View Room:</h6>
                    {
                        Array.isArray(dropdownItems) && dropdownItems.length > 0
                            ? dropdownItems.map((navListItem, index: number) => (
                            <Link to={navListItem.link} key={index} className="collapse-item">{navListItem.displayName}</Link>
                        ))
                            : null
                    }
                    <Link to={createNewLink} className="collapse-item">{createNewText}</Link>
                </div>
            </div>
        </li>
    );
}
