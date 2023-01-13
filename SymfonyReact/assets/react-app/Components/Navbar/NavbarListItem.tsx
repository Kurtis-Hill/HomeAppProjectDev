import * as React from 'react';
import { useState, useEffect } from 'react';
import { Link } from "react-router-dom";

import { NavbarListItemInterface, NavbarListInterface } from "./Interfaces/NavbarItemInterfaces";
import SmallWhiteBoxDisplay from '../../../OldApp/js/components/DisplayBoxes/SmallWhiteBoxDisplay';
import { ListLinkItem } from '../../Response/NavBar/NavBarResponseInterface';

export default function NavbarListItem(props: {
    header: string,
    icon: string;
    listLinks: ListLinkItem[];
    createNewText: string|null;
    flagAddNewModal?: (show: boolean) => void;
    errors?: string[];
}) {
    const heading: string = props.header;
    const icon: string = props.icon;
    // const createNewLink: string|null = props.createNewLink;
    const createNewText: string|null = props.createNewText;
    const dropdownItems: Array<ListLinkItem>|[] = props.listLinks;
    const flagAddNewModal: (show: boolean) => void|null = props.flagAddNewModal ?? null; 

    // console.log('shw add', addNewText)
    // useEffect(() => {

    // }, [showAddNewElement])
    // const showAddNewFlag:(show: boolean) => void|undefined = props.showAddNewFlag;
    // const showAddNewFlagText: string|null = props.showAddNewFlagText ?? null;
    // const addNewModal: React|null = props.newItemModal;

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
            >
                <React.Fragment>
                    {                        
                        Array.isArray(dropdownItems) && dropdownItems.length > 0
                            ? dropdownItems.map((navListItem: ListLinkItem, index: number) => (
                            <Link to={navListItem.link} key={index} className="collapse-item">{navListItem.displayName}</Link>
                        ))
                        : null                    
                    }

                    {
                        flagAddNewModal !== null
                            ? <span className="collapse-item hover" onClick={ () => flagAddNewModal(true) }>{ createNewText }</span>
                            : null
                    }
                </React.Fragment>
            </SmallWhiteBoxDisplay>
        </li>
    );
}
